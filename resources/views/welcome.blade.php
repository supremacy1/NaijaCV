
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>NaijaCV - Build Your CV. Get Hired.</title>
    <meta name="description" content="Create a simple CV for free or upgrade to a professionally designed CV with NaijaCV. No registration required to get started.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --green: #087443;
            --green-dark: #075c36;
            --green-light: #eaf7ef;
            --dark: #14251c;
            --text: #526158;
            --muted: #819087;
            --border: #e6eee8;
            --background: #f8fbf9;
            --white: #ffffff;
            --gold: #f2b544;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            color: var(--dark);
            background: var(--white);
            line-height: 1.6;
        }

        a { text-decoration: none; color: inherit; }

        button { font: inherit; }

        .container {
            width: min(1160px, 92%);
            margin: auto;
        }

        .navbar {
            height: 78px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--border);
            background: rgba(255,255,255,.96);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(12px);
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 25px;
        }

        .logo {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 25px;
            font-weight: 800;
            letter-spacing: -1.2px;
            color: var(--dark);
        }

        .logo span { color: var(--green); }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 30px;
            font-size: 14px;
            font-weight: 600;
            color: #526158;
        }

        .nav-links a:hover { color: var(--green); }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            border-radius: 8px;
            padding: 13px 21px;
            font-weight: 700;
            font-size: 14px;
            transition: .2s ease;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--green);
            color: white;
        }

        .btn-primary:hover {
            background: var(--green-dark);
            transform: translateY(-2px);
        }

        .btn-outline {
            border-color: var(--border);
            color: var(--dark);
            background: white;
        }

        .btn-outline:hover {
            border-color: var(--green);
            color: var(--green);
        }

        .btn-white {
            background: white;
            color: var(--green-dark);
        }

        .hero {
            padding: 88px 0 85px;
            background:
                radial-gradient(circle at 85% 15%, #e2f5e9 0, transparent 28%),
                linear-gradient(135deg, #f8fcf9 0%, #ffffff 65%);
            overflow: hidden;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            align-items: center;
            gap: 65px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 13px;
            border-radius: 30px;
            background: var(--green-light);
            color: var(--green);
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 23px;
        }

        .eyebrow-dot {
            height: 7px;
            width: 7px;
            background: var(--green);
            border-radius: 50%;
        }

        h1, h2, h3 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            line-height: 1.2;
        }

        .hero h1 {
            font-size: clamp(39px, 5vw, 61px);
            letter-spacing: -2.8px;
            font-weight: 800;
            margin-bottom: 22px;
        }

        .hero h1 span { color: var(--green); }

        .hero-description {
            color: var(--text);
            font-size: 17px;
            max-width: 510px;
            line-height: 1.8;
            margin-bottom: 30px;
        }

        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 13px;
            margin-bottom: 27px;
        }

        .hero-note {
            font-size: 13px;
            color: var(--muted);
        }

        .hero-note strong { color: var(--green); }

        .hero-visual {
            position: relative;
            padding: 22px;
        }

        .cv-paper {
            background: white;
            border: 1px solid #e4ece6;
            border-radius: 12px;
            padding: 32px;
            max-width: 420px;
            margin: auto;
            box-shadow: 0 25px 70px rgba(24, 70, 43, .13);
            transform: rotate(1.5deg);
        }

        .cv-top {
            border-bottom: 2px solid var(--green);
            padding-bottom: 18px;
            margin-bottom: 19px;
        }

        .cv-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 24px;
            font-weight: 800;
        }

        .cv-role {
            color: var(--green);
            font-size: 12px;
            font-weight: 700;
            margin-top: 3px;
        }

        .cv-contact {
            color: #89958d;
            font-size: 10px;
            margin-top: 9px;
        }

        .cv-section { margin-top: 19px; }

        .cv-section h4 {
            color: var(--green);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 9px;
        }

        .cv-line {
            height: 7px;
            background: #edf1ee;
            border-radius: 10px;
            margin: 7px 0;
        }

        .cv-line.long { width: 100%; }
        .cv-line.medium { width: 78%; }
        .cv-line.short { width: 52%; }

        .floating-tag {
            position: absolute;
            background: white;
            padding: 12px 17px;
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,.07);
            font-size: 12px;
            font-weight: 800;
        }

        .tag-free { left: -4px; top: 60px; color: var(--green); }
        .tag-pdf { right: -4px; bottom: 45px; color: #a66b08; }

        .trust-strip {
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 22px 0;
            background: white;
        }

        .trust-content {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 35px;
            color: var(--text);
            font-size: 13px;
            font-weight: 700;
        }

        .trust-content span::before {
            content: "✓";
            color: var(--green);
            margin-right: 8px;
            font-weight: 900;
        }

        section { padding: 85px 0; }

        .section-heading {
            text-align: center;
            max-width: 650px;
            margin: 0 auto 48px;
        }

        .section-heading .eyebrow { margin-bottom: 15px; }

        .section-heading h2 {
            font-size: clamp(29px, 4vw, 39px);
            letter-spacing: -1.5px;
            margin-bottom: 14px;
        }

        .section-heading p {
            color: var(--text);
            font-size: 15px;
        }

        .choice-section { background: var(--background); }

        .choice-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .choice-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 15px;
            padding: 32px;
            transition: .2s;
        }

        .choice-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(18, 68, 39, .07);
        }

        .choice-card.featured {
            border: 2px solid var(--green);
            position: relative;
        }

        .popular-label {
            display: inline-block;
            background: var(--green);
            color: white;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 800;
            padding: 6px 11px;
            margin-bottom: 15px;
        }

        .choice-icon {
            height: 49px;
            width: 49px;
            display: grid;
            place-items: center;
            background: var(--green-light);
            border-radius: 12px;
            font-size: 23px;
            margin-bottom: 20px;
        }

        .choice-card h3 {
            font-size: 22px;
            margin-bottom: 8px;
        }

        .choice-card p {
            color: var(--text);
            font-size: 14px;
            margin-bottom: 20px;
        }

        .choice-price {
            font-size: 28px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .choice-price small {
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            color: var(--muted);
            font-weight: 500;
        }

        .feature-list {
            list-style: none;
            margin-bottom: 25px;
        }

        .feature-list li {
            color: var(--text);
            font-size: 13px;
            margin: 12px 0;
        }

        .feature-list li::before {
            content: "✓";
            color: var(--green);
            font-weight: 900;
            margin-right: 10px;
        }

        .choice-card .btn { width: 100%; }

        .templates-section { background: white; }

        .template-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        .template-card {
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            background: white;
        }

        .template-preview {
            background: #f1f5f2;
            padding: 22px 35px;
            height: 245px;
            display: flex;
            justify-content: center;
            overflow: hidden;
        }

        .mini-paper {
            background: white;
            width: 100%;
            max-width: 170px;
            min-height: 230px;
            padding: 17px 13px;
            box-shadow: 0 4px 15px rgba(0,0,0,.08);
        }

        .mini-heading {
            height: 9px;
            width: 65%;
            background: #263b30;
            margin-bottom: 7px;
        }

        .mini-subheading {
            height: 4px;
            width: 45%;
            background: #8da397;
            margin-bottom: 15px;
        }

        .mini-bar {
            height: 4px;
            background: #e4eae6;
            margin: 7px 0;
        }

        .mini-bar.wide { width: 100%; }
        .mini-bar.mid { width: 75%; }
        .mini-bar.small { width: 48%; }

        .mini-green {
            height: 5px;
            width: 50%;
            background: var(--green);
            margin: 13px 0 7px;
        }

        .template-info { padding: 18px; }

        .template-info h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .template-info p {
            font-size: 12px;
            color: var(--muted);
        }

        .template-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 14px;
            margin-top: 13px;
            border-top: 1px solid var(--border);
            font-size: 12px;
            font-weight: 800;
        }

        .template-footer span { color: var(--green); }

        .how-section { background: var(--background); }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .step-card { text-align: center; padding: 15px; }

        .step-number {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            background: var(--green);
            color: white;
            font-size: 19px;
            font-weight: 800;
            border-radius: 50%;
            margin: 0 auto 19px;
        }

        .step-card h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .step-card p {
            color: var(--text);
            font-size: 13px;
        }

        .upgrade-section { background: white; }

        .upgrade-box {
            background: linear-gradient(120deg, #075c36, #0b8750);
            color: white;
            border-radius: 18px;
            padding: 55px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 30px;
            align-items: center;
        }

        .upgrade-box h2 {
            font-size: clamp(27px, 4vw, 38px);
            margin-bottom: 13px;
        }

        .upgrade-box p {
            color: #d9eee2;
            font-size: 14px;
            max-width: 620px;
        }

        .upgrade-box .btn { white-space: nowrap; }

        .faq-section { background: var(--background); }

        .faq-list {
            max-width: 780px;
            margin: auto;
        }

        .faq-item {
            background: white;
            border: 1px solid var(--border);
            border-radius: 10px;
            margin-bottom: 12px;
            padding: 20px;
        }

        .faq-item h3 {
            font-size: 15px;
            margin-bottom: 8px;
        }

        .faq-item p {
            font-size: 13px;
            color: var(--text);
        }

        .footer {
            background: #10251a;
            color: white;
            padding: 55px 0 25px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 45px;
            padding-bottom: 35px;
        }

        .footer .logo { color: white; display: inline-block; margin-bottom: 15px; }

        .footer-description {
            color: #b5c7bb;
            font-size: 13px;
            max-width: 340px;
        }

        .footer h4 { margin-bottom: 15px; font-size: 14px; }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
            color: #b5c7bb;
            font-size: 13px;
        }

        .footer-links a:hover { color: white; }

        .copyright {
            border-top: 1px solid #294235;
            padding-top: 20px;
            color: #a7b8ad;
            font-size: 12px;
            text-align: center;
        }

        @media(max-width: 850px) {
            .hero-grid { grid-template-columns: 1fr; gap: 35px; }
            .hero-content { text-align: center; }
            .hero-description { margin-left: auto; margin-right: auto; }
            .hero-buttons { justify-content: center; }
            .hero-visual { max-width: 500px; width: 100%; margin: auto; }
            .nav-links { display: none; }
            .template-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media(max-width: 600px) {
            .navbar { height: 68px; }
            .logo { font-size: 22px; }
            .nav-actions { gap: 7px; }
            .nav-actions .btn { padding: 10px 12px; font-size: 12px; }
            .hero { padding: 60px 0; }
            .hero h1 { letter-spacing: -1.8px; }
            .hero-description { font-size: 15px; }
            section { padding: 65px 0; }
            .choice-grid, .template-grid, .steps-grid { grid-template-columns: 1fr; }
            .choice-card { padding: 25px; }
            .upgrade-box { grid-template-columns: 1fr; padding: 32px 24px; }
            .upgrade-box .btn { width: 100%; }
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 30px; }
            .footer-brand { grid-column: 1 / -1; }
            .trust-content { gap: 15px 22px; font-size: 11px; }
            .cv-paper { padding: 25px; }
            .floating-tag { font-size: 10px; padding: 9px 11px; }
        }
    </style>
