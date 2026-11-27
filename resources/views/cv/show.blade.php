@php
$template = $previewTemplate ?? $cv->template ?? 'simple';
$isDraft = $isDraft ?? false;

$themes = [
'simple' => ['name' => 'Free Professional', 'primary' => '#26734D', 'soft' => '#E8F3EC', 'text' => '#263238'],
'corporate_blue' => ['name' => 'Corporate Blue', 'primary' => '#173B70', 'soft' => '#EAF1FB', 'text' => '#263238'],
'executive_gold' => ['name' => 'Executive Gold', 'primary' => '#252525', 'soft' => '#F4EBD6', 'text' => '#252525'],
'modern_green' => ['name' => 'Modern Green', 'primary' => '#176B48', 'soft' => '#E7F4EC', 'text' => '#263238'],
'burgundy' => ['name' => 'Burgundy', 'primary' => '#76263D', 'soft' => '#F6E8EC', 'text' => '#333333'],
'creative_purple' => ['name' => 'Creative Purple', 'primary' => '#6841A5', 'soft' => '#F0EAF9', 'text' => '#333333'],
'minimalist_grey' => ['name' => 'Minimalist Grey', 'primary' => '#4B5563', 'soft' => '#EEF0F2', 'text' => '#333333'],
'elegant_teal' => ['name' => 'Elegant Teal', 'primary' => '#087E83', 'soft' => '#E5F4F4', 'text' => '#333333'],
'modern_orange' => ['name' => 'Modern Orange', 'primary' => '#C65A19', 'soft' => '#FFF0E6', 'text' => '#333333'],
'premium_navy' => ['name' => 'Premium Navy', 'primary' => '#172B4D', 'soft' => '#E9EDF4', 'text' => '#263238'],
'classic_luxury' => ['name' => 'Classic Luxury', 'primary' => '#76563C', 'soft' => '#F3ECE4', 'text' => '#333333'],
'professional' => ['name' => 'Professional', 'primary' => '#173B70', 'soft' => '#EAF1FB', 'text' => '#263238'],
'executive' => ['name' => 'Executive', 'primary' => '#252525', 'soft' => '#F4EBD6', 'text' => '#252525'],
'modern' => ['name' => 'Modern', 'primary' => '#176B48', 'soft' => '#E7F4EC', 'text' => '#263238'],
];

$theme = $themes[$template] ?? $themes['simple'];

$isFree = $template === 'simple'
&& !$cv->is_paid
&& in_array($cv->payment_status, ['free', null, ''], true);

$isPaid = $cv->is_paid && $cv->payment_status === 'paid';

$isPremium = !$isFree;

$sidebarTemplates = [
'corporate_blue',
'modern_green',
'elegant_teal',
'classic_luxury',
];

$hasSidebar = in_array($template, $sidebarTemplates, true);

$photoUrl = $previewPhotoUrl ?? ($cv->photo_path
? asset('storage/' . $cv->photo_path)
: null);

$previewChoices = array_merge(['simple'], $premiumTemplates ?? [], ['professional', 'modern', 'executive']);
$previewChoiceIndex = array_search($template, $previewChoices, true);
$previewChoiceIndex = $previewChoiceIndex === false ? 0 : $previewChoiceIndex;
$previousPreviewTemplate = $previewChoices[
    ($previewChoiceIndex - 1 + count($previewChoices)) % count($previewChoices)
];
$nextPreviewTemplate = $previewChoices[
    ($previewChoiceIndex + 1) % count($previewChoices)
];
@endphp

