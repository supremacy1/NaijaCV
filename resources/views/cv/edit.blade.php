<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 px-5">

        <a href="{{ route('cvs.index') }}"
           class="text-blue-600 text-sm">← My CVs</a>

        <h1 class="text-3xl font-extrabold mt-5 mb-2">
            Edit CV
        </h1>

        <p class="text-gray-500 mb-8">
            Update your professional information.
        </p>

        <div class="bg-white shadow-sm rounded-2xl p-6 md:p-9">

            <form action="{{ route('cvs.update', $cv) }}" method="POST">
                @method('PUT')
                @include('cv.form')
            </form>

        </div>

    </div>
</x-app-layout>
