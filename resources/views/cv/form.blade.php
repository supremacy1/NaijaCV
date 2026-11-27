@php
    $currentTemplate = old('template', $cv->template ?? 'simple');

    $educationItems = old('educations', isset($cv)
        ? $cv->educations->map(fn ($item) => $item->only([
            'institution', 'qualification', 'field_of_study',
            'start_year', 'end_year', 'description'
        ]))->values()->all()
        : []);

    $experienceItems = old('experiences', isset($cv)
        ? $cv->experiences->map(fn ($item) => $item->only([
            'company', 'job_title', 'start_date', 'end_date',
            'currently_working', 'duties'
        ]))->values()->all()
        : []);

    $skillItems = old('skills', isset($cv)
        ? $cv->skills->map(fn ($item) => $item->only([
            'name', 'proficiency'
        ]))->values()->all()
        : []);
@endphp

@csrf

@if ($errors->any())
    <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4 text-red-700">
        <p class="font-bold mb-2">Please correct the following:</p>
        <ul class="list-disc pl-5 text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div
    x-data="cvBuilder(
        @js($educationItems),
        @js($experienceItems),
        @js($skillItems)
    )"
    class="space-y-10"
>
    {{-- BASIC INFORMATION --}}
    <section>
        <h2 class="text-xl font-bold text-gray-900 mb-5">1. Personal Information</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-semibold mb-2">CV Name / Title *</label>
                <input type="text" name="title"
                    value="{{ old('title', $cv->title ?? '') }}"
                    required maxlength="150"
                    placeholder="e.g. My Professional CV"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Full Name *</label>
                <input type="text" name="full_name"
                    value="{{ old('full_name', $cv->full_name ?? '') }}"
                    required maxlength="150"
                    placeholder="Your full name"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Professional Title</label>
                <input type="text" name="professional_title"
                    value="{{ old('professional_title', $cv->professional_title ?? '') }}"
                    maxlength="150"
                    placeholder="e.g. Software Developer"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Email Address *</label>
                <input type="email" name="email"
                    value="{{ old('email', $cv->email ?? auth()->user()->email) }}"
                    required maxlength="150"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Phone Number *</label>
                <input type="text" name="phone"
                    value="{{ old('phone', $cv->phone ?? '') }}"
                    required maxlength="30"
                    placeholder="+234..."
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Address</label>
                <input type="text" name="address"
                    value="{{ old('address', $cv->address ?? '') }}"
                    maxlength="1000"
                    placeholder="City, State, Country"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="mt-5">
            <label class="block text-sm font-semibold mb-2">Professional Summary</label>
            <textarea name="summary" rows="5" maxlength="3000"
                placeholder="Write a brief professional summary about yourself..."
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('summary', $cv->summary ?? '') }}</textarea>
        </div>

        <div class="mt-5">
            <label class="block text-sm font-semibold mb-2">Profile Photo (Optional)</label>

            @if(isset($cv) && $cv->photo_path)
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $cv->photo_path) }}"
                        alt="Current profile photo"
                        class="w-24 h-24 rounded-xl object-cover border">
                    <p class="text-xs text-gray-500 mt-1">Current photo. Upload another to replace it.</p>
                </div>
            @endif

            <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp"
                class="w-full rounded-lg border border-gray-300 p-3">
            <p class="text-xs text-gray-500 mt-2">JPG, PNG or WebP. Maximum size: 2MB.</p>
        </div>
    </section>

    <hr>

    {{-- TEMPLATE SELECTION --}}
    <section>
        <h2 class="text-xl font-bold text-gray-900 mb-2">2. Choose Your CV Design</h2>
        <p class="text-sm text-gray-500 mb-5">
            Choose the free design or unlock any premium design for ₦3,000.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="border-2 rounded-xl p-5 cursor-pointer
                {{ $currentTemplate === 'simple' ? 'border-green-600 bg-green-50' : 'border-gray-200' }}">
                <input type="radio" value="simple"
    x-model="template"
    class="text-green-600 focus:ring-green-500">
                <span class="ml-2 font-bold">FREE CV</span>
                <p class="text-sm text-gray-500 mt-2">Simple professional CV design.</p>
                <p class="text-green-700 font-bold mt-2">₦0</p>
            </label>

            <div class="border-2 rounded-xl p-5"
                :class="template !== 'simple' ? 'border-blue-600 bg-blue-50' : 'border-gray-200'">
                <div class="font-bold">PREMIUM CV COLLECTION</div>
                <p class="text-sm text-gray-500 mt-2">Choose any of the ten premium designs.</p>
                <p class="text-blue-700 font-bold mt-2">₦3,000 one-time payment</p>
            </div>
        </div>

        <div class="mt-5">
            <label class="block text-sm font-semibold mb-2">Select Premium Design</label>

            <select name="template" x-model="template"
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="simple">Free — Simple CV</option>
                <option value="corporate_blue">Corporate Blue</option>
                <option value="executive_gold">Executive Gold</option>
                <option value="modern_green">Modern Green</option>
                <option value="burgundy">Burgundy Professional</option>
                <option value="creative_purple">Creative Purple</option>
                <option value="minimalist_grey">Minimalist Grey</option>
                <option value="elegant_teal">Elegant Teal</option>
                <option value="modern_orange">Modern Orange</option>
                <option value="premium_navy">Premium Navy</option>
                <option value="classic_luxury">Classic Luxury</option>

                @if(in_array($currentTemplate, ['professional', 'modern', 'executive']))
                    <option value="{{ $currentTemplate }}">
                        Existing design — {{ ucfirst($currentTemplate) }}
                    </option>
                @endif
            </select>

            <p class="text-sm text-blue-700 mt-2"
                x-show="template !== 'simple'">
                This is a premium design. You can edit it before payment. Payment of ₦3,000 unlocks the PDF download and locks further editing.
            </p>
        </div>
    </section>

    <hr>

    {{-- EDUCATION --}}
    <section>
        <div class="flex items-center justify-between gap-3 mb-5">
            <div>
                <h2 class="text-xl font-bold">3. Education</h2>
                <p class="text-sm text-gray-500">Add your educational qualifications.</p>
            </div>
            <button type="button" @click="addEducation()"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold">
                + Add Education
            </button>
        </div>

        <template x-for="(item, index) in educations" :key="index">
            <div class="border rounded-xl p-5 mb-4 relative">
                <button type="button" @click="removeEducation(index)"
                    class="absolute top-3 right-3 text-red-600 text-sm">Remove</button>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Institution</label>
                        <input :name="`educations[${index}][institution]`"
                            x-model="item.institution" type="text"
                            class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Qualification</label>
                        <input :name="`educations[${index}][qualification]`"
                            x-model="item.qualification" type="text"
                            placeholder="e.g. B.Sc, HND, SSCE"
                            class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Field of Study</label>
                        <input :name="`educations[${index}][field_of_study]`"
                            x-model="item.field_of_study" type="text"
                            class="w-full rounded-lg border-gray-300">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Start Year</label>
                            <input :name="`educations[${index}][start_year]`"
                                x-model="item.start_year" type="text"
                                class="w-full rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">End Year</label>
                            <input :name="`educations[${index}][end_year]`"
                                x-model="item.end_year" type="text"
                                class="w-full rounded-lg border-gray-300">
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-semibold mb-1">Description</label>
                    <textarea :name="`educations[${index}][description]`"
                        x-model="item.description" rows="2"
                        class="w-full rounded-lg border-gray-300"></textarea>
                </div>
            </div>
        </template>

        <p x-show="educations.length === 0" class="text-sm text-gray-400">
            No education added yet.
        </p>
    </section>

    <hr>

    {{-- EXPERIENCE --}}
    <section>
        <div class="flex items-center justify-between gap-3 mb-5">
            <div>
                <h2 class="text-xl font-bold">4. Work Experience</h2>
                <p class="text-sm text-gray-500">Add your previous or current employment.</p>
            </div>
            <button type="button" @click="addExperience()"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold">
                + Add Experience
            </button>
        </div>

        <template x-for="(item, index) in experiences" :key="index">
            <div class="border rounded-xl p-5 mb-4 relative">
                <button type="button" @click="removeExperience(index)"
                    class="absolute top-3 right-3 text-red-600 text-sm">Remove</button>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Company</label>
                        <input :name="`experiences[${index}][company]`"
                            x-model="item.company" type="text"
                            class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Job Title</label>
                        <input :name="`experiences[${index}][job_title]`"
                            x-model="item.job_title" type="text"
                            class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Start Date</label>
                        <input :name="`experiences[${index}][start_date]`"
                            x-model="item.start_date" type="text"
                            placeholder="e.g. January 2022"
                            class="w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">End Date</label>
                        <input :name="`experiences[${index}][end_date]`"
                            x-model="item.end_date" type="text"
                            :disabled="item.currently_working"
                            class="w-full rounded-lg border-gray-300">
                    </div>
                </div>

                <label class="flex items-center gap-2 mt-4 text-sm">
                    <input type="checkbox"
                        :name="`experiences[${index}][currently_working]`"
                        value="1" x-model="item.currently_working"
                        class="rounded border-gray-300">
                    I currently work here
                </label>

                <div class="mt-4">
                    <label class="block text-sm font-semibold mb-1">Responsibilities / Duties</label>
                    <textarea :name="`experiences[${index}][duties]`"
                        x-model="item.duties" rows="3"
                        class="w-full rounded-lg border-gray-300"></textarea>
                </div>
            </div>
        </template>

        <p x-show="experiences.length === 0" class="text-sm text-gray-400">
            No work experience added yet.
        </p>
    </section>

    <hr>

    {{-- SKILLS --}}
    <section>
        <div class="flex items-center justify-between gap-3 mb-5">
            <div>
                <h2 class="text-xl font-bold">5. Skills</h2>
                <p class="text-sm text-gray-500">List your professional and technical skills.</p>
            </div>
            <button type="button" @click="addSkill()"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold">
                + Add Skill
            </button>
        </div>

        <template x-for="(item, index) in skills" :key="index">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 border rounded-xl p-4 mb-3">
                <input :name="`skills[${index}][name]`"
                    x-model="item.name" type="text"
                    placeholder="Skill name"
                    class="w-full rounded-lg border-gray-300">

                <div class="flex gap-2">
                    <select :name="`skills[${index}][proficiency]`"
                        x-model="item.proficiency"
                        class="flex-1 rounded-lg border-gray-300">
                        <option value="">Proficiency (optional)</option>
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Advanced">Advanced</option>
                        <option value="Expert">Expert</option>
                    </select>

                    <button type="button" @click="removeSkill(index)"
                        class="text-red-600 text-sm font-semibold">Remove</button>
                </div>
            </div>
        </template>

        <p x-show="skills.length === 0" class="text-sm text-gray-400">
            No skills added yet.
        </p>
    </section>

    <hr>

    <div class="flex flex-col sm:flex-row gap-3 justify-between items-center">
        <a href="{{ route('cvs.index') }}"
            class="text-gray-600 font-semibold">Cancel</a>

        <button type="submit"
            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold">
            {{ isset($cv) ? 'Save CV Changes' : 'Save My CV' }}
        </button>
    </div>
