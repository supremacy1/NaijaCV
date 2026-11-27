<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class CvController extends Controller
{
    private function premiumTemplates(): array
    {
        return [
            'corporate_blue',
            'executive_gold',
            'modern_green',
            'burgundy',
            'creative_purple',
            'minimalist_grey',
            'elegant_teal',
            'modern_orange',
            'premium_navy',
            'classic_luxury',
        ];
    }

    private function allowedTemplates(): array
    {
        return array_merge(
            ['simple'],
            $this->premiumTemplates(),
            // Retain old identifiers for existing CV records.
            ['professional', 'modern', 'executive']
        );
    }

    private function validateCv(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'full_name' => ['required', 'string', 'max:150'],
            'professional_title' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'summary' => ['nullable', 'string', 'max:3000'],

            'template' => [
                'required',
                Rule::in($this->allowedTemplates()),
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'educations' => ['nullable', 'array', 'max:10'],
            'educations.*.institution' => ['nullable', 'string', 'max:255'],
            'educations.*.qualification' => ['nullable', 'string', 'max:255'],
            'educations.*.field_of_study' => ['nullable', 'string', 'max:255'],
            'educations.*.start_year' => ['nullable', 'string', 'max:20'],
            'educations.*.end_year' => ['nullable', 'string', 'max:20'],
            'educations.*.description' => ['nullable', 'string', 'max:2000'],

            'experiences' => ['nullable', 'array', 'max:10'],
            'experiences.*.company' => ['nullable', 'string', 'max:255'],
            'experiences.*.job_title' => ['nullable', 'string', 'max:255'],
            'experiences.*.start_date' => ['nullable', 'string', 'max:50'],
            'experiences.*.end_date' => ['nullable', 'string', 'max:50'],
            'experiences.*.currently_working' => ['nullable', 'boolean'],
            'experiences.*.duties' => ['nullable', 'string', 'max:3000'],

            'skills' => ['nullable', 'array', 'max:30'],
            'skills.*.name' => ['nullable', 'string', 'max:100'],
            'skills.*.proficiency' => ['nullable', 'string', 'max:100'],
        ]);
    }

    private function saveSections(Cv $cv, array $data): void
    {
        $educations = collect($data['educations'] ?? [])
            ->filter(fn ($item) =>
                !empty($item['institution']) ||
                !empty($item['qualification']) ||
                !empty($item['field_of_study'])
            )
            ->map(fn ($item) => [
                'institution' => $item['institution'] ?? null,
                'qualification' => $item['qualification'] ?? null,
                'field_of_study' => $item['field_of_study'] ?? null,
                'start_year' => $item['start_year'] ?? null,
                'end_year' => $item['end_year'] ?? null,
                'description' => $item['description'] ?? null,
            ])->values()->all();

        $experiences = collect($data['experiences'] ?? [])
            ->filter(fn ($item) =>
                !empty($item['company']) ||
                !empty($item['job_title'])
            )
            ->map(fn ($item) => [
                'company' => $item['company'] ?? null,
                'job_title' => $item['job_title'] ?? null,
                'start_date' => $item['start_date'] ?? null,
                'end_date' => !empty($item['currently_working'])
                    ? null
                    : ($item['end_date'] ?? null),
                'currently_working' => !empty($item['currently_working']),
                'duties' => $item['duties'] ?? null,
            ])->values()->all();

        $skills = collect($data['skills'] ?? [])
            ->filter(fn ($item) => !empty($item['name']))
            ->map(fn ($item) => [
                'name' => $item['name'],
                'proficiency' => $item['proficiency'] ?? null,
            ])->values()->all();

        $cv->educations()->delete();
        $cv->experiences()->delete();
        $cv->skills()->delete();

        if ($educations) {
            $cv->educations()->createMany($educations);
        }

        if ($experiences) {
            $cv->experiences()->createMany($experiences);
        }

        if ($skills) {
            $cv->skills()->createMany($skills);
        }
    }

    private function persistCv(Request $request, array $data, ?string $photoPath): Cv
    {
        $isFree = $data['template'] === 'simple';

        return DB::transaction(function () use ($request, $data, $photoPath, $isFree) {
            $cv = $request->user()->cvs()->create([
                'title' => $data['title'],
                'full_name' => $data['full_name'],
                'professional_title' => $data['professional_title'] ?? null,
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'] ?? null,
                'summary' => $data['summary'] ?? null,
                'template' => $data['template'],
                'photo_path' => $photoPath,
                'is_paid' => false,
                'payment_status' => $isFree ? 'free' : 'unpaid',
                'guest_token' => null,
                'expires_at' => null,
            ]);

            $this->saveSections($cv, $data);

            return $cv;
        });
    }

    private function getDraft(Request $request, string $draft): array
    {
        abort_unless(Str::isUuid($draft), 404);

        $data = $request->session()->get(
            "cv_drafts.{$request->user()->id}.{$draft}"
        );

        abort_unless(is_array($data), 404, 'This CV preview has expired.');

        return $data;
    }

    private function makeDraftCv(array $data, string $template): Cv
    {
        $cv = new Cv([
            'title' => $data['title'],
            'full_name' => $data['full_name'],
            'professional_title' => $data['professional_title'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'summary' => $data['summary'] ?? null,
            'template' => $template,
            'is_paid' => false,
            'payment_status' => $template === 'simple' ? 'free' : 'unpaid',
        ]);

        $sectionFilters = [
            'educations' => fn ($item) =>
                !empty($item['institution']) ||
                !empty($item['qualification']) ||
                !empty($item['field_of_study']),
            'experiences' => fn ($item) =>
                !empty($item['company']) ||
                !empty($item['job_title']),
            'skills' => fn ($item) => !empty($item['name']),
        ];

        foreach ($sectionFilters as $section => $filter) {
            $cv->setRelation(
                $section,
                collect($data[$section] ?? [])
                    ->filter($filter)
                    ->map(fn ($item) => (object) $item)
            );
        }

        return $cv;
    }

    private function authorizeOwner(Request $request, Cv $cv): void
    {
        abort_unless(
            (int) $cv->user_id === (int) $request->user()->id,
            403
        );
    }

    private function isPaid(Cv $cv): bool
    {
        return $cv->is_paid && $cv->payment_status === 'paid';
    }

    public function index(Request $request)
    {
        $cvs = $request->user()->cvs()
            ->withCount(['educations', 'experiences', 'skills'])
            ->latest()
            ->paginate(10);

        return view('cv.index', compact('cvs'));
    }

    public function create()
    {
        return view('cv.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateCv($request);

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('cv-photos', 'public')
            : null;

        $cv = $this->persistCv($request, $validated, $photoPath);

        return redirect()->route('cvs.show', $cv)
            ->with('success', 'Your CV preview is ready. Review it before downloading.');
    }

    public function previewDraft(Request $request)
    {
        $draftInput = $request->validate([
            'draft_id' => ['nullable', 'uuid'],
        ]);
        $validated = $this->validateCv($request);
        $draftId = $draftInput['draft_id'] ?? (string) Str::uuid();
        $draftData = !empty($draftInput['draft_id'])
            ? $this->getDraft($request, $draftId)
            : [];
        $photoPath = $draftData['photo_path'] ?? null;

        if ($request->hasFile('photo')) {
            $newPhotoPath = $request->file('photo')
                ->store("cv-drafts/{$request->user()->id}", 'local');

            if (!$newPhotoPath) {
                throw new \RuntimeException('Unable to stage the CV photo for preview.');
            }

            if ($photoPath) {
                Storage::disk('local')->delete($photoPath);
            }

            $photoPath = $newPhotoPath;
        }

        unset($validated['photo']);
        $draftData = array_merge($validated, ['photo_path' => $photoPath]);

        $request->session()->put(
            "cv_drafts.{$request->user()->id}.{$draftId}",
            $draftData
        );

        return redirect()->route('cvs.preview', ['draft' => $draftId]);
    }

    public function showDraftPreview(Request $request, string $draft)
    {
        $data = $this->getDraft($request, $draft);
        $selection = $request->validate([
            'template' => ['sometimes', 'string', Rule::in($this->allowedTemplates())],
        ]);
        $previewTemplate = $selection['template'] ?? $data['template'];
        $cv = $this->makeDraftCv($data, $previewTemplate);
        $isDraft = true;
        $draftId = $draft;
        $previewPhotoUrl = null;
        $stagedPhoto = $data['photo_path'] ?? null;

        if ($stagedPhoto && Storage::disk('local')->exists($stagedPhoto)) {
            $mimeType = Storage::disk('local')->mimeType($stagedPhoto) ?: 'image/jpeg';
            $previewPhotoUrl = 'data:' . $mimeType . ';base64,' .
                base64_encode(Storage::disk('local')->get($stagedPhoto));
        }

        return view('cv.show', compact(
            'cv',
            'isDraft',
            'draftId',
            'previewTemplate',
            'previewPhotoUrl'
        ));
    }

    public function editDraft(Request $request, string $draft)
    {
        $data = $this->getDraft($request, $draft);
        $selection = $request->validate([
            'template' => ['required', 'string', Rule::in($this->allowedTemplates())],
        ]);
        $data['template'] = $selection['template'];
        unset($data['photo_path']);
        $data['draft_id'] = $draft;

        return redirect()
            ->route('cvs.create')
            ->withInput($data);
    }

    public function saveDraft(Request $request, string $draft)
    {
        $data = $this->getDraft($request, $draft);
        $selection = $request->validate([
            'template' => ['required', 'string', Rule::in($this->allowedTemplates())],
        ]);
        $cv = $this->persistDraft($request, $draft, $data, $selection['template']);

        return redirect()->route('cvs.show', $cv)
            ->with('success', 'Your CV has been saved.');
    }

    public function payDraft(Request $request, string $draft)
    {
        $data = $this->getDraft($request, $draft);
        $selection = $request->validate([
            'template' => ['required', 'string', Rule::in($this->premiumTemplates())],
        ]);
        $cv = $this->persistDraft($request, $draft, $data, $selection['template']);

        return $this->initializePayment($cv);
    }

    private function persistDraft(Request $request, string $draft, array $data, string $template): Cv
    {
        $data['template'] = $template;
        $stagedPhoto = $data['photo_path'] ?? null;
        $photoPath = null;

        if ($stagedPhoto) {
            abort_unless(Storage::disk('local')->exists($stagedPhoto), 410, 'The staged photo has expired. Please upload it again.');

            $photoPath = 'cv-photos/' . Str::uuid() . '.' . pathinfo($stagedPhoto, PATHINFO_EXTENSION);
            $stored = Storage::disk('public')->put(
                $photoPath,
                Storage::disk('local')->get($stagedPhoto)
            );

            if (!$stored) {
                throw new \RuntimeException('Unable to save the CV photo.');
            }
        }

        try {
            $cv = $this->persistCv($request, $data, $photoPath);
        } catch (\Throwable $exception) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            throw $exception;
        }

        $request->session()->forget("cv_drafts.{$request->user()->id}.{$draft}");

        if ($stagedPhoto) {
            Storage::disk('local')->delete($stagedPhoto);
        }

        return $cv;
    }

    public function downloadDraft(Request $request, string $draft)
    {
        $data = $this->getDraft($request, $draft);
        $selection = $request->validate([
            'template' => ['required', 'string', Rule::in($this->allowedTemplates())],
        ]);
        abort_unless(
            $selection['template'] === 'simple',
            403,
            'Premium templates must be paid for before downloading.'
        );

        $cv = $this->makeDraftCv($data, 'simple');
        $stagedPhoto = $data['photo_path'] ?? null;
        $draftPhotoPath = $stagedPhoto && Storage::disk('local')->exists($stagedPhoto)
            ? Storage::disk('local')->path($stagedPhoto)
            : null;
        $pdf = Pdf::loadView('guest-cvs.pdf', compact('cv', 'draftPhotoPath'))
            ->setPaper('a4', 'portrait');
        $filename = Str::slug($cv->full_name) . '-cv.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function show(Request $request, Cv $cv)
    {
        $this->authorizeOwner($request, $cv);

        $cv->load(['educations', 'experiences', 'skills']);

        return view('cv.show', compact('cv'));
    }

    public function edit(Request $request, Cv $cv)
    {
        $this->authorizeOwner($request, $cv);

        if ($this->isPaid($cv)) {
            return redirect()
                ->route('cvs.show', $cv)
                ->with('error', 'This CV is locked because payment has been completed.');
        }

        $cv->load(['educations', 'experiences', 'skills']);

        return view('cv.edit', compact('cv'));
    }

    public function update(Request $request, Cv $cv)
    {
        $this->authorizeOwner($request, $cv);

        abort_if(
            $this->isPaid($cv),
            403,
            'This CV cannot be edited after payment.'
        );

        $validated = $this->validateCv($request);

        $oldPhotoPath = $cv->photo_path;
        $newPhotoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('cv-photos', 'public')
            : $oldPhotoPath;

        $isFree = $validated['template'] === 'simple';

        DB::transaction(function () use ($cv, $validated, $newPhotoPath, $isFree) {
            $cv->update([
                'title' => $validated['title'],
                'full_name' => $validated['full_name'],
                'professional_title' => $validated['professional_title'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? null,
                'summary' => $validated['summary'] ?? null,
                'template' => $validated['template'],
                'photo_path' => $newPhotoPath,
                'is_paid' => false,
                'payment_status' => $isFree ? 'free' : 'unpaid',
                'payment_reference' => null,
                'payment_verified_at' => null,
            ]);

            $this->saveSections($cv, $validated);
        });

        if ($oldPhotoPath && $oldPhotoPath !== $newPhotoPath) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        return redirect()
            ->route('cvs.index')
            ->with('success', 'Your CV has been updated successfully.');
    }

    public function download(Request $request, Cv $cv)
    {
        $this->authorizeOwner($request, $cv);

        $cv->load(['educations', 'experiences', 'skills']);

        $isFree = $cv->template === 'simple'
            && !$cv->is_paid
            && in_array($cv->payment_status, ['free', null, ''], true);

        abort_unless(
            $isFree || $this->isPaid($cv),
            403,
            'Please complete payment before downloading this premium CV.'
        );

        $pdf = Pdf::loadView('guest-cvs.pdf', compact('cv'))
            ->setPaper('a4', 'portrait');

        $filename = str($cv->full_name)->slug() . '-cv.pdf';

        return $pdf->download($filename);
    }

    public function pay(Request $request, Cv $cv)
    {
        $this->authorizeOwner($request, $cv);

        if ($this->isPaid($cv)) {
            return redirect()->route('cvs.download', $cv);
        }

        abort_unless(
            in_array($cv->template, $this->premiumTemplates(), true),
            403,
            'This CV does not require payment.'
        );

        return $this->initializePayment($cv);
    }

    public function paymentCallback(Request $request, Cv $cv)
    {
        $this->authorizeOwner($request, $cv);

        if ($this->isPaid($cv)) {
            return redirect()->route('cvs.download', $cv);
        }

        $reference = $request->query('reference') ?? $request->query('trxref');

        if (!$reference || !$cv->payment_reference || !hash_equals($cv->payment_reference, $reference)) {
            return redirect()->route('cvs.show', $cv)
                ->with('error', 'Payment reference could not be confirmed.');
        }

        $secretKey = config('services.paystack.secret_key');

        if (!$secretKey) {
            return redirect()->route('cvs.show', $cv)
                ->with('error', 'Payment verification is currently unavailable.');
        }

        try {
            $response = Http::withToken($secretKey)
                ->acceptJson()
                ->timeout(30)
                ->get('https://api.paystack.co/transaction/verify/' . urlencode($reference));
        } catch (ConnectionException $exception) {
            report($exception);

            return redirect()->route('cvs.show', $cv)
                ->with('error', 'Payment verification is unavailable. If you were charged, please try again shortly.');
        }

        if (!$response->successful() || !$response->json('status')) {
            return redirect()->route('cvs.show', $cv)
                ->with('error', 'We could not verify your payment. Please try again.');
        }

        $payment = $response->json('data');
        $verified = ($payment['status'] ?? null) === 'success'
            && (int) ($payment['amount'] ?? 0) === 300000
            && ($payment['currency'] ?? null) === 'NGN'
            && ($payment['reference'] ?? null) === $cv->payment_reference;

        if (!$verified) {
            return redirect()->route('cvs.show', $cv)
                ->with('error', 'Payment was not successful or could not be verified.');
        }

        $cv->update([
            'is_paid' => true,
            'payment_status' => 'paid',
            'payment_verified_at' => now(),
        ]);

        return redirect()->route('cvs.download', $cv);
    }

    private function initializePayment(Cv $cv)
    {
        if (!$cv->email) {
            return redirect()->route('cvs.show', $cv)
                ->with('error', 'Please provide an email address before making payment.');
        }

        $secretKey = config('services.paystack.secret_key');

        if (!$secretKey) {
            return redirect()->route('cvs.show', $cv)
                ->with('error', 'Payment is not configured yet. Please try again later.');
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
                    'callback_url' => route('cvs.payment.callback', ['cv' => $cv]),
                    'metadata' => [
                        'cv_id' => $cv->id,
                        'cv_template' => $cv->template,
                        'product' => 'NaijaCV Premium CV',
                    ],
                ]);
        } catch (ConnectionException $exception) {
            report($exception);
            $cv->update(['payment_reference' => null]);

            return redirect()->route('cvs.show', $cv)
                ->with('error', 'Payment could not be started. Please try again.');
        }

        if (!$response->successful() || !$response->json('status')) {
            $cv->update(['payment_reference' => null]);

            return redirect()->route('cvs.show', $cv)
                ->with('error', $response->json('message') ?? 'Unable to initialize payment. Please try again.');
        }

        $authorizationUrl = $response->json('data.authorization_url');

        if (!$authorizationUrl) {
            $cv->update(['payment_reference' => null]);

            return redirect()->route('cvs.show', $cv)
                ->with('error', 'Paystack did not return a checkout link. Please try again.');
        }

        return redirect()->away($authorizationUrl);
    }

    public function destroy(Request $request, Cv $cv)
    {
        $this->authorizeOwner($request, $cv);

        if ($this->isPaid($cv)) {
            return back()->with(
                'error',
                'A paid CV cannot be deleted because its payment record must be preserved.'
            );
        }

        if ($cv->photo_path) {
            Storage::disk('public')->delete($cv->photo_path);
        }

        $cv->delete();

        return redirect()
            ->route('cvs.index')
            ->with('success', 'CV deleted successfully.');
    }
}