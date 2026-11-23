
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $cv->full_name }} | NaijaCV Preview</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: #f8fafc;
            font-family: 'DM Sans', sans-serif;
        }

        .cv-paper {
            width: 100%;
            max-width: 850px;
            min-height: 1000px;
            margin: auto;
            background: white;
            padding: 55px;
            box-shadow: 0 5px 25px rgba(0,0,0,.07);
            color: #374151;
        }

        .cv-personal-header {
            padding-bottom: 24px;
            border-bottom: 2px solid #087443;
        }

        .cv-name {
            color: #111827;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .cv-accent {
            color: #087443 !important;
        }

        .cv-heading {
            color: #087443;
            border-bottom: 2px solid #087443;
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 1px;
        }

        .cv-section {
            margin-top: 30px;
        }

        /* PROFESSIONAL TEMPLATE */
        .template-professional {
            border-top: 10px solid #172554;
        }

        .template-professional .cv-personal-header {
            border-bottom: 3px solid #1e3a8a;
        }

        .template-professional .cv-name {
            color: #172554;
        }

        .template-professional .cv-accent {
            color: #1d4ed8 !important;
        }

        .template-professional .cv-heading {
            color: #1e3a8a;
            border-bottom-color: #1e3a8a;
        }

        /* EXECUTIVE TEMPLATE */
        .template-executive {
            border-top: 12px solid #1f2937;
        }

        .template-executive .cv-personal-header {
            background: #1f2937;
            color: white;
            padding: 28px;
            border: none;
            margin: -55px -55px 30px;
        }

        .template-executive .cv-name {
            color: white;
        }

        .template-executive .cv-personal-header p,
        .template-executive .cv-personal-header div,
        .template-executive .cv-personal-header span {
            color: #f3f4f6;
        }

        .template-executive .cv-accent {
            color: #a16207 !important;
        }

        .template-executive .cv-heading {
            color: #a16207;
            border-bottom-color: #a16207;
        }

        @media(max-width: 640px) {
            .cv-paper {
                padding: 25px;
                min-height: auto;
            }

            .template-executive .cv-personal-header {
                margin: -25px -25px 25px;
                padding: 25px;
            }
        }

        @media print {
            body {
                background: white;
            }

            .cv-paper {
                box-shadow: none;
                margin: 0;
                max-width: none;
            }
        }
    </style>
</head>

<body>

