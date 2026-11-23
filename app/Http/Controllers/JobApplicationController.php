<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobApplicationController extends Controller
{
    private const STATUSES = [
        'Applied',
        'Interview',
        'Rejected',
        'Offer',
    ];

    public function index(Request $request)
    {
        $jobs = $request->user()
            ->jobApplications()
            ->latest('date_applied')
            ->paginate(10);

        return view('jobs.index', compact('jobs'));
    }

    public function create()
    {
        return view('jobs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company' => 'required|string|max:150',
            'role' => 'required|string|max:150',
            'date_applied' => 'required|date',
            'status' => [
                'required',
                Rule::in(self::STATUSES),
            ],
            'link' => 'nullable|url|max:2048',
            'notes' => 'nullable|string|max:3000',
        ]);

        $request->user()
            ->jobApplications()
            ->create($validated);

        return redirect()
            ->route('jobs.index')
            ->with('success', 'Job application added successfully!');
    }

    public function edit(Request $request, JobApplication $job)
    {
        $this->authorizeOwner($request, $job);

        return view('jobs.edit', compact('job'));
    }

    public function update(
        Request $request,
        JobApplication $job
    ) {
        $this->authorizeOwner($request, $job);

        $validated = $request->validate([
            'company' => 'required|string|max:150',
            'role' => 'required|string|max:150',
            'date_applied' => 'required|date',
            'status' => [
                'required',
                Rule::in(self::STATUSES),
            ],
            'link' => 'nullable|url|max:2048',
            'notes' => 'nullable|string|max:3000',
        ]);

        $job->update($validated);

        return redirect()
            ->route('jobs.index')
            ->with('success', 'Job application updated!');
    }

    public function destroy(
        Request $request,
        JobApplication $job
    ) {
        $this->authorizeOwner($request, $job);

        $job->delete();

        return redirect()
            ->route('jobs.index')
            ->with('success', 'Job application deleted.');
    }

    private function authorizeOwner(
        Request $request,
        JobApplication $job
    ): void {
        abort_unless(
            $job->user_id === $request->user()->id,
            403
        );
    }
}
