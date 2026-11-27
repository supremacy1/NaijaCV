<!DOCTYPE html>
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

$photoFile = $draftPhotoPath ?? ($cv->photo_path
? public_path('storage/' . $cv->photo_path)
: null);

$hasPhoto = $photoFile && file_exists($photoFile);
@endphp

<html lang="en">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 25px 28px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #252525;
        }

        .cv {
            width: 100%;

            --accent: {
                    {
                    $accent
                }
            }

            ;

            --soft: {
                    {
                    $soft
                }
            }

            ;
        }

        .header {
            padding: 25px 28px;

            background-color: {
                    {
                    $accent
                }
            }

            ;
            color: #fff;
            min-height: 105px;
        }

        .name {
            font-size: 23px;
            font-weight: bold;
            margin: 0 0 5px;
        }

        .role {
            font-size: 11px;
            margin-bottom: 8px;
        }

        .contact {
            font-size: 8.5px;
            line-height: 1.7;
        }

        .photo {
            width: 82px;
            height: 82px;
            border-radius: 41px;
            border: 3px solid #fff;
            object-fit: cover;
        }

        .header-photo {
            float: right;
            margin-left: 15px;
        }

        .body {
            padding: 25px 28px;
        }

        .section {
            margin-bottom: 19px;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;

            color: {
                    {
                    $accent
                }
            }

            ;

            border-bottom: 1.5px solid {
                    {
                    $accent
                }
            }

            ;
            padding-bottom: 5px;
            margin: 0 0 9px;
        }

        .summary {
            font-size: 9px;
            line-height: 1.6;
            margin: 0;
            white-space: pre-line;
        }

        .item {
            margin-bottom: 12px;
        }

        .item-title {
            font-size: 9.5px;
            font-weight: bold;
            margin: 0 0 3px;
            color: #222;
        }

        .meta {
            font-size: 8px;
            color: #666;
            margin-bottom: 4px;
        }

        .description {
            font-size: 8.5px;
            line-height: 1.55;
            margin: 0;
            white-space: pre-line;
        }

        .skills {
            margin: 0;
            padding: 0;
        }

        .skill {
            display: inline-block;

            background-color: {
                    {
                    $soft
                }
            }

            ;

            color: {
                    {
                    $accent
                }
            }

            ;
            padding: 4px 7px;
            margin: 2px 3px 3px 0;
            font-size: 8px;
        }

        .rule {
            height: 4px;

            background-color: {
                    {
                    $accent
                }
            }

            ;
        }

        /* Sidebar layouts: table-based for DomPDF */
        .layout-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sidebar {
            width: 29%;
            vertical-align: top;
            padding: 22px 15px;

            background-color: {
                    {
                    $accent
                }
            }

            ;
            color: #fff;
        }

        .main-column {
            width: 71%;
            vertical-align: top;
            padding: 25px 22px;
            background-color: #fff;
        }

        .sidebar-photo {
            display: block;
            width: 95px;
            height: 95px;
            border-radius: 48px;
            margin: 0 auto 20px;
        }

        .sidebar-title {
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, .6);
            padding-bottom: 5px;
            margin: 20px 0 8px;
        }

        .sidebar-contact {
            font-size: 8px;
            line-height: 1.8;
            overflow-wrap: anywhere;
        }

        .sidebar-skill {
            font-size: 8px;
            margin-bottom: 7px;
            line-height: 1.5;
        }

        .sidebar-blue {
            background-color: #E3E3E3;
            color: #252525;
            border-right: 1px solid #ccc;
        }

        .sidebar-blue .sidebar-title {
            color: #173B70;
            border-color: #173B70;
        }

        .sidebar-blue .sidebar-contact,
        .sidebar-blue .sidebar-skill {
            color: #374151;
        }

        .sidebar-teal {
            background-color: #E5F4F4;
            color: #164D50;
        }

        .sidebar-teal .sidebar-title {
            color: #087E83;
            border-color: #087E83;
        }

        .sidebar-teal .sidebar-contact,
        .sidebar-teal .sidebar-skill {
            color: #164D50;
        }

        .sidebar-luxury {
            background-color: #F3ECE4;
            color: #4B3829;
            border-right: 4px solid #B89A70;
        }

        .sidebar-luxury .sidebar-title {
            color: #76563C;
            border-color: #B89A70;
        }

        .sidebar-luxury .sidebar-contact,
        .sidebar-luxury .sidebar-skill {
            color: #4B3829;
        }

        /* Executive Gold */
        .template-executive_gold .header {
            background-color: #24211D;
            text-align: center;
            border-bottom: 7px solid #B58A35;
        }

        .template-executive_gold .header-photo {
            float: none;
            display: block;
            margin: 0 auto 10px;
            border-color: #B58A35;
        }

        .template-executive_gold .section-title {
            color: #A77C27;
            border-color: #D8BC7B;
        }

        .template-executive_gold .body {
            padding-left: 55px;
            padding-right: 55px;
        }

        /* Burgundy */
        .template-burgundy .header {
            background-color: #fff;
            color: #252525;
            border-top: 12px solid #76263D;
            border-bottom: 1px solid #ddd;
        }

        .template-burgundy .header .contact,
        .template-burgundy .header .role {
            color: #555;
        }

        .template-burgundy .header-photo {
            border-color: #76263D;
        }

        .template-burgundy .body {
            border-left: 8px solid #F6E8EC;
        }

        /* Creative Purple */
        .template-creative_purple .header {
            background-color: #6841A5;
            border-bottom: 8px solid #C8B4E4;
        }

        .template-creative_purple .section-title {
            border-left: 4px solid #6841A5;
            padding-left: 8px;
        }

        /* Minimalist Grey */
        .template-minimalist_grey .header {
            background-color: #F1F2F3;
            color: #222;
            border-bottom: 1px solid #bbb;
        }

        .template-minimalist_grey .header .contact {
            color: #555;
        }

        .template-minimalist_grey .header-photo {
            border-color: #777;
        }

        .template-minimalist_grey .section-title {
            color: #333;
            border-color: #999;
        }

        .template-minimalist_grey .body {
            padding-left: 55px;
            padding-right: 55px;
        }

        /* Modern Orange */
        .template-modern_orange .header {
            background-color: #A94312;
            border-bottom: 9px solid #F6C29E;
        }

        .template-modern_orange .section {
            padding: 10px;
            border-left: 4px solid #C65A19;
            background-color: #FFF8F2;
        }

        /* Premium Navy */
        .template-premium_navy .header {
            background-color: #172B4D;
            border-bottom: 7px solid #AEBBD0;
        }

        .template-premium_navy .item {
            border-left: 3px solid #AEBBD0;
            padding-left: 10px;
        }

        .template-premium_navy .section-title {
            border-color: #AEBBD0;
        }

        /* Basic */
        .template-simple .header {
            background-color: #fff;
            color: #222;
            border-bottom: 4px solid #26734D;
        }

        .template-simple .header .contact {
            color: #4b5563;
        }

        .template-simple .header-photo {
            border-color: #26734D;
        }

        .template-professional .header {
            background-color: #173B70;
        }

        .template-executive .header {
            background-color: #24211D;
            border-bottom: 6px solid #B58A35;
        }
    </style>