<header class="bg-white border-b">
    <div class="max-w-6xl mx-auto px-5 py-4 flex justify-between items-center">

        <a href="{{ route('home') }}"
           class="text-2xl font-extrabold text-gray-900">
            Naija<span class="text-blue-700">CV</span>.
        </a>

        <span class="text-sm text-green-700 font-semibold">
            CV Created Successfully
        </span>

    </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-10">

    <!-- SUCCESS MESSAGE -->
    <div class="bg-green-50 border border-green-200 rounded-xl p-5 mb-7">

        <h1 class="font-bold text-green-800 text-lg">
            Your CV has been created!
        </h1>

        <p class="text-sm text-green-700 mt-1">
            Review your information below. Your guest CV is temporarily
            available through this private link.
        </p>

        <p class="text-xs text-green-700 mt-2">
            Guest CV expiry:
            {{ $cv->expires_at?->format('d M Y, h:i A') }}
        </p>

    </div>

    <!-- SELECTED CV TEMPLATE -->
    <article class="cv-paper template-{{ $cv->template }}">

        <!-- PERSONAL DETAILS -->
        <div class="cv-personal-header">

            <h1 class="cv-name text-3xl md:text-4xl font-extrabold uppercase">
                {{ $cv->full_name }}
            </h1>

            @if($cv->professional_title)
                <p class="cv-accent text-lg font-semibold mt-2">
                    {{ $cv->professional_title }}
                </p>
            @endif

            <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm text-gray-600 mt-4">

                <span>{{ $cv->email }}</span>

                <span>{{ $cv->phone }}</span>

                @if($cv->address)
                    <span>{{ $cv->address }}</span>
                @endif

            </div>

        </div>

        <!-- PROFESSIONAL SUMMARY -->
        @if($cv->summary)

            <section class="cv-section">

                <h2 class="cv-heading">
                    Professional Summary
                </h2>

                <p class="text-gray-700 text-sm leading-7 whitespace-pre-line">
                    {{ $cv->summary }}
                </p>

            </section>

        @endif

        <!-- WORK EXPERIENCE -->
        @if($cv->experiences->isNotEmpty())

            <section class="cv-section">

                <h2 class="cv-heading">
                    Work Experience
                </h2>

                @foreach($cv->experiences as $experience)

                    <div class="mb-6">

                        <h3 class="font-bold text-gray-900">
                            {{ $experience->job_title }}
                        </h3>

                        <p class="cv-accent font-semibold text-sm">
                            {{ $experience->company }}
                        </p>

                        <p class="text-gray-500 text-xs mt-1">

                            {{ $experience->start_date?->format('M Y') ?? 'Start date not specified' }}

                            —

                            {{ $experience->currently_working
                                ? 'Present'
                                : ($experience->end_date?->format('M Y') ?? 'End date not specified') }}

                        </p>

                        @if($experience->duties)

                            <p class="text-gray-700 text-sm leading-6 mt-2 whitespace-pre-line">
                                {{ $experience->duties }}
                            </p>

                        @endif

                    </div>

                @endforeach

            </section>

        @endif

        <!-- EDUCATION -->
        @if($cv->educations->isNotEmpty())

            <section class="cv-section">

                <h2 class="cv-heading">
                    Education
                </h2>

                @foreach($cv->educations as $education)

                    <div class="mb-5">

                        <h3 class="font-bold text-gray-900">
                            {{ $education->qualification }}
                        </h3>

                        <p class="cv-accent text-sm font-semibold">
                            {{ $education->institution }}
                        </p>

                        @if($education->field_of_study)

                            <p class="text-gray-600 text-sm">
                                {{ $education->field_of_study }}
                            </p>

                        @endif

                        @if($education->start_year || $education->end_year)

                            <p class="text-gray-500 text-xs mt-1">
                                {{ $education->start_year }}

                                @if($education->end_year)
                                    — {{ $education->end_year }}
                                @endif
                            </p>

                        @endif

                        @if($education->description)

                            <p class="text-gray-700 text-sm mt-2">
                                {{ $education->description }}
                            </p>

                        @endif

                    </div>

                @endforeach

            </section>

        @endif

        <!-- SKILLS -->
        @if($cv->skills->isNotEmpty())

            <section class="cv-section">

                <h2 class="cv-heading">
                    Skills
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                    @foreach($cv->skills as $skill)

                        <div class="text-sm text-gray-700">

                            <span class="font-semibold">
                                {{ $skill->name }}
                            </span>

                            @if($skill->proficiency)

                                <span class="text-gray-500">
                                    — {{ $skill->proficiency }}
                                </span>

                            @endif

                        </div>

                    @endforeach

                </div>

            </section>

        @endif

    </article>

    <!-- NEXT ACTION -->
    <div class="max-w-3xl mx-auto text-center mt-10">

        <h2 class="text-2xl font-extrabold">
            Your CV is ready!
        </h2>

        <p class="text-gray-500 mt-2">
            Your selected design is
            <strong>
                @if($cv->template === 'simple')
                    Basic CV
                @elseif($cv->template === 'professional')
                    Professional CV
                @else
                    Executive CV
                @endif
            </strong>.
        </p>

        <!-- BASIC / FREE CV -->
        @if($cv->template === 'simple' && $cv->payment_status === 'free' && !$cv->is_paid)

            <p class="text-gray-500 text-sm mt-2">
                Your Basic CV is free. You can download it as a PDF.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4 mt-7">

                <a
                    href="{{ route('guest-cvs.download', $cv->guest_token) }}"
                    class="inline-block bg-green-600 hover:bg-green-700 text-white px-7 py-4 rounded-xl font-bold"
                >
                    ↓ Download Free CV (PDF)
                </a>

                <a
                    href="{{ route('guest-cvs.create') }}"
                    class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-700 px-7 py-4 rounded-xl font-bold"
                >
                    Create Another CV
                </a>

            </div>

        <!-- PREMIUM CV -->
        @else

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mt-7 text-left">

                <h3 class="font-bold text-blue-900 text-lg">
                    Premium CV Design Selected
                </h3>

                <p class="text-sm text-blue-800 mt-2">

                    You selected the

                    <strong>
                        {{ $cv->template === 'professional' ? 'Professional' : 'Executive' }}
                    </strong>

                    template.

                </p>

                <div class="flex justify-between items-center mt-5 py-4 border-t border-blue-200">

                    <span class="text-blue-900 font-semibold">
                        Premium CV Price
                    </span>

                    <span class="text-2xl font-extrabold text-blue-900">
                        ₦3,000
                    </span>

                </div>

                <div class="bg-white border border-blue-100 rounded-lg p-4 mt-3">

                    <p class="text-sm font-semibold text-gray-800">
                        Payment integration is coming in the next stage.
                    </p>

                    <p class="text-xs text-gray-500 mt-1">
                        Your CV has been saved. PDF download will become
                        available after successful payment verification.
                    </p>

                </div>

            </div>

            <div class="mt-6">

                <a
                    href="{{ route('guest-cvs.create') }}"
                    class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-700 px-7 py-4 rounded-xl font-bold"
                >
                    Create Another CV
                </a>

            </div>

        @endif

    </div>

</main>

<footer class="text-center py-7 text-sm text-gray-500">
    © {{ date('Y') }} NaijaCV. All rights reserved.
</footer>

</body>
</html>