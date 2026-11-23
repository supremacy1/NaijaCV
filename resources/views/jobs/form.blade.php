@csrf

<div class="space-y-5">

    <div>
        <label class="block font-semibold mb-2">Company Name</label>
        <input type="text" name="company"
            value="{{ old('company', $job->company ?? '') }}"
            placeholder="e.g. Access Bank"
            class="w-full rounded-lg border-gray-300" required>
        @error('company') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-semibold mb-2">Job Position</label>
        <input type="text" name="role"
            value="{{ old('role', $job->role ?? '') }}"
            placeholder="e.g. Software Developer"
            class="w-full rounded-lg border-gray-300" required>
        @error('role') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-semibold mb-2">Date Applied</label>
        <input type="date" name="date_applied"
            value="{{ old('date_applied', isset($job) ? $job->date_applied->format('Y-m-d') : date('Y-m-d')) }}"
            class="w-full rounded-lg border-gray-300" required>
        @error('date_applied') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-semibold mb-2">Application Status</label>
        <select name="status" class="w-full rounded-lg border-gray-300" required>
            @foreach(['Applied', 'Interview', 'Rejected', 'Offer'] as $status)
                <option value="{{ $status }}"
                    {{ old('status', $job->status ?? 'Applied') === $status ? 'selected' : '' }}>
                    {{ $status }}
                </option>
            @endforeach
        </select>
        @error('status') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-semibold mb-2">
            Job Application Link (Optional)
        </label>
        <input type="url" name="link"
            value="{{ old('link', $job->link ?? '') }}"
            placeholder="https://company.com/careers"
            class="w-full rounded-lg border-gray-300">
        @error('link') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-semibold mb-2">Notes</label>
        <textarea name="notes" rows="4"
            placeholder="Add interview dates, contact details or other notes..."
            class="w-full rounded-lg border-gray-300">{{ old('notes', $job->notes ?? '') }}</textarea>
        @error('notes') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
    </div>

    <div class="flex gap-3">
        <button type="submit"
            class="bg-blue-600 text-white px-7 py-3 rounded-lg font-bold">
            Save Application
        </button>

        <a href="{{ route('jobs.index') }}"
            class="bg-gray-100 px-7 py-3 rounded-lg">
            Cancel
        </a>
    </div>

</div>