<x-app-layout>
    <div id="cv-preview-async-content"
         data-cv-preview-container
         class="cv-preview-async-content min-h-screen bg-gray-100 py-8 px-4">

        <div class="max-w-6xl mx-auto">

            {{-- PAGE HEADER --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-7">

                <div>
                    <a href="{{ route('cvs.index') }}"
                        class="text-sm text-blue-600 hover:underline">
                        ← Back to My CVs
                    </a>

                    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 mt-3">
                        CV Preview
                    </h1>

                    <p class="text-sm text-gray-500 mt-1">
                        Review your CV before downloading it.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2 items-center">

                    @if($isFree || $isPaid)
                    <span class="bg-green-100 text-green-800 text-xs font-bold px-4 py-2 rounded-full">
                        {{ $isPaid ? 'PAID AND UNLOCKED' : 'FREE CV' }}
                    </span>
                    @else
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-4 py-2 rounded-full">
                        PREMIUM — PAYMENT REQUIRED
                    </span>
                    @endif

                    <span class="bg-white border text-gray-600 text-xs font-semibold px-4 py-2 rounded-full">
                        {{ $theme['name'] }}
                    </span>

                </div>

            </div>


            {{-- SAVED MESSAGE --}}
            <div class="{{ $isDraft ? 'bg-amber-50 border-amber-200' : 'bg-green-50 border-green-200' }} border rounded-xl p-4 mb-6 flex gap-3 items-start">
                <div class="{{ $isDraft ? 'text-amber-700' : 'text-green-700' }} text-xl">✓</div>
                <div>
                    <p class="font-bold {{ $isDraft ? 'text-amber-900' : 'text-green-900' }}">
                        {{ $isDraft ? 'Preview only — your CV has not been saved.' : 'Your CV is saved in your account.' }}
                    </p>
                    <p class="text-sm {{ $isDraft ? 'text-amber-800' : 'text-green-800' }} mt-1">
                        @if($isDraft)
                            @if($isFree)
                                Choose Save CV to keep this document, or download a free PDF. Previewing a different template will not save it.
                            @else
                                Choose Save CV to keep this document, or pay ₦3,000 to download the premium PDF. Previewing a different template will not save it.
                            @endif
                        @else
                            You can return to My CVs and access this document whenever you log in.
                        @endif
                    </p>
                </div>
            </div>

            {{-- ACTION BAR --}}
            <div class="bg-white border rounded-xl p-4 mb-7 shadow-sm">

                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                    <div>
                        <p class="font-bold text-gray-900">
                            {{ $cv->title }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $cv->full_name }} · {{ $theme['name'] }} Design
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if($isDraft)
                            <form method="POST" action="{{ route('cvs.preview.edit', $draftId) }}">
                                @csrf
                                <input type="hidden" name="template" value="{{ $template }}">
                                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-5 py-3 rounded-lg text-sm font-bold">
                                    Edit details
                                </button>
                            </form>

                            <div class="cv-save-download-actions">
                                <form method="POST" action="{{ route('cvs.preview.save', $draftId) }}">
                                    @csrf
                                    <input type="hidden" name="template" value="{{ $template }}">
                                    <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-lg text-sm font-bold">
                                        Save CV
                                    </button>
                                </form>

                                @if($isFree)
                                    <form method="POST" action="{{ route('cvs.preview.download', $draftId) }}">
                                        @csrf
                                        <input type="hidden" name="template" value="{{ $template }}">
                                        <button type="submit" class="bg-white border border-green-700 text-green-800 px-6 py-3 rounded-lg text-sm font-bold">
                                            Download PDF
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('cvs.preview.pay', $draftId) }}">
                                        @csrf
                                        <input type="hidden" name="template" value="{{ $template }}">
                                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-3 rounded-lg text-sm font-bold">
                                            Pay ₦3,000 &amp; Download
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @else
                            @if(!$isPaid)
                                <a href="{{ route('cvs.edit', $cv) }}"
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-5 py-3 rounded-lg text-sm font-bold text-center">
                                    Edit CV
                                </a>
                            @endif

                            @if($isFree || $isPaid)
                                <a href="{{ route('cvs.download', $cv) }}"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-sm font-bold text-center">
                                    Download PDF
                                </a>
                            @else
                                <form method="POST" action="{{ route('cvs.pay', $cv) }}">
                                    @csrf
                                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-3 rounded-lg text-sm font-bold">
                                        Pay ₦3,000 &amp; Download
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                    </div>

                </div>

            </div>


            {{-- CV DOCUMENT AND TEMPLATE SWITCHER --}}
            <div class="cv-preview-stage">
                <div class="cv-preview-scroll overflow-x-auto pb-6">

                    <div class="cv-paper mx-auto bg-white shadow-xl"
                    style="--cv-primary: {{ $theme['primary'] }};
                            --cv-soft: {{ $theme['soft'] }};
                            --cv-text: {{ $theme['text'] }};">

                    @if($hasSidebar)

                    {{-- SIDEBAR DESIGNS --}}
                    <div class="cv-sidebar-layout">

                        <aside class="cv-sidebar">

                            <div class="text-center mb-8">

                                @if($photoUrl)
                                <img src="{{ $photoUrl }}"
                                    alt="Profile photograph"
                                    class="cv-photo mx-auto mb-5">
                                @else
                                <div class="cv-photo-placeholder mx-auto mb-5">
                                    {{ strtoupper(substr($cv->full_name, 0, 1)) }}
                                </div>
                                @endif

                                <h2 class="text-xl font-extrabold text-white">
                                    {{ $cv->full_name }}
                                </h2>

                                <p class="text-sm text-white opacity-80 mt-2">
                                    {{ $cv->professional_title }}
                                </p>

                            </div>


                            <div class="cv-sidebar-section">
                                <h3>CONTACT</h3>

                                @if($cv->email)
                                <p>{{ $cv->email }}</p>
                                @endif

                                @if($cv->phone)
                                <p>{{ $cv->phone }}</p>
                                @endif

                                @if($cv->address)
                                <p>{{ $cv->address }}</p>
                                @endif
                            </div>


                            @if($cv->skills->count())
                            <div class="cv-sidebar-section">
                                <h3>SKILLS</h3>

                                @foreach($cv->skills as $skill)
                                <div class="mb-3">
                                    <p class="font-semibold">{{ $skill->name }}</p>
                                    @if($skill->proficiency)
                                    <p class="text-xs opacity-75">{{ $skill->proficiency }}</p>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @endif

                        </aside>


                        <main class="cv-main-content">

                            @if($cv->summary)
                            <section class="cv-section">
                                <h2>PROFILE</h2>
                                <p class="cv-body-text">{{ $cv->summary }}</p>
                            </section>
                            @endif


                            @if($cv->experiences->count())
                            <section class="cv-section">
                                <h2>WORK EXPERIENCE</h2>

                                @foreach($cv->experiences as $experience)
                                <div class="cv-entry">
                                    <h3>{{ $experience->job_title }}</h3>
                                    <p class="cv-entry-subtitle">
                                        {{ $experience->company }}
                                    </p>

                                    <p class="cv-entry-date">
                                        {{ $experience->start_date }}
                                        —
                                        {{ $experience->currently_working ? 'Present' : $experience->end_date }}
                                    </p>

                                    @if($experience->duties)
                                    <p class="cv-body-text mt-2">
                                        {{ $experience->duties }}
                                    </p>
                                    @endif
                                </div>
                                @endforeach
                            </section>
                            @endif


                            @if($cv->educations->count())
                            <section class="cv-section">
                                <h2>EDUCATION</h2>

                                @foreach($cv->educations as $education)
                                <div class="cv-entry">
                                    <h3>{{ $education->qualification }}</h3>
                                    <p class="cv-entry-subtitle">
                                        {{ $education->institution }}
                                    </p>

                                    @if($education->field_of_study)
                                    <p class="cv-body-text">
                                        {{ $education->field_of_study }}
                                    </p>
                                    @endif

                                    <p class="cv-entry-date">
                                        {{ $education->start_year }}
                                        —
                                        {{ $education->end_year }}
                                    </p>

                                    @if($education->description)
                                    <p class="cv-body-text mt-2">
                                        {{ $education->description }}
                                    </p>
                                    @endif
                                </div>
                                @endforeach
                            </section>
                            @endif

                        </main>

                    </div>

                    @else

                    {{-- STANDARD / SINGLE-COLUMN DESIGNS --}}
                    <div class="cv-standard-header">

                        @if($photoUrl)
                        <img src="{{ $photoUrl }}"
                            alt="Profile photograph"
                            class="cv-standard-photo">
                        @endif

                        <div class="flex-1">
                            <h2 class="cv-name">
                                {{ $cv->full_name }}
                            </h2>

                            <p class="cv-professional-title">
                                {{ $cv->professional_title }}
                            </p>

                            <div class="cv-contact">
                                @if($cv->email)
                                <span>{{ $cv->email }}</span>
                                @endif

                                @if($cv->phone)
                                <span>{{ $cv->phone }}</span>
                                @endif

                                @if($cv->address)
                                <span>{{ $cv->address }}</span>
                                @endif
                            </div>

                    </div>
                    </div>


                    <div class="cv-standard-content">

                        @if($cv->summary)
                        <section class="cv-section">
                            <h2>PROFESSIONAL SUMMARY</h2>
                            <p class="cv-body-text">{{ $cv->summary }}</p>
                        </section>
                        @endif


                        @if($cv->experiences->count())
                        <section class="cv-section">
                            <h2>WORK EXPERIENCE</h2>

                            @foreach($cv->experiences as $experience)
                            <div class="cv-entry">
                                <h3>{{ $experience->job_title }}</h3>
                                <p class="cv-entry-subtitle">
                                    {{ $experience->company }}
                                </p>

                                <p class="cv-entry-date">
                                    {{ $experience->start_date }}
                                    —
                                    {{ $experience->currently_working ? 'Present' : $experience->end_date }}
                                </p>

                                @if($experience->duties)
                                <p class="cv-body-text mt-2">
                                    {{ $experience->duties }}
                                </p>
                                @endif
                            </div>
                            @endforeach
                        </section>
                        @endif


                        @if($cv->educations->count())
                        <section class="cv-section">
                            <h2>EDUCATION</h2>

                            @foreach($cv->educations as $education)
                            <div class="cv-entry">
                                <h3>{{ $education->qualification }}</h3>
                                <p class="cv-entry-subtitle">
                                    {{ $education->institution }}
                                </p>

                                @if($education->field_of_study)
                                <p class="cv-body-text">
                                    {{ $education->field_of_study }}
                                </p>
                                @endif

                                <p class="cv-entry-date">
                                    {{ $education->start_year }}
                                    —
                                    {{ $education->end_year }}
                                </p>

                                @if($education->description)
                                <p class="cv-body-text mt-2">
                                    {{ $education->description }}
                                </p>
                                @endif
                            </div>
                            @endforeach
                        </section>
                        @endif


                        @if($cv->skills->count())
                        <section class="cv-section">
                            <h2>SKILLS</h2>

                            <div class="flex flex-wrap gap-2">
                                @foreach($cv->skills as $skill)
                                <span class="cv-skill">
                                    {{ $skill->name }}
                                    @if($skill->proficiency)
                                    <small>({{ $skill->proficiency }})</small>
                                    @endif
                                </span>
                                @endforeach
                            </div>
                        </section>
                        @endif

                    </div>

                    @endif

                </div>

                @if($isDraft)
                    <nav class="cv-preview-arrow-nav" aria-label="CV template preview navigation">
                        <a href="{{ route('cvs.preview', ['draft' => $draftId, 'template' => $previousPreviewTemplate]) }}"
                           class="cv-preview-arrow"
                           data-preview-direction="previous"
                           aria-label="Preview previous template"
                           title="Preview previous template">
                            ←
                        </a>
                        <a href="{{ route('cvs.preview', ['draft' => $draftId, 'template' => $nextPreviewTemplate]) }}"
                           class="cv-preview-arrow"
                           data-preview-direction="next"
                           aria-label="Preview next template"
                           title="Preview next template">
                            →
                        </a>
                    </nav>
                @endif

            </div>


            {{-- BOTTOM ACTIONS --}}
            <div class="bg-white border rounded-xl p-5 mt-5 text-center">

                <h2 class="text-lg font-bold text-gray-900">
                    {{ $isDraft ? 'Preview complete' : 'Your CV is ready' }}
                </h2>

                <p class="text-sm text-gray-500 mt-2">
                    {{ $isDraft ? 'This preview has not been saved. Choose what you want to do next.' : 'Your document is saved to your account. You can return to it at any time.' }}
                </p>

                <div class="{{ $isDraft ? 'cv-save-download-actions mx-auto mt-5' : 'flex flex-wrap justify-center gap-3 mt-5' }}">

                    @if($isDraft)
                        <form method="POST" action="{{ route('cvs.preview.save', $draftId) }}">
                            @csrf
                            <input type="hidden" name="template" value="{{ $template }}">
                            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-7 py-3 rounded-lg font-bold text-sm">
                                Save CV
                            </button>
                        </form>

                        @if($isFree)
                            <form method="POST" action="{{ route('cvs.preview.download', $draftId) }}">
                                @csrf
                                <input type="hidden" name="template" value="{{ $template }}">
                                <button type="submit" class="bg-white border border-green-700 text-green-800 px-7 py-3 rounded-lg font-bold text-sm">
                                    Download PDF
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('cvs.preview.pay', $draftId) }}">
                                @csrf
                                <input type="hidden" name="template" value="{{ $template }}">
                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg font-bold text-sm">
                                    Pay ₦3,000 &amp; Download
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('cvs.index') }}"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-bold text-sm">
                            My Saved CVs
                        </a>

                        @if($isFree || $isPaid)
                            <a href="{{ route('cvs.download', $cv) }}"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-7 py-3 rounded-lg font-bold text-sm">
                                Download My CV
                            </a>
                        @else
                            <form method="POST" action="{{ route('cvs.pay', $cv) }}">
                                @csrf
                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg font-bold text-sm">
                                    Pay ₦3,000 &amp; Download
                                </button>
                            </form>
                        @endif
                    @endif

                </div>

            </div>

        </div>

    </div>
