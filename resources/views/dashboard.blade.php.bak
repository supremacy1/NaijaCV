<x-app-layout>
    <style>
        .naija-dashboard {
            background: #f8fbf9;
            min-height: 100vh;
            color: #14251c;
        }

        .naija-sidebar {
            background: #10251a;
            color: white;
            min-height: 100vh;
            width: 250px;
        }

        .naija-brand {
            font-size: 25px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .naija-brand span {
            color: #8bd2a7;
        }

        .side-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            border-radius: 10px;
            color: #bdc8dc;
            text-decoration: none;
            margin-bottom: 7px;
            transition: .2s;
        }

        .side-link:hover,
        .side-link.active {
            background: #1d4930;
            color: white;
        }

        .dashboard-card {
            background: white;
            border: 1px solid #edf0f5;
            border-radius: 15px;
            padding: 22px;
            box-shadow: 0 3px 12px rgba(15,23,42,.025);
        }

        .stat-number {
            font-size: 30px;
            font-weight: 800;
            color: #172033;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 21px;
        }

        .primary-btn {
            background: #087443;
            color: white;
            padding: 11px 18px;
            border-radius: 9px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
        }

        .primary-btn:hover {
            background: #075c36;
            color: white;
        }

        .section-title {
            font-size: 17px;
            font-weight: 750;
        }

        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-Applied {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-Interview {
            background: #fef3c7;
            color: #92400e;
        }

        .status-Offer {
            background: #dcfce7;
            color: #166534;
        }

        .status-Rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .table-clean th {
            color: #718096;
            font-size: 12px;
            font-weight: 600;
            padding: 13px;
        }

        .table-clean td {
            padding: 15px 13px;
            border-top: 1px solid #f0f2f6;
            font-size: 13px;
        }

        @media(max-width: 768px) {
            .naija-sidebar {
                display: none;
            }

            .stat-number {
                font-size: 24px;
            }
        }
    </style>

    <div class="naija-dashboard">

        <div class="flex">

            <!-- SIDEBAR -->
            <aside class="naija-sidebar p-5">

                <div class="naija-brand mb-2">
                    Naija<span>CV</span>
                </div>

                <p class="text-xs text-slate-400 mb-10">
                    YOUR CAREER STARTS HERE
                </p>

                <p class="text-xs text-slate-500 mb-3">
                    MAIN MENU
                </p>

                <a href="{{ route('dashboard') }}"
                   class="side-link active">
                    <span>▦</span> Dashboard
                </a>

                <a href="{{ route('cvs.index') }}"
                   class="side-link">
                    <span>📄</span> My CVs
                </a>

                <a href="{{ route('cvs.create') }}"
                   class="side-link">
                    <span>✚</span> Create CV
                </a>

                <a href="{{ route('jobs.index') }}"
                   class="side-link">
                    <span>💼</span> Job Tracker
                </a>

                <div class="border-t border-slate-700 my-7"></div>

                <p class="text-xs text-slate-500 mb-3">
                    ACCOUNT
                </p>

                <a href="{{ route('dashboard') }}"
                   class="side-link">
                    <span>⚙</span> Settings
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button class="side-link w-full text-left">
                        <span>↪</span> Logout
                    </button>
                </form>

                <div class="mt-16 p-4 rounded-xl bg-blue-950">
                    <p class="font-bold text-sm">Need a better CV?</p>

                    <p class="text-xs text-slate-300 mt-2">
                        Create a professional CV and stand out
                        from other applicants.
                    </p>

                    <a href="{{ route('cvs.create') }}"
                       class="block text-center bg-blue-600 rounded-lg p-2 mt-4 text-sm">
                        Create CV
                    </a>
                </div>

            </aside>

            <!-- MAIN CONTENT -->
            <main class="flex-1 p-5 md:p-9">

                <!-- TOP HEADER -->
                <div class="flex flex-wrap justify-between items-center gap-4 mb-9">

                    <div>
                        <p class="text-sm text-gray-500">
                            Welcome back,
                        </p>

                        <h1 class="text-2xl font-extrabold mt-1">
                            {{ Auth::user()->name }} 👋
                        </h1>

                        <p class="text-sm text-gray-500 mt-1">
                            Here's an overview of your career activities.
                        </p>
                    </div>

                    <a href="{{ route('cvs.create') }}"
                       class="primary-btn">
                        + Create New CV
                    </a>

                </div>

                <!-- SUCCESS MESSAGE -->
                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-5">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- STATISTICS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

                    <div class="dashboard-card">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-500 text-sm">My CVs</p>
                                <div class="stat-number">{{ $totalCvs }}</div>
                            </div>
                            <div class="stat-icon bg-blue-100 text-blue-600">
                                📄
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-3">
                            Total CV documents
                        </p>
                    </div>

                    <div class="dashboard-card">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-500 text-sm">Applications</p>
                                <div class="stat-number">{{ $totalJobs }}</div>
                            </div>
                            <div class="stat-icon bg-purple-100 text-purple-600">
                                💼
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-3">
                            Jobs applied for
                        </p>
                    </div>

                    <div class="dashboard-card">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-500 text-sm">Interviews</p>
                                <div class="stat-number">{{ $interviews }}</div>
                            </div>
                            <div class="stat-icon bg-yellow-100 text-yellow-600">
                                🎯
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-3">
                            Interview invitations
                        </p>
                    </div>

                    <div class="dashboard-card">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-500 text-sm">Job Offers</p>
                                <div class="stat-number">{{ $offers }}</div>
                            </div>
                            <div class="stat-icon bg-green-100 text-green-600">
                                🏆
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-3">
                            Successful applications
                        </p>
                    </div>

                </div>

                <!-- CAREER BANNER -->
                <div class="rounded-2xl p-6 md:p-8 mb-8 text-white"
                     style="background:linear-gradient(110deg,#075c36,#087443,#24a565)">

                    <div class="max-w-xl">
                        <p class="text-blue-100 text-sm font-semibold">
                            YOUR NEXT OPPORTUNITY STARTS HERE
                        </p>

                        <h2 class="text-2xl md:text-3xl font-extrabold mt-3">
                            Make your first impression count.
                        </h2>

                        <p class="text-blue-100 mt-3 text-sm">
                            Build a professional CV, apply for jobs and
                            track every step of your career journey.
                        </p>

                        <a href="{{ route('cvs.create') }}"
                           class="inline-block mt-5 bg-white text-blue-700 px-5 py-3 rounded-lg font-bold text-sm">
                            Build My CV →
                        </a>
                    </div>

                </div>

                <!-- CV AND JOB TABLES -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

                    <!-- RECENT CVS -->
                    <div class="dashboard-card">

                        <div class="flex justify-between items-center mb-5">

                            <h3 class="section-title">My Recent CVs</h3>

                            <a href="{{ route('cvs.index') }}"
                               class="text-blue-600 text-xs font-bold">
                                View All
                            </a>

                        </div>

                        @forelse($recentCvs as $cv)

                            <div class="flex justify-between items-center py-4 border-b border-gray-100 gap-3">

                                <div>
                                    <p class="font-bold text-sm">
                                        {{ $cv->title }}
                                    </p>

                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $cv->professional_title ?: 'Professional CV' }}
                                    </p>

                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $cv->created_at->format('d M Y') }}
                                    </p>
                                </div>

                                <a href="{{ route('cvs.edit', $cv) }}"
                                   class="text-blue-600 text-xs font-bold">
                                    Edit →
                                </a>

                            </div>

                        @empty

                            <div class="text-center py-10">
                                <div class="text-4xl mb-3">📄</div>

                                <p class="font-bold text-sm">
                                    No CV created yet
                                </p>

                                <p class="text-xs text-gray-500 mt-2">
                                    Create your first professional CV.
                                </p>

                                <a href="{{ route('cvs.create') }}"
                                   class="primary-btn mt-4">
                                    Create CV
                                </a>
                            </div>

                        @endforelse

                    </div>

                    <!-- RECENT JOB APPLICATIONS -->
                    <div class="dashboard-card">

                        <div class="flex justify-between items-center mb-5">

                            <h3 class="section-title">
                                Recent Applications
                            </h3>

                            <a href="{{ route('jobs.index') }}"
                               class="text-blue-600 text-xs font-bold">
                                View All
                            </a>

                        </div>

                        @forelse($recentJobs as $job)

                            <div class="flex justify-between items-center py-4 border-b border-gray-100 gap-3">

                                <div>
                                    <p class="font-bold text-sm">
                                        {{ $job->company }}
                                    </p>

                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $job->role }}
                                    </p>

                                    <p class="text-xs text-gray-400 mt-1">
{{ $job->date_applied ? \Carbon\Carbon::parse($job->date_applied)->format('d M Y') : 'Date not set' }}
                                    </p>
                                </div>

                                <span class="status status-{{ ucfirst(strtolower($job->status)) }}">
    {{ ucfirst($job->status) }}
</span>

                            </div>

                        @empty

                            <div class="text-center py-10">
                                <div class="text-4xl mb-3">💼</div>

                                <p class="font-bold text-sm">
                                    No job applications yet
                                </p>

                                <p class="text-xs text-gray-500 mt-2">
                                    Start tracking your applications.
                                </p>

                                <a href="{{ route('jobs.create') }}"
                                   class="primary-btn mt-4">
                                    Add Application
                                </a>
                            </div>

                        @endforelse

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="text-center text-xs text-gray-400 mt-10">
                    © {{ date('Y') }} NaijaCV.
                    Build your future, one application at a time.
                </div>

            </main>

        </div>

    </div>

</x-app-layout>
