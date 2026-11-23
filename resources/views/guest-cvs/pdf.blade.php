<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>{{ $cv->full_name }} - CV</title>

    <style>
        @page {
            margin: 45px 48px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #252525;
            font-size: 10px;
            line-height: 1.6;
        }

        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 16px;
            margin-bottom: 22px;
        }

        .name {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 5px;
            color: #172033;
        }

        .professional-title {
            font-size: 12px;
            color: #444;
            margin-bottom: 10px;
        }

        .contact {
            font-size: 9px;
            color: #555;
        }

        .section {
            margin-top: 20px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #222;
            border-bottom: 1px solid #999;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .entry {
            margin-bottom: 13px;
        }

        .entry-title {
            font-size: 10px;
            font-weight: bold;
            color: #222;
        }

        .entry-subtitle {
            font-size: 9px;
            color: #444;
        }

        .date {
            font-size: 8px;
            color: #666;
            margin-top: 2px;
        }

        .description {
            margin-top: 5px;
            white-space: pre-line;
        }

        .skill {
            display: inline-block;
            border: 1px solid #ddd;
            padding: 4px 7px;
            margin: 0 5px 5px 0;
            font-size: 9px;
        }

        .muted {
            color: #666;
        }
    </style>
</head>

<body>

    <!-- PERSONAL INFORMATION -->
    <div class="header">

        <h1 class="name">{{ $cv->full_name }}</h1>

        @if($cv->professional_title)
            <div class="professional-title">
                {{ $cv->professional_title }}
            </div>
        @endif

        <div class="contact">
            {{ $cv->email }}
            &nbsp; | &nbsp;
            {{ $cv->phone }}

            @if($cv->address)
                <br>
                {{ $cv->address }}
            @endif
        </div>

    </div>


    <!-- PROFESSIONAL SUMMARY -->
    @if($cv->summary)
        <div class="section">

            <div class="section-title">
                Professional Summary
            </div>

            <div class="description">
                {{ $cv->summary }}
            </div>

        </div>
    @endif


    <!-- WORK EXPERIENCE -->
    @if($cv->experiences->isNotEmpty())
        <div class="section">

            <div class="section-title">
                Work Experience
            </div>

            @foreach($cv->experiences as $experience)

                <div class="entry">

                    <div class="entry-title">
                        {{ $experience->job_title }}
                    </div>

                    <div class="entry-subtitle">
                        {{ $experience->company }}
                    </div>

                    <div class="date">
                        {{ $experience->start_date?->format('M Y') ?? 'Start date not specified' }}
                        -
                        {{ $experience->currently_working
                            ? 'Present'
                            : ($experience->end_date?->format('M Y') ?? 'End date not specified') }}
                    </div>

                    @if($experience->duties)
                        <div class="description">
                            {{ $experience->duties }}
                        </div>
                    @endif

                </div>

            @endforeach

        </div>
    @endif


    <!-- EDUCATION -->
    @if($cv->educations->isNotEmpty())
        <div class="section">

            <div class="section-title">
                Education
            </div>

            @foreach($cv->educations as $education)

                <div class="entry">

                    <div class="entry-title">
                        {{ $education->qualification }}
                    </div>

                    <div class="entry-subtitle">
                        {{ $education->institution }}
                    </div>

                    @if($education->field_of_study)
                        <div class="muted">
                            {{ $education->field_of_study }}
                        </div>
                    @endif

                    <div class="date">
                        {{ $education->start_year }}

                        @if($education->end_year)
                            - {{ $education->end_year }}
                        @endif
                    </div>

                    @if($education->description)
                        <div class="description">
                            {{ $education->description }}
                        </div>
                    @endif

                </div>

            @endforeach

        </div>
    @endif


    <!-- SKILLS -->
    @if($cv->skills->isNotEmpty())
        <div class="section">

            <div class="section-title">
                Skills
            </div>

            @foreach($cv->skills as $skill)

                <span class="skill">
                    {{ $skill->name }}

                    @if($skill->proficiency)
                        ({{ $skill->proficiency }})
                    @endif
                </span>

            @endforeach

        </div>
    @endif

</body>
</html>
