<!DOCTYPE html>

<html lang="en">

<head>


    <meta charset="UTF-8">


    <meta name="viewport" content="width=device-width, initial-scale=1.0">



    <title>Create Your CV | NaijaCV</title>




    <link rel="preconnect" href="https://fonts.googleapis.com">


    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>


    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">



    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>



<body class="guest-cv-page bg-gray-50 text-gray-800">



    <div class="min-h-screen">



        <!-- HEADER -->

        <header class="bg-white border-b">

            <div class="max-w-6xl mx-auto px-5 py-5 flex justify-between items-center">



                <a href="{{ route('home') }}"

                    class="guest-brand text-2xl font-extrabold text-blue-700">

                    Naija<span>CV</span>.

                </a>



                <div class="flex items-center gap-4 text-sm">

                    <a href="{{ route('login') }}"

                        class="text-gray-600 hover:text-blue-600">

                        Login

                    </a>



                    <a href="{{ route('register') }}"

                        class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold">

                        Create Account

                    </a>

                </div>



            </div>

        </header>





        <main class="max-w-5xl mx-auto px-4 py-10">



            <!-- PAGE TITLE -->

            <div class="mb-8">



                <a href="{{ route('home') }}"

                    class="text-sm text-blue-600 hover:underline">

                    ← Back to Home

                </a>



                <h1 class="text-3xl md:text-4xl font-extrabold mt-5">

                    Build Your CV

                </h1>



                <p class="text-gray-500 mt-2">

                    Complete the form below to create your CV.

                    No registration is required.

                </p>



                <div class="flex flex-wrap gap-3 mt-5">



                    <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-semibold">

                        Free CV Creation

                    </span>



                    <span class="bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-semibold">

                        No Account Required

                    </span>



                </div>



            </div>





            @if ($errors->any())

            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-5 mb-6">



                <h3 class="font-bold mb-2">

                    Please correct the following errors:

                </h3>



                <ul class="list-disc pl-5 text-sm space-y-1">

                    @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                    @endforeach

                </ul>



            </div>

            @endif





            <form

                action="{{ route('guest-cvs.store') }}"

                method="POST"

                enctype="multipart/form-data"
                x-data="guestCvForm()"

                class="space-y-7">



                @csrf

                <!-- DOCUMENT INFORMATION -->

                <section class="bg-white border rounded-2xl shadow-sm p-6 md:p-8">



                    <div class="mb-6">

                        <h2 class="text-xl font-bold">

                            1. CV Document

                        </h2>



                        <p class="text-sm text-gray-500 mt-1">

                            Give your CV a name so you can identify it.

                        </p>

                    </div>



                    <label class="block text-sm font-semibold mb-2">

                        CV Document Name

                    </label>



                    <input

                        type="text"

                        name="title"

                        value="{{ old('title', 'My Professional CV') }}"

                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"

                        required>



                    @error('title')

                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>

                    @enderror



                </section>


                <!-- CV TEMPLATE SELECTION -->
                <section class="bg-white border rounded-2xl shadow-sm p-6 md:p-8"
                    x-data="templateChooser()">

                    <div class="mb-6">
                        <h2 class="text-xl font-bold">2. Choose Your CV Package</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Select a free CV or browse our premium professional designs.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- FREE PACKAGE -->
                        <label class="cursor-pointer">
                            <input type="radio"
                                name="package_choice"
                                value="simple"
                                class="peer sr-only"
                                x-model="selectedPackage">

                            <div class="border-2 rounded-xl p-5 h-full
                        peer-checked:border-green-600
                        peer-checked:bg-green-50
                        hover:border-green-400 transition">

                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="font-bold text-lg">Free CV</h3>
                                    <span class="text-green-700 font-extrabold">FREE</span>
                                </div>

                                <div class="h-48 rounded-lg bg-gray-100 p-4">
                                    <div class="bg-white shadow-sm h-full p-4">
                                        <div class="h-4 w-2/3 bg-green-700 mb-3"></div>
                                        <div class="h-2 w-1/2 bg-gray-300 mb-5"></div>
                                        <div class="h-2 w-full bg-gray-200 mb-2"></div>
                                        <div class="h-2 w-5/6 bg-gray-200 mb-5"></div>
                                        <div class="h-2 w-1/3 bg-green-700 mb-2"></div>
                                        <div class="h-2 w-full bg-gray-200 mb-2"></div>
                                        <div class="h-2 w-4/5 bg-gray-200"></div>
                                    </div>
                                </div>

                                <p class="text-sm text-gray-600 mt-4">
                                    Clean and professional basic CV layout.
                                </p>

                                <p class="text-sm text-green-700 font-semibold mt-3">
                                    ✓ Free PDF download
                                </p>
                            </div>
                        </label>

                        <!-- PAID PACKAGE -->
                        <div class="border-2 rounded-xl p-5"
                            :class="selectedPackage === 'paid'
                ? 'border-blue-600 bg-blue-50'
                : 'border-gray-200'">

                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-bold text-lg">Premium CV</h3>
                                <span class="text-blue-700 font-extrabold">₦3,000</span>
                            </div>

                            <p class="text-sm text-gray-600 mb-4">
                                Choose from ten professionally designed CV layouts.
                            </p>

                            <!-- CURRENT PREMIUM TEMPLATE -->
                            <div class="bg-white rounded-lg border overflow-hidden">

                                <div class="h-48 flex"
                                    :style="'background:' + currentTemplate.soft">

                                    <!-- Miniature sidebar preview -->
                                    <div class="w-1/3 p-3"
                                        :style="'background:' + currentTemplate.colour">
                                        <div class="w-12 h-12 rounded-full bg-white mx-auto mb-4"></div>
                                        <div class="h-2 bg-white opacity-80 mb-3"></div>
                                        <div class="h-2 bg-white opacity-60 mb-2"></div>
                                        <div class="h-2 bg-white opacity-60 mb-2"></div>
                                        <div class="h-2 bg-white opacity-60"></div>
                                    </div>

                                    <div class="flex-1 p-4">
                                        <div class="h-3 w-4/5 mb-3 rounded"
                                            :style="'background:' + currentTemplate.colour"></div>
                                        <div class="h-2 w-1/2 bg-gray-300 mb-5"></div>
                                        <div class="h-2 w-2/5 mb-2"
                                            :style="'background:' + currentTemplate.colour"></div>
                                        <div class="h-2 w-full bg-gray-200 mb-2"></div>
                                        <div class="h-2 w-5/6 bg-gray-200 mb-4"></div>
                                        <div class="h-2 w-1/2 mb-2"
                                            :style="'background:' + currentTemplate.colour"></div>
                                        <div class="h-2 w-full bg-gray-200"></div>
                                    </div>
                                </div>

                                <div class="p-4">
                                    <div class="flex justify-between items-center gap-2">
                                        <h4 class="font-bold" x-text="currentTemplate.name"></h4>
                                        <span class="text-xs text-blue-700 font-bold"
                                            x-text="(templateIndex + 1) + ' / 10'"></span>
                                    </div>

                                    <p class="text-xs text-gray-500 mt-2"
                                        x-text="currentTemplate.desc"></p>

                                    <p class="text-xs font-semibold mt-3 text-blue-700"
                                        x-text="currentTemplate.layout"></p>
                                </div>
                            </div>

                            <!-- SLIDER CONTROLS -->
                            <div class="flex items-center justify-between mt-4 gap-3">

                                <button type="button"
                                    @click="previousTemplate()"
                                    class="px-4 py-2 border rounded-lg hover:bg-gray-100">
                                    ← Previous
                                </button>

                                <div class="flex gap-1.5">
                                    <template x-for="(item, index) in premiumTemplates"
                                        :key="item.id">
                                        <button type="button"
                                            @click="templateIndex = index; selectedPackage = 'paid'"
                                            class="w-2.5 h-2.5 rounded-full"
                                            :class="templateIndex === index
                                    ? 'bg-blue-700'
                                    : 'bg-gray-300'"
                                            :aria-label="'View ' + item.name">
                                        </button>
                                    </template>
                                </div>

                                <button type="button"
                                    @click="nextTemplate()"
                                    class="px-4 py-2 border rounded-lg hover:bg-gray-100">
                                    Next →
                                </button>
                            </div>

                            <button type="button"
                                @click="selectedPackage = 'paid'"
                                class="w-full mt-4 bg-blue-700 hover:bg-blue-800
                           text-white font-bold py-3 rounded-lg">
                                Select This Premium Design
                            </button>
                        </div>
                    </div>

                    <!-- This preserves the existing backend field name -->
                    <input type="hidden"
                        name="template"
                        :value="selectedPackage === 'simple'
                ? 'simple'
                : currentTemplate.id">

                    @error('template')
                    <p class="text-red-500 text-sm mt-3">{{ $message }}</p>
                    @enderror

                    <p class="text-xs text-gray-500 mt-5">
                        Premium payment is ₦3,000 once. It unlocks the selected premium
                        design and its PDF download.
                    </p>
                </section>

                <script>
                    function templateChooser() {
                        return {
                            selectedPackage: @js(old('template', 'simple')) === 'simple' ?
                                'simple' : 'paid',

                            templateIndex: Math.max(0, [
                                'corporate_blue',
                                'executive_gold',
                                'modern_green',
                                'burgundy',
                                'creative_purple',
                                'minimalist_grey',
                                'elegant_teal',
                                'modern_orange',
                                'premium_navy',
                                'classic_luxury'
                            ].indexOf(@js(old('template', 'corporate_blue')))),

                            premiumTemplates: [{
                                    id: 'corporate_blue',
                                    name: 'Corporate Blue',
                                    desc: 'Professional corporate CV with a left sidebar.',
                                    layout: 'Two-column sidebar layout',
                                    colour: '#173B70',
                                    soft: '#EAF1FB'
                                },

                                {
                                    id: 'executive_gold',
                                    name: 'Executive Gold',
                                    desc: 'Elegant executive CV with a refined presentation.',
                                    layout: 'Executive header and structured sections',
                                    colour: '#252525',
                                    soft: '#F4EBD6'
                                },

                                {
                                    id: 'modern_green',
                                    name: 'Modern Green',
                                    desc: 'Modern CV with a green information sidebar.',
                                    layout: 'Modern two-column layout',
                                    colour: '#176B48',
                                    soft: '#E7F4EC'
                                },

                                {
                                    id: 'burgundy',
                                    name: 'Burgundy',
                                    desc: 'Distinctive professional design with strong section styling.',
                                    layout: 'Left accent rail and clean content area',
                                    colour: '#76263D',
                                    soft: '#F6E8EC'
                                },

                                {
                                    id: 'creative_purple',
                                    name: 'Creative Purple',
                                    desc: 'Creative layout with a distinctive profile header.',
                                    layout: 'Asymmetric header and two-column content',
                                    colour: '#6841A5',
                                    soft: '#F0EAF9'
                                },

                                {
                                    id: 'minimalist_grey',
                                    name: 'Minimalist Grey',
                                    desc: 'Simple, elegant and spacious professional design.',
                                    layout: 'Minimal single-column layout',
                                    colour: '#4B5563',
                                    soft: '#EEF0F2'
                                },

                                {
                                    id: 'elegant_teal',
                                    name: 'Elegant Teal',
                                    desc: 'Professional layout with a dedicated contact sidebar.',
                                    layout: 'Right-side information panel',
                                    colour: '#087E83',
                                    soft: '#E5F4F4'
                                },

                                {
                                    id: 'modern_orange',
                                    name: 'Modern Orange',
                                    desc: 'Contemporary design with a bold profile introduction.',
                                    layout: 'Split header and modular content sections',
                                    colour: '#C65A19',
                                    soft: '#FFF0E6'
                                },

                                {
                                    id: 'premium_navy',
                                    name: 'Premium Navy',
                                    desc: 'Formal CV with a strong career-history presentation.',
                                    layout: 'Timeline-based experience and education',
                                    colour: '#172B4D',
                                    soft: '#E9EDF4'
                                },

                                {
                                    id: 'classic_luxury',
                                    name: 'Classic Luxury',
                                    desc: 'Traditional and elegant CV with a refined sidebar.',
                                    layout: 'Classic two-column editorial layout',
                                    colour: '#76563C',
                                    soft: '#F3ECE4'
                                }
                            ],

                            get currentTemplate() {
                                return this.premiumTemplates[this.templateIndex];
                            },

                            previousTemplate() {
                                this.templateIndex =
                                    (this.templateIndex - 1 + this.premiumTemplates.length) %
                                    this.premiumTemplates.length;
                                this.selectedPackage = 'paid';
                            },

                            nextTemplate() {
                                this.templateIndex =
                                    (this.templateIndex + 1) % this.premiumTemplates.length;
                                this.selectedPackage = 'paid';
                            }
                        };
                    }
                </script>


                <!-- PERSONAL INFORMATION -->

                <section class="bg-white border rounded-2xl shadow-sm p-6 md:p-8">



                    <div class="mb-6">

                        <h2 class="text-xl font-bold">

                            3. Personal Information

                        </h2>



                        <p class="text-sm text-gray-500 mt-1">

                            Enter your correct contact and professional details.

                        </p>

                    </div>



                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">



                        <div class="md:col-span-2">

                            <label class="block text-sm font-semibold mb-2">

                                Full Name \*

                            </label>



                            <input

                                type="text"

                                name="full_name"

                                value="{{ old('full_name') }}"

                                placeholder="e.g. Andrew Ogbu"

                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"

                                required>



                            @error('full_name')

                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>

                            @enderror

                        </div>





                        <div>

                            <label class="block text-sm font-semibold mb-2">

                                Professional Title

                            </label>



                            <input

                                type="text"

                                name="professional_title"

                                value="{{ old('professional_title') }}"

                                placeholder="e.g. Web Developer"

                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">



                            @error('professional_title')

                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>

                            @enderror

                        </div>





                        <div>

                            <label class="block text-sm font-semibold mb-2">

                                Email Address \*

                            </label>



                            <input

                                type="email"

                                name="email"

                                value="{{ old('email') }}"

                                placeholder="example\@email.com"

                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"

                                required>



                            @error('email')

                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>

                            @enderror

                        </div>





                        <div>

                            <label class="block text-sm font-semibold mb-2">

                                Phone Number \*

                            </label>



                            <input

                                type="tel"

                                name="phone"

                                value="{{ old('phone') }}"

                                placeholder="+234 800 000 0000"

                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"

                                required>



                            @error('phone')

                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>

                            @enderror

                        </div>





                        <div>

                            <label class="block text-sm font-semibold mb-2">

                                Residential Address

                            </label>



                            <input

                                type="text"

                                name="address"

                                value="{{ old('address') }}"

                                placeholder="City, State, Nigeria"

                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">



                            @error('address')

                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>

                            @enderror

                        </div>



                    </div>



                </section>





                <!-- OPTIONAL PROFILE PHOTO -->
                <section class="bg-white border rounded-2xl shadow-sm p-6 md:p-8">
                    <div class="mb-4">
                        <h2 class="text-xl font-bold">Optional Profile Photograph</h2>
                        <p class="text-sm text-gray-500 mt-1">You may upload a professional headshot to include in your CV. This is optional.</p>
                    </div>
                    <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="block w-full rounded-lg border border-gray-300 p-3 text-sm">
                    <p class="text-xs text-gray-500 mt-2">JPG, PNG or WebP only. Maximum file size: 2MB. You can leave this blank.</p>
                    @error('photo')<p class="text-red-500 text-sm mt-2">{{ $message }}</p>@enderror
                </section>

                <!-- PROFESSIONAL SUMMARY -->

                <section class="bg-white border rounded-2xl shadow-sm p-6 md:p-8">



                    <div class="mb-5">

                        <h2 class="text-xl font-bold">

                            4. Professional Summary

                        </h2>



                        <p class="text-sm text-gray-500 mt-1">

                            Briefly introduce yourself, your experience and your career goals.

                        </p>

                    </div>



                    <textarea

                        name="summary"

                        rows="5"

                        maxlength="3000"

                        placeholder="Write a short professional introduction..."

                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('summary') }}</textarea>



                    @error('summary')

                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>

                    @enderror



                </section>





                <!-- EDUCATION -->

                <section class="bg-white border rounded-2xl shadow-sm p-6 md:p-8">



                    <div class="flex flex-wrap justify-between items-center gap-3 mb-6">



                        <div>

                            <h2 class="text-xl font-bold">

                                5. Educational Background

                            </h2>



                            <p class="text-sm text-gray-500 mt-1">

                                Add your schools, qualifications and courses.

                            </p>

                        </div>



                        <button

                            type="button"

                            @click="addEducation()"

                            class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-100">

                            + Add Education

                        </button>



                    </div>





                    <template x-for="(education, index) in educations" :key="index">



                        <div class="border rounded-xl p-5 mb-4 relative">



                            <div class="flex justify-between items-center mb-5">



                                <h3 class="font-bold text-gray-700">

                                    Education <span x-text="index + 1"></span>

                                </h3>



                                <button

                                    type="button"

                                    @click="removeEducation(index)"

                                    x-show="educations.length > 1"

                                    class="text-red-500 text-sm font-semibold">

                                    Remove

                                </button>



                            </div>





                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">



                                <div>

                                    <label class="block text-sm font-semibold mb-2">

                                        Institution

                                    </label>



                                    <input

                                        type="text"

                                        :name="'educations[' + index + '][institution]'"

                                        x-model="education.institution"

                                        placeholder="School or university"

                                        class="w-full rounded-lg border-gray-300">

                                </div>





                                <div>

                                    <label class="block text-sm font-semibold mb-2">

                                        Qualification

                                    </label>



                                    <input

                                        type="text"

                                        :name="'educations[' + index + '][qualification]'"

                                        x-model="education.qualification"

                                        placeholder="e.g. B.Sc, HND, SSCE"

                                        class="w-full rounded-lg border-gray-300">

                                </div>





                                <div>

                                    <label class="block text-sm font-semibold mb-2">

                                        Field of Study

                                    </label>



                                    <input

                                        type="text"

                                        :name="'educations[' + index + '][field_of_study]'"

                                        x-model="education.field_of_study"

                                        placeholder="e.g. Computer Science"

                                        class="w-full rounded-lg border-gray-300">

                                </div>





                                <div class="grid grid-cols-2 gap-3">



                                    <div>

                                        <label class="block text-sm font-semibold mb-2">

                                            Start Year

                                        </label>



                                        <input

                                            type="text"

                                            :name="'educations[' + index + '][start_year]'"

                                            x-model="education.start_year"

                                            placeholder="2018"

                                            class="w-full rounded-lg border-gray-300">

                                    </div>



                                    <div>

                                        <label class="block text-sm font-semibold mb-2">

                                            End Year

                                        </label>



                                        <input

                                            type="text"

                                            :name="'educations[' + index + '][end_year]'"

                                            x-model="education.end_year"

                                            placeholder="2022"

                                            class="w-full rounded-lg border-gray-300">

                                    </div>



                                </div>





                                <div class="md:col-span-2">



                                    <label class="block text-sm font-semibold mb-2">

                                        Additional Details

                                    </label>



                                    <textarea

                                        :name="'educations[' + index + '][description]'"

                                        x-model="education.description"

                                        rows="2"

                                        placeholder="Optional information about your education"

                                        class="w-full rounded-lg border-gray-300"></textarea>



                                </div>



                            </div>



                        </div>



                    </template>



                    <p class="text-xs text-gray-500">

                        You can leave unused education entries blank.

                    </p>



                </section>





                <!-- WORK EXPERIENCE -->

                <section class="bg-white border rounded-2xl shadow-sm p-6 md:p-8">



                    <div class="flex flex-wrap justify-between items-center gap-3 mb-6">



                        <div>

                            <h2 class="text-xl font-bold">

                                6. Work Experience

                            </h2>



                            <p class="text-sm text-gray-500 mt-1">

                                Include your previous and current employment.

                            </p>

                        </div>



                        <button

                            type="button"

                            @click="addExperience()"

                            class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-100">

                            + Add Experience

                        </button>



                    </div>





                    <template x-for="(experience, index) in experiences" :key="index">



                        <div class="border rounded-xl p-5 mb-4">



                            <div class="flex justify-between items-center mb-5">



                                <h3 class="font-bold text-gray-700">

                                    Experience <span x-text="index + 1"></span>

                                </h3>



                                <button

                                    type="button"

                                    @click="removeExperience(index)"

                                    x-show="experiences.length > 1"

                                    class="text-red-500 text-sm font-semibold">

                                    Remove

                                </button>



                            </div>





                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">



                                <div>

                                    <label class="block text-sm font-semibold mb-2">

                                        Company / Organisation

                                    </label>



                                    <input

                                        type="text"

                                        :name="'experiences[' + index + '][company]'"

                                        x-model="experience.company"

                                        placeholder="Company name"

                                        class="w-full rounded-lg border-gray-300">

                                </div>





                                <div>

                                    <label class="block text-sm font-semibold mb-2">

                                        Job Position

                                    </label>



                                    <input

                                        type="text"

                                        :name="'experiences[' + index + '][job_title]'"

                                        x-model="experience.job_title"

                                        placeholder="e.g. Software Developer"

                                        class="w-full rounded-lg border-gray-300">

                                </div>





                                <div>

                                    <label class="block text-sm font-semibold mb-2">

                                        Start Date

                                    </label>



                                    <input

                                        type="date"

                                        :name="'experiences[' + index + '][start_date]'"

                                        x-model="experience.start_date"

                                        class="w-full rounded-lg border-gray-300">

                                </div>





                                <div>

                                    <label class="block text-sm font-semibold mb-2">

                                        End Date

                                    </label>



                                    <input

                                        type="date"

                                        :name="'experiences[' + index + '][end_date]'"

                                        x-model="experience.end_date"

                                        :disabled="experience.currently_working"

                                        class="w-full rounded-lg border-gray-300 disabled:bg-gray-100">

                                </div>





                                <div class="md:col-span-2">



                                    <label class="flex items-center gap-2 text-sm font-medium">



                                        <input

                                            type="checkbox"

                                            :name="'experiences[' + index + '][currently_working]'"

                                            value="1"

                                            x-model="experience.currently_working"

                                            class="rounded border-gray-300 text-blue-600">



                                        I currently work here



                                    </label>



                                </div>





                                <div class="md:col-span-2">



                                    <label class="block text-sm font-semibold mb-2">

                                        Responsibilities / Achievements

                                    </label>



                                    <textarea

                                        :name="'experiences[' + index + '][duties]'"

                                        x-model="experience.duties"

                                        rows="4"

                                        placeholder="Describe your responsibilities and achievements..."

                                        class="w-full rounded-lg border-gray-300"></textarea>



                                </div>



                            </div>



                        </div>



                    </template>



                    <p class="text-xs text-gray-500">

                        You can leave unused work experience entries blank.

                    </p>



                </section>





                <!-- SKILLS -->

                <section class="bg-white border rounded-2xl shadow-sm p-6 md:p-8">



                    <div class="flex flex-wrap justify-between items-center gap-3 mb-6">



                        <div>

                            <h2 class="text-xl font-bold">

                                7. Skills

                            </h2>



                            <p class="text-sm text-gray-500 mt-1">

                                Add your technical and professional skills.

                            </p>

                        </div>



                        <button

                            type="button"

                            @click="addSkill()"

                            class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-100">

                            + Add Skill

                        </button>



                    </div>





                    <template x-for="(skill, index) in skills" :key="index">



                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 items-end">



                            <div>

                                <label class="block text-sm font-semibold mb-2">

                                    Skill Name

                                </label>



                                <input

                                    type="text"

                                    :name="'skills[' + index + '][name]'"

                                    x-model="skill.name"

                                    placeholder="e.g. HTML, Communication, Leadership"

                                    class="w-full rounded-lg border-gray-300">

                            </div>





                            <div class="flex gap-2">



                                <div class="flex-1">

                                    <label class="block text-sm font-semibold mb-2">

                                        Proficiency

                                    </label>



                                    <select

                                        :name="'skills[' + index + '][proficiency]'"

                                        x-model="skill.proficiency"

                                        class="w-full rounded-lg border-gray-300">

                                        <option value="">Select Level</option>

                                        <option value="Beginner">Beginner</option>

                                        <option value="Intermediate">Intermediate</option>

                                        <option value="Advanced">Advanced</option>

                                        <option value="Expert">Expert</option>

                                    </select>

                                </div>



                                <button

                                    type="button"

                                    @click="removeSkill(index)"

                                    x-show="skills.length > 1"

                                    class="text-red-500 text-sm font-semibold px-2">

                                    Remove

                                </button>



                            </div>



                        </div>



                    </template>



                </section>





                <!-- TEMPLATE INFORMATION -->

                <section class="bg-blue-50 border border-blue-100 rounded-2xl p-6">



                    <h2 class="text-lg font-bold text-blue-900">

                        Your Selected CV Design

                    </h2>



                    <p class="text-blue-800 text-sm mt-2">

                        You can preview your selected design after completing this form.

                        Basic CV downloads are free. Premium CV downloads cost ₦3,000.

                    </p>



                </section>



                <!-- SUBMIT -->

                <div class="flex flex-col sm:flex-row gap-4">



                    <button

                        type="submit"

                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center font-bold py-4 px-6 rounded-xl shadow-sm">

                        Create My CV →

                    </button>



                    <a

                        href="{{ route('home') }}"

                        class="text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-4 px-6 rounded-xl">

                        Cancel

                    </a>



                </div>



                <p class="text-center text-xs text-gray-500 pb-5">

                    By creating your CV, you agree to provide accurate information.

                </p>




            </form>



        </main>





        <footer class="border-t bg-white py-6 text-center text-sm text-gray-500">

            © {{ date('Y') }} NaijaCV. Build your CV. Get hired. Move forward.

        </footer>



    </div>


    <script>
        function guestCvForm() {
            return {
                educations: (@js(old('educations')) || [{
                    institution: '',
                    qualification: '',
                    field_of_study: '',
                    start_year: '',
                    end_year: '',
                    description: ''
                }]),

                experiences: (@js(old('experiences')) || [{
                    company: '',
                    job_title: '',
                    start_date: '',
                    end_date: '',
                    currently_working: false,
                    duties: ''
                }]),

                skills: (@js(old('skills')) || [{
                    name: '',
                    proficiency: ''
                }]),

                addEducation() {
                    if (this.educations.length >= 10) return;

                    this.educations.push({
                        institution: '',
                        qualification: '',
                        field_of_study: '',
                        start_year: '',
                        end_year: '',
                        description: ''
                    });
                },

                removeEducation(index) {
                    this.educations.splice(index, 1);
                },

                addExperience() {
                    if (this.experiences.length >= 10) return;

                    this.experiences.push({
                        company: '',
                        job_title: '',
                        start_date: '',
                        end_date: '',
                        currently_working: false,
                        duties: ''
                    });
                },

                removeExperience(index) {
                    this.experiences.splice(index, 1);
                },

                addSkill() {
                    if (this.skills.length >= 30) return;

                    this.skills.push({
                        name: '',
                        proficiency: ''
                    });
                },

                removeSkill(index) {
                    this.skills.splice(index, 1);
                }
            };
        }
    </script>




</body>

</html>