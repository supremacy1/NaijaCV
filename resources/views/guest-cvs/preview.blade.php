<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $cv->full_name }} | NaijaCV Preview</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $template = $cv->template ?? 'simple';

        $themes = [
            'simple' => ['#26734D', '#E8F3EC'],
            'corporate_blue' => ['#173B70', '#EAF1FB'],
            'executive_gold' => ['#252525', '#F4EBD6'],
            'modern_green' => ['#176B48', '#E7F4EC'],
            'burgundy' => ['#76263D', '#F6E8EC'],
            'creative_purple' => ['#6841A5', '#F0EAF9'],
            'minimalist_grey' => ['#4B5563', '#EEF0F2'],
            'elegant_teal' => ['#087E83', '#E5F4F4'],
            'modern_orange' => ['#C65A19', '#FFF0E6'],
            'premium_navy' => ['#172B4D', '#E9EDF4'],
            'classic_luxury' => ['#76563C', '#F3ECE4'],
            'professional' => ['#173B70', '#EAF1FB'],
            'executive' => ['#252525', '#F4EBD6'],
        ];

        [$accent, $soft] = $themes[$template] ?? $themes['simple'];

        $sidebarTemplates = [
            'corporate_blue',
            'modern_green',
            'elegant_teal',
            'classic_luxury',
        ];

        $hasSidebar = in_array($template, $sidebarTemplates, true);

        $premiumNames = [
            'corporate_blue' => 'Corporate Blue',
            'executive_gold' => 'Executive Gold',
            'modern_green' => 'Modern Green',
            'burgundy' => 'Burgundy',
            'creative_purple' => 'Creative Purple',
            'minimalist_grey' => 'Minimalist Grey',
            'elegant_teal' => 'Elegant Teal',
            'modern_orange' => 'Modern Orange',
            'premium_navy' => 'Premium Navy',
            'classic_luxury' => 'Classic Luxury',
            'professional' => 'Professional (Legacy)',
            'executive' => 'Executive (Legacy)',
        ];

        $isFree = $template === 'simple'
            && $cv->payment_status === 'free'
            && !$cv->is_paid;

        $isPaid = $cv->payment_status === 'paid' && $cv->is_paid;

        $templateLabel = $template === 'simple'
            ? 'Basic CV'
            : ($premiumNames[$template] ?? 'CV');

        $photo = $cv->photo_path
            && file_exists(public_path('storage/' . $cv->photo_path))
            ? asset('storage/' . $cv->photo_path)
            : null;
    @endphp

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #f1f3f5;
            font-family: Arial, Helvetica, sans-serif;
            color: #252525;
        }

        .cv-paper {
            width: 900px;
            min-height: 1100px;
            margin: 25px auto;
            background: white;
            box-shadow: 0 10px 35px #00000015;
            overflow: hidden;
            --accent: {{ $accent }};
            --soft: {{ $soft }};
        }

        .cv-header {
            padding: 42px 48px;
            background: var(--accent);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .cv-name {
            font-size: 30px;
            font-weight: 800;
            line-height: 1.2;
            margin: 0 0 8px;
        }

        .cv-role {
            font-size: 15px;
            margin-bottom: 14px;
        }

        .cv-contact {
            font-size: 12px;
            line-height: 1.8;
            overflow-wrap: anywhere;
        }

        .cv-photo {
            width: 112px;
            height: 112px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,.8);
        }

        .cv-header-photo {
            float: right;
            margin-left: 20px;
        }

        .cv-section {
            margin-bottom: 27px;
            break-inside: avoid;
        }

        .cv-section h2 {
            font-size: 13px;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            color: var(--accent);
            border-bottom: 2px solid var(--soft);
            padding-bottom: 8px;
            margin: 0 0 13px;
            font-weight: 800;
        }

        .cv-section p {
            font-size: 13px;
            line-height: 1.7;
            color: #374151;
            white-space: pre-line;
            margin: 0;
        }

        .cv-item {
            margin-bottom: 19px;
            break-inside: avoid;
        }

        .cv-item h3 {
            font-size: 14px;
            color: #1f2937;
            margin: 0 0 5px;
            font-weight: 700;
        }

        .cv-meta {
            color: #777;
            font-size: 11px;
            margin-bottom: 7px;
        }

        .cv-item p {
            font-size: 12px;
            line-height: 1.65;
            white-space: pre-line;
            color: #444;
        }

        .cv-body {
            padding: 38px 48px;
        }

        .cv-layout {
            display: grid;
            grid-template-columns: 265px 1fr;
            min-height: 1100px;
        }

        .cv-sidebar {
            padding: 32px 23px;
            background: var(--accent);
            color: white;
        }

        .cv-sidebar .cv-photo {
            display: block;
            margin: 0 auto 27px;
            width: 125px;
            height: 125px;
        }

        .cv-sidebar h2 {
            font-size: 13px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            border-bottom: 1px solid rgba(255,255,255,.55);
            padding-bottom: 8px;
            margin: 25px 0 12px;
            color: white;
        }

        .cv-sidebar .cv-contact {
            font-size: 11px;
            line-height: 1.9;
            overflow-wrap: anywhere;
        }

        .cv-sidebar .cv-skills {
            padding-left: 17px;
            margin: 0;
        }

        .cv-sidebar .cv-skills li {
            font-size: 11px;
            margin-bottom: 9px;
            line-height: 1.5;
        }

        .cv-main {
            padding: 38px 35px;
            min-width: 0;
        }

        .cv-skills {
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .cv-skills li {
            display: inline-block;
            background: var(--soft);
            color: var(--accent);
            border-radius: 4px;
            padding: 7px 10px;
            margin: 3px 4px 4px 0;
            font-size: 11px;
        }

        /* Corporate Blue */
        .template-corporate_blue .cv-sidebar {
            background: #E3E3E3;
            color: #252525;
            border-right: 1px solid #d1d5db;
        }

        .template-corporate_blue .cv-sidebar h2 {
            color: #173B70;
            border-color: #173B70;
        }

        .template-corporate_blue .cv-sidebar .cv-contact {
            color: #374151;
        }

        .template-corporate_blue .cv-sidebar .cv-photo {
            border-color: white;
        }

        /* Modern Green */
        .template-modern_green .cv-sidebar {
            background: #176B48;
        }

        .template-modern_green .cv-main {
            border-top: 8px solid #B8DCC5;
        }

        /* Elegant Teal */
        .template-elegant_teal .cv-layout {
            grid-template-columns: 1fr 250px;
        }

        .template-elegant_teal .cv-sidebar {
            grid-column: 2;
            grid-row: 1;
            background: #E5F4F4;
            color: #164D50;
        }

        .template-elegant_teal .cv-sidebar h2 {
            color: #087E83;
            border-color: #8FCAC8;
        }

        .template-elegant_teal .cv-sidebar .cv-contact {
            color: #164D50;
        }

        .template-elegant_teal .cv-sidebar .cv-photo {
            border-color: #087E83;
        }

        .template-elegant_teal .cv-main {
            grid-column: 1;
            grid-row: 1;
        }

        /* Classic Luxury */
        .template-classic_luxury .cv-sidebar {
            background: #F3ECE4;
            color: #4B3829;
            border-right: 5px solid #B89A70;
        }

        .template-classic_luxury .cv-sidebar h2 {
            color: #76563C;
            border-color: #B89A70;
        }

        .template-classic_luxury .cv-sidebar .cv-contact {
            color: #4B3829;
        }

        .template-classic_luxury .cv-sidebar .cv-photo {
            border-color: #B89A70;
        }

        .template-classic_luxury .cv-main {
            font-family: Georgia, serif;
        }

        /* Executive Gold */
        .template-executive_gold .cv-header {
            text-align: center;
            background: #24211D;
            border-bottom: 8px solid #B58A35;
        }

        .template-executive_gold .cv-header-photo {
            float: none;
            display: block;
            margin: 0 auto 15px;
            border-color: #B58A35;
        }

        .template-executive_gold .cv-section h2 {
            color: #A77C27;
            border-bottom: 1px solid #D8BC7B;
        }

        .template-executive_gold .cv-body {
            padding-left: 75px;
            padding-right: 75px;
        }

        /* Burgundy */
        .template-burgundy .cv-header {
            background: white;
            color: #252525;
            border-top: 14px solid #76263D;
            border-bottom: 1px solid #ddd;
        }

        .template-burgundy .cv-header .cv-role,
        .template-burgundy .cv-header .cv-contact {
            color: #555;
        }

        .template-burgundy .cv-header-photo {
            border-color: #76263D;
        }

        .template-burgundy .cv-body {
            border-left: 12px solid #F6E8EC;
        }

        /* Creative Purple */
        .template-creative_purple .cv-header {
            background: linear-gradient(120deg, #42246E, #8059B5);
            border-radius: 0 0 55px 0;
        }

        .template-creative_purple .cv-body {
            padding-top: 45px;
        }

        .template-creative_purple .cv-section h2 {
            border-left: 5px solid #6841A5;
            border-bottom: 1px solid #ddd;
            padding-left: 12px;
        }

        /* Minimalist Grey */
        .template-minimalist_grey .cv-header {
            background: #F1F2F3;
            color: #222;
            border-bottom: 1px solid #ccc;
        }

        .template-minimalist_grey .cv-header .cv-contact {
            color: #555;
        }

        .template-minimalist_grey .cv-header-photo {
            border-color: #777;
        }

        .template-minimalist_grey .cv-section h2 {
            color: #333;
            border-bottom: 1px solid #999;
        }

        .template-minimalist_grey .cv-body {
            padding-left: 75px;
            padding-right: 75px;
        }

        /* Modern Orange */
        .template-modern_orange .cv-header {
            background: linear-gradient(120deg, #A94312, #E47B35);
            border-bottom: 10px solid #F6C29E;
        }

        .template-modern_orange .cv-body {
            background: #FFFCF9;
        }

        .template-modern_orange .cv-section {
            padding: 15px;
            border-left: 4px solid #C65A19;
            background: #FFF8F2;
        }

        /* Premium Navy */
        .template-premium_navy .cv-header {
            background: #172B4D;
            border-bottom: 7px solid #AEBBD0;
        }

        .template-premium_navy .cv-item {
            border-left: 3px solid #AEBBD0;
            padding-left: 17px;
            margin-left: 5px;
        }

        .template-premium_navy .cv-section h2 {
            border-bottom: 1px solid #AEBBD0;
        }

        /* Basic */
        .template-simple .cv-header {
            background: white;
            color: #222;
            border-bottom: 4px solid #26734D;
        }

        .template-simple .cv-header .cv-contact {
            color: #4b5563;
        }

        .template-simple .cv-header-photo {
            border-color: #26734D;
        }

        .template-professional .cv-header {
            background: #173B70;
        }

        .template-executive .cv-header {
            background: #24211D;
            border-bottom: 6px solid #B58A35;
        }

        .preview-toolbar {
            max-width: 1100px;
            margin: 25px auto 0;
            padding: 20px;
            background: white;
            border-radius: 12px;
        }

        @media(max-width: 950px) {
            .cv-paper {
                width: 100%;
                margin: 15px auto;
            }
        }

        @media(max-width: 650px) {
            .cv-layout {
                display: block;
            }

            .cv-sidebar,
            .cv-main,
            .cv-body {
                padding: 25px 20px;
            }

            .template-elegant_teal .cv-sidebar {
                display: block;
            }

            .cv-header {
                padding: 30px 22px;
            }

            .cv-name {
                font-size: 23px;
            }

            .cv-photo {
                width: 85px;
                height: 85px;
            }
        }

        @media print {
            body { background: white; }

            .no-print { display: none !important; }

            .cv-paper {
                width: 100%;
                min-height: 0;
                margin: 0;
                box-shadow: none;
            }

            .cv-layout {
                min-height: 0;
            }
        }
    </style>
</head>

<body>

    <div class="no-print preview-toolbar">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <a href="{{ route('home') }}" class="text-blue-700 font-bold">NaijaCV</a>
                <h1 class="text-xl font-extrabold mt-2">Your CV is ready to preview</h1>
                <p class="text-sm text-gray-500">
                    {{ $templateLabel }} ·
                    {{ $isFree ? 'Free design' : 'Premium design — ₦3,000' }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                @if($isFree || $isPaid)
                    <a href="{{ route('guest-cvs.download', ['token' => $cv->guest_token]) }}"
                       class="bg-green-600 hover:bg-green-700 text-white font-bold px-5 py-3 rounded-lg">
                        Download PDF
                    </a>
                @elseif($isPremium = array_key_exists($template, $premiumNames))
                    <form method="POST" action="{{ route('guest-cvs.pay', ['token' => $cv->guest_token]) }}">
                        @csrf
                        <button class="bg-blue-700 hover:bg-blue-800 text-white font-bold px-5 py-3 rounded-lg">
                            Pay ₦3,000 & Download
                        </button>
                    </form>

                    @if($cv->payment_reference)
                        <a href="{{ route('guest-cvs.recover', ['token' => $cv->guest_token]) }}"
                           class="border border-green-600 text-green-700 font-semibold px-4 py-3 rounded-lg">
                            I have paid — Recover CV
                        </a>
                    @endif
                @endif
            </div>
        </div>

        @if(session('error'))
            <div class="mt-4 bg-red-50 text-red-700 border border-red-200 rounded-lg p-4">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mt-4 bg-green-50 text-green-700 border border-green-200 rounded-lg p-4">
                {{ session('success') }}
            </div>
        @endif

        @if(!$isFree && !$isPaid)
            <p class="text-sm text-gray-600 mt-3">
                Your premium design is available to preview. Complete the ₦3,000 payment
                to unlock its PDF download.
            </p>
        @endif
    </div>

    <main class="cv-paper template-{{ $template }}">

        @if($hasSidebar)

            <div class="cv-layout">

                <aside class="cv-sidebar">

                    @if($photo)
                        <img class="cv-photo" src="{{ $photo }}" alt="Profile photograph">
                    @endif

                    <h2>Contact</h2>
                    <div class="cv-contact">
                        {{ $cv->email }}<br>
                        {{ $cv->phone }}
                        @if($cv->address)
                            <br>{{ $cv->address }}
                        @endif
                    </div>

                    @if($cv->skills->count())
                        <h2>Skills</h2>
                        <ul class="cv-skills">
                            @foreach($cv->skills as $skill)
                                <li>
                                    {{ $skill->name }}
                                    @if($skill->proficiency)
                                        — {{ $skill->proficiency }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif

                </aside>

                <div class="cv-main">

                    <h1 class="cv-name">{{ $cv->full_name }}</h1>

                    @if($cv->professional_title)
                        <div class="cv-role" style="color:{{ $accent }}">
                            {{ $cv->professional_title }}
                        </div>
                    @endif

                    @if($cv->summary)
                        <section class="cv-section">
                            <h2>Professional Profile</h2>
                            <p>{{ $cv->summary }}</p>
                        </section>
                    @endif

                    @if($cv->experiences->count())
                        <section class="cv-section">
                            <h2>Work Experience</h2>

                            @foreach($cv->experiences as $experience)
                                <div class="cv-item">
                                    <h3>{{ $experience->job_title }} — {{ $experience->company }}</h3>
                                    <div class="cv-meta">
                                        {{ $experience->start_date }}
                                        @if($experience->currently_working)
                                            — Present
                                        @elseif($experience->end_date)
                                            — {{ $experience->end_date }}
                                        @endif
                                    </div>
                                    @if($experience->duties)
                                        <p>{{ $experience->duties }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </section>
                    @endif

                    @if($cv->educations->count())
                        <section class="cv-section">
                            <h2>Education</h2>

                            @foreach($cv->educations as $education)
                                <div class="cv-item">
                                    <h3>
                                        {{ $education->qualification }}
                                        @if($education->field_of_study)
                                            — {{ $education->field_of_study }}
                                        @endif
                                    </h3>
                                    <div class="cv-meta">
                                        {{ $education->institution }}
                                        @if($education->start_year)
                                            | {{ $education->start_year }}
                                        @endif
                                        @if($education->end_year)
                                            — {{ $education->end_year }}
                                        @endif
                                    </div>
                                    @if($education->description)
                                        <p>{{ $education->description }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </section>
                    @endif

                </div>
            </div>

        @else

            <header class="cv-header">

                @if($photo)
                    <img class="cv-photo cv-header-photo" src="{{ $photo }}" alt="Profile photograph">
                @endif

                <h1 class="cv-name">{{ $cv->full_name }}</h1>

                @if($cv->professional_title)
                    <div class="cv-role">{{ $cv->professional_title }}</div>
                @endif

                <div class="cv-contact">
                    {{ $cv->email }}
                    @if($cv->phone)
                        &nbsp; | &nbsp; {{ $cv->phone }}
                    @endif
                    @if($cv->address)
                        <br>{{ $cv->address }}
                    @endif
                </div>
            </header>

            <div class="cv-body">

                @if($cv->summary)
                    <section class="cv-section">
                        <h2>Professional Profile</h2>
                        <p>{{ $cv->summary }}</p>
                    </section>
                @endif

                @if($cv->experiences->count())
                    <section class="cv-section">
                        <h2>Work Experience</h2>

                        @foreach($cv->experiences as $experience)
                            <div class="cv-item">
                                <h3>{{ $experience->job_title }} — {{ $experience->company }}</h3>
                                <div class="cv-meta">
                                    {{ $experience->start_date }}
                                    @if($experience->currently_working)
                                        — Present
                                    @elseif($experience->end_date)
                                        — {{ $experience->end_date }}
                                    @endif
                                </div>
                                @if($experience->duties)
                                    <p>{{ $experience->duties }}</p>
                                @endif
                            </div>
                        @endforeach
                    </section>
                @endif

                @if($cv->educations->count())
                    <section class="cv-section">
                        <h2>Education</h2>

                        @foreach($cv->educations as $education)
                            <div class="cv-item">
                                <h3>
                                    {{ $education->qualification }}
                                    @if($education->field_of_study)
                                        — {{ $education->field_of_study }}
                                    @endif
                                </h3>
                                <div class="cv-meta">
                                    {{ $education->institution }}
                                    @if($education->start_year)
                                        | {{ $education->start_year }}
                                    @endif
                                    @if($education->end_year)
                                        — {{ $education->end_year }}
                                    @endif
                                </div>
                                @if($education->description)
                                    <p>{{ $education->description }}</p>
                                @endif
                            </div>
                        @endforeach
                    </section>
                @endif

                @if($cv->skills->count())
                    <section class="cv-section">
                        <h2>Skills</h2>
                        <ul class="cv-skills">
                            @foreach($cv->skills as $skill)
                                <li>
                                    {{ $skill->name }}
                                    @if($skill->proficiency)
                                        · {{ $skill->proficiency }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

            </div>
        @endif

    </main>

    <footer class="no-print text-center text-xs text-gray-500 py-8">
        Created with NaijaCV · Build your CV. Get hired. Move forward.
    </footer>

</body>
</html>