</head>

<body>

    <div class="cv template-{{ $template }}">

        @if($hasSidebar)

        @php
        $sidebarClass = match($template) {
        'corporate_blue' => 'sidebar-blue',
        'elegant_teal' => 'sidebar-teal',
        'classic_luxury' => 'sidebar-luxury',
        default => '',
        };

        $sidebarOnRight = $template === 'elegant_teal';
        @endphp

        <table class="layout-table">
            <tr>

                @if(!$sidebarOnRight)

                <td class="sidebar {{ $sidebarClass }}">

                    @if($hasPhoto)
                    <img class="sidebar-photo" src="{{ $photoFile }}">
                    @endif

                    <div class="sidebar-title">Contact</div>

                    <div class="sidebar-contact">
                        {{ $cv->email }}<br>
                        {{ $cv->phone }}
                        @if($cv->address)
                        <br>{{ $cv->address }}
                        @endif
                    </div>

                    @if($cv->skills->count())
                    <div class="sidebar-title">Skills</div>
                    @foreach($cv->skills as $skill)
                    <div class="sidebar-skill">
                        • {{ $skill->name }}
                        @if($skill->proficiency)
                        — {{ $skill->proficiency }}
                        @endif
                    </div>
                    @endforeach
                    @endif

                </td>

                @endif

                <td class="main-column">

                    <h1 class="name">{{ $cv->full_name }}</h1>

                    @if($cv->professional_title)
                    <div class="role" style="color:{{ $accent }}">
                        {{ $cv->professional_title }}
                    </div>
                    @endif

                    @if($cv->summary)
                    <div class="section">
                        <div class="section-title">Professional Profile</div>
                        <p class="summary">{{ $cv->summary }}</p>
                    </div>
                    @endif

                    @if($cv->experiences->count())
                    <div class="section">
                        <div class="section-title">Work Experience</div>

                        @foreach($cv->experiences as $experience)
                        <div class="item">
                            <div class="item-title">
                                {{ $experience->job_title }} — {{ $experience->company }}
                            </div>

                            <div class="meta">
                                {{ $experience->start_date }}
                                @if($experience->currently_working)
                                — Present
                                @elseif($experience->end_date)
                                — {{ $experience->end_date }}
                                @endif
                            </div>

                            @if($experience->duties)
                            <p class="description">{{ $experience->duties }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($cv->educations->count())
                    <div class="section">
                        <div class="section-title">Education</div>

                        @foreach($cv->educations as $education)
                        <div class="item">
                            <div class="item-title">
                                {{ $education->qualification }}
                                @if($education->field_of_study)
                                — {{ $education->field_of_study }}
                                @endif
                            </div>

                            <div class="meta">
                                {{ $education->institution }}
                                @if($education->start_year)
                                | {{ $education->start_year }}
                                @endif
                                @if($education->end_year)
                                — {{ $education->end_year }}
                                @endif
                            </div>

                            @if($education->description)
                            <p class="description">{{ $education->description }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                </td>

                @if($sidebarOnRight)

                <td class="sidebar {{ $sidebarClass }}">

                    @if($hasPhoto)
                    <img class="sidebar-photo" src="{{ $photoFile }}">
                    @endif

                    <div class="sidebar-title">Contact</div>

                    <div class="sidebar-contact">
                        {{ $cv->email }}<br>
                        {{ $cv->phone }}
                        @if($cv->address)
                        <br>{{ $cv->address }}
                        @endif
                    </div>

                    @if($cv->skills->count())
                    <div class="sidebar-title">Skills</div>
                    @foreach($cv->skills as $skill)
                    <div class="sidebar-skill">
                        • {{ $skill->name }}
                        @if($skill->proficiency)
                        — {{ $skill->proficiency }}
                        @endif
                    </div>
                    @endforeach
                    @endif

                </td>

                @endif

            </tr>
        </table>

        @else

        <div class="header">

            @if($hasPhoto)
            <img class="photo header-photo" src="{{ $photoFile }}">
            @endif

            <h1 class="name">{{ $cv->full_name }}</h1>

            @if($cv->professional_title)
            <div class="role">{{ $cv->professional_title }}</div>
            @endif

            <div class="contact">
                {{ $cv->email }}
                @if($cv->phone)
                | {{ $cv->phone }}
                @endif
                @if($cv->address)
                <br>{{ $cv->address }}
                @endif
            </div>

        </div>

        <div class="rule"></div>

        <div class="body">

            @if($cv->summary)
            <div class="section">
                <div class="section-title">Professional Profile</div>
                <p class="summary">{{ $cv->summary }}</p>
            </div>
            @endif

            @if($cv->experiences->count())
            <div class="section">
                <div class="section-title">Work Experience</div>

                @foreach($cv->experiences as $experience)
                <div class="item">
                    <div class="item-title">
                        {{ $experience->job_title }} — {{ $experience->company }}
                    </div>

                    <div class="meta">
                        {{ $experience->start_date }}
                        @if($experience->currently_working)
                        — Present
                        @elseif($experience->end_date)
                        — {{ $experience->end_date }}
                        @endif
                    </div>

                    @if($experience->duties)
                    <p class="description">{{ $experience->duties }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            @if($cv->educations->count())
            <div class="section">
                <div class="section-title">Education</div>

                @foreach($cv->educations as $education)
                <div class="item">
                    <div class="item-title">
                        {{ $education->qualification }}
                        @if($education->field_of_study)
                        — {{ $education->field_of_study }}
                        @endif
                    </div>

                    <div class="meta">
                        {{ $education->institution }}
                        @if($education->start_year)
                        | {{ $education->start_year }}
                        @endif
                        @if($education->end_year)
                        — {{ $education->end_year }}
                        @endif
                    </div>

                    @if($education->description)
                    <p class="description">{{ $education->description }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            @if($cv->skills->count())
            <div class="section">
                <div class="section-title">Skills</div>

                @foreach($cv->skills as $skill)
                <span class="skill">
                    {{ $skill->name }}
                    @if($skill->proficiency)
                    · {{ $skill->proficiency }}
                    @endif
                </span>
                @endforeach
            </div>
            @endif

        </div>

        @endif

    </div>

</body>

</html>