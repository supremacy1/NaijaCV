<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

        $isFree = $validated['template'] === 'simple';

        DB::transaction(function () use ($request, $validated, $photoPath, $isFree) {
            $cv = $request->user()->cvs()->create([
                'title' => $validated['title'],
                'full_name' => $validated['full_name'],
                'professional_title' => $validated['professional_title'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? null,
                'summary' => $validated['summary'] ?? null,
                'template' => $validated['template'],
                'photo_path' => $photoPath,
                'is_paid' => false,
                'payment_status' => $isFree ? 'free' : 'unpaid',
                'guest_token' => null,
                'expires_at' => null,
            ]);

            $this->saveSections($cv, $validated);
        });
$cv = $request->user()->cvs()
    ->latest()
    ->first();

return redirect()->route('cvs.show', $cv)
    ->with('success', 'Your CV preview is ready. Review it before downloading.');
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