</div>

@once
<script>
    function cvBuilder(educationData = [], experienceData = [], skillData = []) {
        return {
            template: @js($currentTemplate),

            educations: educationData.map(item => ({
                institution: item.institution || '',
                qualification: item.qualification || '',
                field_of_study: item.field_of_study || '',
                start_year: item.start_year || '',
                end_year: item.end_year || '',
                description: item.description || ''
            })),

            experiences: experienceData.map(item => ({
                company: item.company || '',
                job_title: item.job_title || '',
                start_date: item.start_date || '',
                end_date: item.end_date || '',
                currently_working: Boolean(item.currently_working),
                duties: item.duties || ''
            })),

            skills: skillData.map(item => ({
                name: item.name || '',
                proficiency: item.proficiency || ''
            })),

            addEducation() {
                if (this.educations.length < 10) {
                    this.educations.push({
                        institution: '',
                        qualification: '',
                        field_of_study: '',
                        start_year: '',
                        end_year: '',
                        description: ''
                    });
                }
            },

            removeEducation(index) {
                this.educations.splice(index, 1);
            },

            addExperience() {
                if (this.experiences.length < 10) {
                    this.experiences.push({
                        company: '',
                        job_title: '',
                        start_date: '',
                        end_date: '',
                        currently_working: false,
                        duties: ''
                    });
                }
            },

            removeExperience(index) {
                this.experiences.splice(index, 1);
            },

            addSkill() {
                if (this.skills.length < 30) {
                    this.skills.push({
                        name: '',
                        proficiency: ''
                    });
                }
            },

            removeSkill(index) {
                this.skills.splice(index, 1);
            }
        };
    }
</script>
@endonce