</head>

<body>

    <!-- NAVIGATION -->
    <header class="navbar">
        <div class="container nav-content">

            <a href="{{ route('home') }}" class="logo">
                Naija<span>CV</span>.
            </a>

            <nav class="nav-links">
                <a href="#how-it-works">How it works</a>
                <a href="#templates">Templates</a>
                <a href="#pricing">Pricing</a>
                <a href="#faq">FAQs</a>
            </nav>

            <div class="nav-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-outline">Dashboard</a>
                    <a href="{{ route('cvs.create') }}" class="btn btn-primary">Create CV</a>
                @else
                    @if(Route::has('login'))
                        <a href="{{ route('login') }}" class="btn btn-outline">Log in</a>
                    @endif

                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary">Sign up</a>
                    @endif
                @endauth
            </div>

        </div>
    </header>

    <!-- HERO -->
    <section class="hero">
        <div class="container hero-grid">

            <div class="hero-content">

                <div class="eyebrow">
                    <span class="eyebrow-dot"></span>
                    YOUR CAREER STARTS HERE
                </div>

                <h1>
                    Build Your CV.<br>
                    <span>Get Hired.</span><br>
                    Move Forward.
                </h1>

                <p class="hero-description">
                    Create a clean, simple CV for free and download it as a PDF.
                    Want something more professional? Upgrade to a beautifully
                    designed CV that helps you stand out.
                </p>

                <div class="hero-buttons">
                    <a href="create-cv" class="btn btn-primary">
                        Create My Free CV →
                    </a>

                    <a href="#templates" class="btn btn-outline">
                        Explore Templates
                    </a>
                </div>

                <p class="hero-note">
                    <strong>✓ Free PDF download</strong>
                    &nbsp; No registration required to get started.
                </p>

            </div>

            <div class="hero-visual">

                <div class="floating-tag tag-free">✓ Free CV</div>

                <div class="cv-paper">

                    <div class="cv-top">
                        <div class="cv-name">JOHN DOE</div>
                        <div class="cv-role">ADMINISTRATIVE ASSISTANT</div>
                        <div class="cv-contact">
                            Lagos, Nigeria &nbsp; | &nbsp; john@email.com
                            <br>080 0000 0000
                        </div>
                    </div>

                    <div class="cv-section">
                        <h4>Professional Summary</h4>
                        <div class="cv-line long"></div>
                        <div class="cv-line long"></div>
                        <div class="cv-line medium"></div>
                    </div>

                    <div class="cv-section">
                        <h4>Work Experience</h4>
                        <div class="cv-line medium"></div>
                        <div class="cv-line long"></div>
                        <div class="cv-line medium"></div>
                    </div>

                    <div class="cv-section">
                        <h4>Education</h4>
                        <div class="cv-line medium"></div>
                        <div class="cv-line short"></div>
                    </div>

                    <div class="cv-section">
                        <h4>Skills</h4>
                        <div class="cv-line long"></div>
                        <div class="cv-line medium"></div>
                    </div>

                </div>

                <div class="floating-tag tag-pdf">↓ Download PDF</div>

            </div>
        </div>
    </section>

    <!-- TRUST STRIP -->
    <div class="trust-strip">
        <div class="container trust-content">
            <span>Free CV creation</span>
            <span>No registration required</span>
            <span>PDF download</span>
            <span>Premium designs available</span>
        </div>
    </div>

    <!-- CHOOSE YOUR CV -->
    <section class="choice-section" id="pricing">
        <div class="container">

            <div class="section-heading">
                <div class="eyebrow">CHOOSE YOUR CV</div>
                <h2>Start free. Upgrade when you're ready.</h2>
                <p>
                    Whether you're applying for your first job or looking
                    to take the next step in your career, NaijaCV has an option for you.
                </p>
            </div>

            <div class="choice-grid">

                <!-- FREE -->
                <div class="choice-card">

                    <div class="choice-icon">📄</div>

                    <h3>Basic CV</h3>

                    <p>
                        Create a simple, clean and casual CV with all your
                        important information.
                    </p>

                    <div class="choice-price">
                        ₦0 <small>/ Always free</small>
                    </div>

                    <ul class="feature-list">
                        <li>Create your CV without registration</li>
                        <li>Simple and clean CV layout</li>
                        <li>Personal information and contact details</li>
                        <li>Education and work experience</li>
                        <li>Skills and professional summary</li>
                        <li>Free PDF download</li>
                    </ul>

                    <a href="#start" class="btn btn-outline">
                        Create Free CV →
                    </a>

                </div>

                <!-- PREMIUM -->
                <div class="choice-card featured">

                    <div class="popular-label">PROFESSIONAL CHOICE</div>

                    <div class="choice-icon">⭐</div>

                    <h3>Premium CV</h3>

                    <p>
                        Make a stronger impression with professionally
                        designed and tailored CV templates.
                    </p>

                    <div class="choice-price">
                        ₦3,000 <small>/ Per CV</small>
                    </div>

                    <ul class="feature-list">
                        <li>Choose from premium CV templates</li>
                        <li>Modern professional designs</li>
                        <li>Improved layout and presentation</li>
                        <li>Professional profile and tailored sections</li>
                        <li>Preview before payment</li>
                        <li>PDF download after payment</li>
                        <li>No registration required</li>
                    </ul>

                    <a href="#templates" class="btn btn-primary">
                        Explore Premium CVs →
                    </a>

                </div>

            </div>
        </div>
    </section>

    <!-- TEMPLATES -->
    <section class="templates-section" id="templates">
        <div class="container">

            <div class="section-heading">
                <div class="eyebrow">CV TEMPLATES</div>
                <h2>Choose a design that represents you.</h2>
                <p>
                    Start with our free casual CV or choose a premium
                    design for a more polished professional appearance.
                </p>
            </div>

            <div class="template-grid">

                <div class="template-card">
                    <div class="template-preview">
                        <div class="mini-paper">
                            <div class="mini-heading"></div>
                            <div class="mini-subheading"></div>
                            <div class="mini-green"></div>
                            <div class="mini-bar wide"></div>
                            <div class="mini-bar mid"></div>
                            <div class="mini-green"></div>
                            <div class="mini-bar wide"></div>
                            <div class="mini-bar small"></div>
                            <div class="mini-green"></div>
                            <div class="mini-bar mid"></div>
                            <div class="mini-bar wide"></div>
                        </div>
                    </div>
                    <div class="template-info">
                        <h3>Simple Classic</h3>
                        <p>Clean and straightforward CV layout.</p>
                        <div class="template-footer">
                            <span>FREE</span>
                            <span>Basic Design</span>
                        </div>
                    </div>
                </div>

                <div class="template-card">
                    <div class="template-preview">
                        <div class="mini-paper" style="border-top: 9px solid #087443;">
                            <div class="mini-heading"></div>
                            <div class="mini-subheading"></div>
                            <div class="mini-green"></div>
                            <div class="mini-bar wide"></div>
                            <div class="mini-bar mid"></div>
                            <div class="mini-green"></div>
                            <div class="mini-bar wide"></div>
                            <div class="mini-bar small"></div>
                            <div class="mini-green"></div>
                            <div class="mini-bar mid"></div>
                            <div class="mini-bar wide"></div>
                        </div>
                    </div>
                    <div class="template-info">
                        <h3>Modern Professional</h3>
                        <p>Refined layout for professional applications.</p>
                        <div class="template-footer">
                            <span>₦3,000</span>
                            <span>Premium</span>
                        </div>
                    </div>
                </div>

                <div class="template-card">
                    <div class="template-preview">
                        <div class="mini-paper" style="display:grid;grid-template-columns:38% 62%;padding:0;">
                            <div style="background:#173c2b;padding:13px 8px;">
                                <div style="height:8px;background:#fff;width:80%;margin-bottom:12px;"></div>
                                <div style="height:4px;background:#8bb49b;margin:7px 0;"></div>
                                <div style="height:4px;background:#8bb49b;margin:7px 0;"></div>
                                <div style="height:4px;background:#8bb49b;margin:7px 0;"></div>
                                <div style="height:4px;background:#8bb49b;margin:7px 0;"></div>
                            </div>
                            <div style="padding:13px 9px;">
                                <div class="mini-heading"></div>
                                <div class="mini-subheading"></div>
                                <div class="mini-green"></div>
                                <div class="mini-bar wide"></div>
                                <div class="mini-bar mid"></div>
                                <div class="mini-green"></div>
                                <div class="mini-bar wide"></div>
                                <div class="mini-bar small"></div>
                            </div>
                        </div>
                    </div>
                    <div class="template-info">
                        <h3>Executive Design</h3>
                        <p>Distinctive two-column professional design.</p>
                        <div class="template-footer">
                            <span>₦3,000</span>
                            <span>Premium</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="how-section" id="how-it-works">
        <div class="container">

            <div class="section-heading">
                <div class="eyebrow">SIMPLE PROCESS</div>
                <h2>Your CV in three easy steps.</h2>
                <p>No complicated process. Just enter your details and create your CV.</p>
            </div>

            <div class="steps-grid">

                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3>Enter Your Details</h3>
                    <p>
                        Add your name, contact information, education,
                        work experience and skills.
                    </p>
                </div>

                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3>Choose Your CV Style</h3>
                    <p>
                        Use the free casual layout or select a premium
                        professional design.
                    </p>
                </div>

                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3>Download Your CV</h3>
                    <p>
                        Download your free CV immediately or pay for
                        your selected premium design.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- UPGRADE CTA -->
    <section class="upgrade-section" id="start">
        <div class="container">

            <div class="upgrade-box">
                <div>
                    <h2>Your next opportunity deserves a great CV.</h2>
                    <p>
                        Start with a free CV today. When you're ready to
                        stand out, upgrade to a professional design.
                        No account is needed to get started.
                    </p>
                </div>

                <a href="#pricing" class="btn btn-white">
                    Get Started →
                </a>
            </div>

        </div>
    </section>

    <!-- FAQ -->
    <section class="faq-section" id="faq">
        <div class="container">

            <div class="section-heading">
                <div class="eyebrow">GOT QUESTIONS?</div>
                <h2>Frequently asked questions.</h2>
                <p>Everything you need to know about creating your CV with NaijaCV.</p>
            </div>

            <div class="faq-list">

                <div class="faq-item">
                    <h3>Can I create a CV for free?</h3>
                    <p>
                        Yes. You can create a basic, clean CV and download
                        it as a PDF without paying.
                    </p>
                </div>

                <div class="faq-item">
                    <h3>Do I need to register before creating a CV?</h3>
                    <p>
                        No. Our guest CV creation option will allow you to
                        create and download a CV without creating an account.
                    </p>
                </div>

                <div class="faq-item">
                    <h3>What is the difference between free and premium CVs?</h3>
                    <p>
                        The free CV uses a simple casual layout. Premium CVs
                        offer more professionally designed templates and
                        improved presentation.
                    </p>
                </div>

                <div class="faq-item">
                    <h3>Can I pay without registering?</h3>
                    <p>
                        Yes. You will be able to choose a premium template,
                        enter your details, pay securely and download your CV
                        without creating an account.
                    </p>
                </div>

                <div class="faq-item">
                    <h3>Can I save my CV for future editing?</h3>
                    <p>
                        Creating an account will allow you to save and manage
                        your CVs and access additional career tools.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">

            <div class="footer-grid">

                <div class="footer-brand">
                    <a href="{{ route('home') }}" class="logo">
                        Naija<span>CV</span>.
                    </a>

                    <p class="footer-description">
                        Helping Nigerians create better CVs, present their
                        skills confidently and take the next step in their careers.
                    </p>
                </div>

                <div>
                    <h4>Explore</h4>
                    <div class="footer-links">
                        <a href="#how-it-works">How it works</a>
                        <a href="#templates">CV templates</a>
                        <a href="#pricing">Pricing</a>
                        <a href="#faq">FAQs</a>
                    </div>
                </div>

                <div>
                    <h4>Account</h4>
                    <div class="footer-links">
                        @guest
                            @if(Route::has('login'))
                                <a href="{{ route('login') }}">Log in</a>
                            @endif
                            @if(Route::has('register'))
                                <a href="{{ route('register') }}">Create account</a>
                            @endif
                        @else
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                            <a href="{{ route('cvs.index') }}">My CVs</a>
                        @endguest
                    </div>
                </div>

            </div>

            <div class="copyright">
                © {{ date('Y') }} NaijaCV. All rights reserved.
                Built to help you move forward.
            </div>

        </div>
    </footer>

</body>
</html>
