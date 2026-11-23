<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CvController extends Controller
{
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
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'full_name' => 'required|string|max:150',
            'professional_title' => 'nullable|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'address' => 'nullable|string|max:1000',
            'summary' => 'nullable|string|max:3000',
            'template' => [
                'required',
                Rule::in(['professional', 'modern', 'simple']),
            ],
        ]);

        $request->user()->cvs()->create($validated);

        return redirect()
            ->route('cvs.index')
            ->with('success', 'Your CV has been created successfully!');
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

        return view('cv.edit', compact('cv'));
    }

    public function update(Request $request, Cv $cv)
    {
        $this->authorizeOwner($request, $cv);

        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'full_name' => 'required|string|max:150',
            'professional_title' => 'nullable|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'address' => 'nullable|string|max:1000',
            'summary' => 'nullable|string|max:3000',
            'template' => [
                'required',
                Rule::in(['professional', 'modern', 'simple']),
            ],
        ]);

        $cv->update($validated);

        return redirect()
            ->route('cvs.index')
            ->with('success', 'CV updated successfully!');
    }

    public function destroy(Request $request, Cv $cv)
    {
        $this->authorizeOwner($request, $cv);

        $cv->delete();

        return redirect()
            ->route('cvs.index')
            ->with('success', 'CV deleted successfully.');
    }

    private function authorizeOwner(Request $request, Cv $cv): void
    {
        abort_unless(
            $cv->user_id === $request->user()->id,
            403
        );
    }
}
