<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class GuestCvController extends Controller
{
    /**
     * Display the public guest CV form.
     */
    public function create()
    {
        return view('guest-cvs.create');
    }

    /**
     * Save a guest CV and its related information.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'template' => [
                'required',
                Rule::in(['simple', 'professional', 'executive'])
            ],

            'full_name' => 'required|string|max:150',
            'professional_title' => 'nullable|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'address' => 'nullable|string|max:1000',
            'summary' => 'nullable|string|max:3000',

            'educations' => 'nullable|array|max:10',
            'educations.*.institution' => 'nullable|string|max:200',
            'educations.*.qualification' => 'nullable|string|max:200',
            'educations.*.field_of_study' => 'nullable|string|max:200',
            'educations.*.start_year' => 'nullable|string|max:10',
            'educations.*.end_year' => 'nullable|string|max:10',
            'educations.*.description' => 'nullable|string|max:1500',

            'experiences' => 'nullable|array|max:10',
            'experiences.*.company' => 'nullable|string|max:200',
            'experiences.*.job_title' => 'nullable|string|max:200',
            'experiences.*.start_date' => 'nullable|date',
            'experiences.*.end_date' => 'nullable|date',
            'experiences.*.currently_working' => 'nullable|boolean',
            'experiences.*.duties' => 'nullable|string|max:2000',

            'skills' => 'nullable|array|max:30',
            'skills.*.name' => 'nullable|string|max:100',
            'skills.*.proficiency' => 'nullable|string|max:50',
        ]);

        // Keep only completed education entries.
        $educations = collect($validated['educations'] ?? [])
            ->filter(function ($education) {
                return !empty($education['institution'])
                    && !empty($education['qualification']);
            })
            ->map(function ($education) {
                return [
                    'institution' => $education['institution'],
                    'qualification' => $education['qualification'],
                    'field_of_study' => $education['field_of_study'] ?? null,
                    'start_year' => $education['start_year'] ?? null,
                    'end_year' => $education['end_year'] ?? null,
                    'description' => $education['description'] ?? null,
                ];
            })
            ->values()
            ->all();

        // Keep only completed experience entries.
        $experiences = collect($validated['experiences'] ?? [])
            ->filter(function ($experience) {
                return !empty($experience['company'])
                    && !empty($experience['job_title']);
            })
            ->map(function ($experience) {
                $currentlyWorking = !empty($experience['currently_working']);

                return [
                    'company' => $experience['company'],
                    'job_title' => $experience['job_title'],
                    'start_date' => $experience['start_date'] ?? null,
                    'end_date' => $currentlyWorking
                        ? null
                        : ($experience['end_date'] ?? null),
                    'currently_working' => $currentlyWorking,
                    'duties' => $experience['duties'] ?? null,
                ];
            })
            ->values()
            ->all();

        // Keep only skills with a name.
        $skills = collect($validated['skills'] ?? [])
            ->filter(fn ($skill) => !empty($skill['name']))
            ->map(function ($skill) {
                return [
                    'name' => $skill['name'],
                    'proficiency' => $skill['proficiency'] ?? null,
                ];
            })
            ->values()
            ->all();

        $cv = DB::transaction(function () use (
            $validated,
            $educations,
            $experiences,
            $skills
        ) {
            $isFreeTemplate = $validated['template'] === 'simple';

            $cv = Cv::create([
                'user_id' => null,

                'title' => $validated['title'],
                'full_name' => $validated['full_name'],
                'professional_title' => $validated['professional_title'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? null,
                'summary' => $validated['summary'] ?? null,

                'template' => $validated['template'],

                'is_paid' => false,
                'payment_status' => $isFreeTemplate ? 'free' : 'unpaid',

                'guest_token' => (string) Str::uuid(),
                'expires_at' => now()->addDays(7),
            ]);

            if (!empty($educations)) {
                $cv->educations()->createMany($educations);
            }

            if (!empty($experiences)) {
                $cv->experiences()->createMany($experiences);
            }

            if (!empty($skills)) {
                $cv->skills()->createMany($skills);
            }

            return $cv;
        });

        return redirect()->route('guest-cvs.preview', [
            'token' => $cv->guest_token,
        ]);
    }

    /**
     * Initialize Paystack payment.
     * No login required.
     */
    public function pay(string $token)
    {
        $cv = Cv::where('guest_token', $token)
            ->whereNull('user_id')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        if (!in_array($cv->template, ['professional', 'executive'])) {
            abort(403, 'This CV does not require payment.');
        }

        // If already paid, download immediately.
        if ($cv->payment_status === 'paid' && $cv->is_paid) {
            return redirect()->route('guest-cvs.download', [
                'token' => $cv->guest_token,
            ]);
        }

        if (empty($cv->email)) {
            return back()->with(
                'error',
                'Please provide an email address before making payment.'
            );
        }

        $secretKey = config('services.paystack.secret_key');

        if (!$secretKey) {
            return back()->with(
                'error',
                'Payment is not configured yet. Please try again later.'
            );
        }

        $reference = 'NAIJACV' . strtoupper(Str::random(24));

        $cv->update([
            'payment_status' => 'unpaid',
            'payment_reference' => $reference,
        ]);

        try {
            $response = Http::withToken($secretKey)
                ->acceptJson()
                ->timeout(30)
                ->post('https://api.paystack.co/transaction/initialize', [
                    'email' => $cv->email,
                    'amount' => 300000,
                    'currency' => 'NGN',
                    'reference' => $reference,

                    'callback_url' => route(
                        'guest-cvs.payment.callback',
                        ['token' => $cv->guest_token]
                    ),

                    'metadata' => [
                        'cv_id' => $cv->id,
                        'cv_template' => $cv->template,
                        'product' => 'NaijaCV Premium CV',
                    ],
                ]);

            if (!$response->successful() || !$response->json('status')) {
                $cv->update([
                    'payment_reference' => null,
                ]);

                return back()->with(
                    'error',
                    $response->json('message') ?? 'Unable to initialize payment. Please try again.'
                );
            }

            $authorizationUrl = $response->json('data.authorization_url');

            if (!$authorizationUrl) {
                $cv->update([
                    'payment_reference' => null,
                ]);

                return back()->with(
                    'error',
                    'Paystack did not return a checkout link. Please try again.'
                );
            }

            return redirect()->away($authorizationUrl);

        } catch (\Throwable $e) {
            report($e);

            $cv->update([
                'payment_reference' => null,
            ]);

            return back()->with(
                'error',
                'Payment could not be started. Please try again.'
            );
        }
    }

    /**
     * Verify Paystack payment and automatically download CV.
     * No login required.
     */
    public function paymentCallback(Request $request, string $token)
    {
        $cv = Cv::where('guest_token', $token)
            ->whereNull('user_id')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        // Already paid? Download again without charging.
        if ($cv->payment_status === 'paid' && $cv->is_paid) {
            return redirect()->route('guest-cvs.download', [
                'token' => $cv->guest_token,
            ]);
        }

        $reference = $request->query('reference')
            ?? $request->query('trxref');

        if (
            !$reference ||
            !$cv->payment_reference ||
            !hash_equals($cv->payment_reference, $reference)
        ) {
            return redirect()
                ->route('guest-cvs.preview', ['token' => $token])
                ->with('error', 'Payment reference could not be confirmed.');
        }

        $secretKey = config('services.paystack.secret_key');

        if (!$secretKey) {
            return redirect()
                ->route('guest-cvs.preview', ['token' => $token])
                ->with('error', 'Payment verification is currently unavailable.');
        }

        try {
            // Verify transaction directly with Paystack.
            $response = Http::withToken($secretKey)
                ->acceptJson()
                ->timeout(30)
                ->get(
                    'https://api.paystack.co/transaction/verify/' . urlencode($reference)
                );

            if (!$response->successful() || !$response->json('status')) {
                return redirect()
                    ->route('guest-cvs.preview', ['token' => $token])
                    ->with('error', 'We could not verify your payment. Please try again.');
            }

            $payment = $response->json('data');

            // Confirm amount, currency, reference and transaction status.
            $verified =
                ($payment['status'] ?? null) === 'success' &&
                (int) ($payment['amount'] ?? 0) === 300000 &&
                ($payment['currency'] ?? null) === 'NGN' &&
                ($payment['reference'] ?? null) === $cv->payment_reference;

            if (!$verified) {
                return redirect()
                    ->route('guest-cvs.preview', ['token' => $token])
                    ->with('error', 'Payment was not successful or could not be verified.');
            }

            // Mark CV as paid only after Paystack verification.
            $cv->update([
                'is_paid' => true,
                'payment_status' => 'paid',
                'payment_verified_at' => now(),
            ]);

            // Automatically download the premium CV.
            return redirect()->route('guest-cvs.download', [
                'token' => $cv->guest_token,
            ]);

        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('guest-cvs.preview', ['token' => $token])
                ->with('error', 'An error occurred while verifying your payment. If you were charged, please contact support.');
        }
    }

    /**
     * Display the guest CV preview.
     */
    public function preview(string $token)
    {
        $cv = Cv::where('guest_token', $token)
            ->whereNull('user_id')
            ->where('expires_at', '>', now())
            ->with(['educations', 'experiences', 'skills'])
            ->firstOrFail();

        return view('guest-cvs.preview', compact('cv'));
    }

    /**
     * Download free or successfully paid premium CV as PDF.
     */
    public function download(string $token)
    {
        $cv = Cv::where('guest_token', $token)
            ->whereNull('user_id')
            ->where('expires_at', '>', now())
            ->with(['educations', 'experiences', 'skills'])
            ->firstOrFail();

        $isFree =
            $cv->template === 'simple' &&
            $cv->payment_status === 'free' &&
            !$cv->is_paid;

        $isPremiumPaid =
            in_array($cv->template, ['professional', 'executive']) &&
            $cv->payment_status === 'paid' &&
            $cv->is_paid;

        // Block all unpaid premium downloads.
        if (!$isFree && !$isPremiumPaid) {
            abort(403, 'Payment is required before downloading this CV.');
        }

        $pdf = Pdf::loadView('guest-cvs.pdf', compact('cv'))
            ->setPaper('a4', 'portrait');

        $filename = Str::slug($cv->full_name) . '-cv.pdf';

        return $pdf->download($filename);
    }
}
