@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <div class="md:col-span-2">
        <label class="block font-semibold text-sm mb-2">
            CV Document Name
        </label>
        <input type="text" name="title"
            value="{{ old('title', $cv->title ?? 'My Professional CV') }}"
            class="w-full rounded-lg border-gray-300"
            required>
        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <h3 class="font-bold text-lg border-b pb-3">
            Personal Information
        </h3>
    </div>

    <div>
        <label class="block font-semibold text-sm mb-2">Full Name</label>
        <input type="text" name="full_name"
            value="{{ old('full_name', $cv->full_name ?? '') }}"
            placeholder="Enter your full name"
            class="w-full rounded-lg border-gray-300" required>
        @error('full_name') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-semibold text-sm mb-2">
            Professional Title
        </label>
        <input type="text" name="professional_title"
            value="{{ old('professional_title', $cv->professional_title ?? '') }}"
            placeholder="e.g. Frontend Developer"
            class="w-full rounded-lg border-gray-300">
    </div>

    <div>
        <label class="block font-semibold text-sm mb-2">Email Address</label>
        <input type="email" name="email"
            value="{{ old('email', $cv->email ?? auth()->user()->email) }}"
            class="w-full rounded-lg border-gray-300" required>
        @error('email') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-semibold text-sm mb-2">Phone Number</label>
        <input type="text" name="phone"
            value="{{ old('phone', $cv->phone ?? '') }}"
            placeholder="+234..."
            class="w-full rounded-lg border-gray-300" required>
        @error('phone') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block font-semibold text-sm mb-2">Address</label>
        <textarea name="address" rows="2"
            class="w-full rounded-lg border-gray-300"
            placeholder="Your residential address">{{ old('address', $cv->address ?? '') }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label class="block font-semibold text-sm mb-2">
            Professional Summary
        </label>
        <textarea name="summary" rows="5"
            class="w-full rounded-lg border-gray-300"
            placeholder="Write a short professional introduction...">{{ old('summary', $cv->summary ?? '') }}</textarea>
        @error('summary') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <h3 class="font-bold text-lg border-b pb-3 mt-3">
            Choose Your CV Design
        </h3>
    </div>

    <div class="md:col-span-2">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            @foreach([
                'professional' => 'Professional - Blue',
                'modern' => 'Modern - Black',
                'simple' => 'Simple - White'
            ] as $value => $label)

                <label class="border rounded-xl p-4 cursor-pointer hover:border-blue-500">
                    <input type="radio" name="template"
                        value="{{ $value }}"
                        {{ old('template', $cv->template ?? 'professional') === $value ? 'checked' : '' }}
                        required>

                    <span class="block font-bold text-sm mt-2">
                        {{ $label }}
                    </span>
                </label>

            @endforeach

        </div>
        @error('template') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
    </div>

</div>

<div class="flex gap-3 mt-8">
    <button type="submit"
        class="bg-blue-600 hover:bg-blue-700 text-white px-7 py-3 rounded-lg font-bold">
        Save CV
    </button>

    <a href="{{ route('cvs.index') }}"
        class="bg-gray-100 text-gray-700 px-7 py-3 rounded-lg">
        Cancel
    </a>
</div>
