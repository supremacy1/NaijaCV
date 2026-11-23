<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

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

                // Save the selected CV design.
                'template' => $validated['template'],

                // Payment information.
                'is_paid' => false,
                'payment_status' => $isFreeTemplate ? 'free' : 'unpaid',

                // Guest access information.
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
     * Download the free guest CV as PDF.
     */
    public function download(string $token)
    {
        $cv = Cv::where('guest_token', $token)
            ->whereNull('user_id')
            ->where('expires_at', '>', now())
            ->with(['educations', 'experiences', 'skills'])
            ->firstOrFail();

        // Only unpaid, free-template CVs can use this download route.
        if ($cv->payment_status !== 'free' || $cv->is_paid) {
            abort(403, 'This CV is not available for free download.');
        }

        $pdf = Pdf::loadView('guest-cvs.pdf', compact('cv'))
            ->setPaper('a4', 'portrait');

        $filename = Str::slug($cv->full_name) . '-cv.pdf';

        return $pdf->download($filename);
    }
}