</x-app-layout>


<style>
    .cv-paper {
        width: 210mm;
        min-height: 297mm;
        color: var(--cv-text);
        font-family: Arial, Helvetica, sans-serif;
        overflow: hidden;
    }

    .cv-sidebar-layout {
        display: grid;
        grid-template-columns: 31% 69%;
        min-height: 297mm;
    }

    .cv-sidebar {
        background: var(--cv-primary);
        color: white;
        padding: 35px 25px;
    }

    .cv-photo,
    .cv-photo-placeholder {
        width: 115px;
        height: 115px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid rgba(255, 255, 255, .8);
    }

    .cv-photo-placeholder {
        background: rgba(255, 255, 255, .2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 40px;
        font-weight: bold;
    }

    .cv-sidebar-section {
        margin-top: 35px;
        font-size: 12px;
        line-height: 1.8;
        overflow-wrap: anywhere;
    }

    .cv-sidebar-section h3 {
        color: white;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 1.5px;
        border-bottom: 1px solid rgba(255, 255, 255, .45);
        padding-bottom: 8px;
        margin-bottom: 15px;
    }

    .cv-main-content {
        padding: 45px 35px;
    }

    .cv-standard-header {
        background: var(--cv-soft);
        border-bottom: 5px solid var(--cv-primary);
        padding: 40px;
        display: flex;
        align-items: center;
        gap: 25px;
    }

    .cv-standard-photo {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
    }

    .cv-name {
        color: var(--cv-primary);
        font-size: 30px;
        line-height: 1.2;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .cv-professional-title {
        font-size: 15px;
        margin-top: 8px;
        font-weight: 600;
    }

    .cv-contact {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 15px;
        font-size: 10px;
        margin-top: 16px;
        overflow-wrap: anywhere;
    }

    .cv-standard-content {
        padding: 35px 40px;
    }

    .cv-section {
        margin-bottom: 30px;
        page-break-inside: avoid;
    }

    .cv-section h2 {
        color: var(--cv-primary);
        font-size: 14px;
        font-weight: 800;
        letter-spacing: 1.4px;
        border-bottom: 2px solid var(--cv-primary);
        padding-bottom: 8px;
        margin-bottom: 17px;
    }

    .cv-entry {
        margin-bottom: 20px;
        page-break-inside: avoid;
    }

    .cv-entry h3 {
        font-size: 14px;
        font-weight: 800;
        color: #252525;
    }

    .cv-entry-subtitle {
        font-size: 12px;
        font-weight: 700;
        color: var(--cv-primary);
        margin-top: 3px;
    }

    .cv-entry-date {
        font-size: 10px;
        color: #777;
        margin-top: 4px;
    }

    .cv-body-text {
        font-size: 12px;
        line-height: 1.8;
        white-space: pre-line;
        overflow-wrap: anywhere;
    }

    .cv-skill {
        display: inline-block;
        background: var(--cv-soft);
        color: var(--cv-primary);
        border-radius: 5px;
        padding: 6px 10px;
        font-size: 11px;
        margin: 0 5px 7px 0;
    }

    .cv-skill small {
        opacity: .8;
    }

    @media (max-width: 767px) {
        .cv-paper {
            width: 100%;
            min-width: 0;
            min-height: 0;
        }

        .cv-sidebar-layout {
            grid-template-columns: minmax(0, 1fr);
            min-height: 0;
        }

        .cv-sidebar {
            padding: 26px 22px;
        }

        .cv-sidebar .text-center {
            text-align: left;
        }

        .cv-main-content,
        .cv-standard-content {
            padding: 28px 22px;
        }

        .cv-standard-header {
            align-items: flex-start;
            flex-direction: column;
            padding: 28px 22px;
        }

        .cv-standard-photo,
        .cv-photo,
        .cv-photo-placeholder {
            width: 88px;
            height: 88px;
        }
    }

    @media print {
        body {
            background: white !important;
        }

        .cv-paper {
            box-shadow: none !important;
            margin: 0 !important;
        }
    }
</style>