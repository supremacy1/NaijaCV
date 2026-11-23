<x-app-layout>
    <div class="max-w-3xl mx-auto py-10 px-5">

        <a href="{{ route('jobs.index') }}"
           class="text-blue-600 text-sm">← Job Tracker</a>

        <h1 class="text-3xl font-extrabold mt-5">
            Add Job Application
        </h1>

        <p class="text-gray-500 mt-2 mb-8">
            Record a job you have applied for.
        </p>

        <div class="bg-white p-6 md:p-9 rounded-2xl shadow-sm">

            <form action="{{ route('jobs.store') }}" method="POST">
                @include('jobs.form')
            </form>

        </div>
    </div>
</x-app-layout>
