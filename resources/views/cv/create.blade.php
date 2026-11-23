<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 px-5">

        <a href="{{ route('dashboard') }}"
           class="text-blue-600 text-sm">← Dashboard</a>

        <div class="mt-5 mb-8">
            <h1 class="text-3xl font-extrabold">Create Your CV</h1>
            <p class="text-gray-500 mt-2">
                Enter your information to get started.
            </p>
        </div>

        <div class="bg-white shadow-sm rounded-2xl p-6 md:p-9">

            <form action="{{ route('cvs.store') }}" method="POST">
                @include('cv.form')
            </form>

        </div>
    </div>
</x-app-layout>
