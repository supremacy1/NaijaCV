<x-app-layout>
    <div class="max-w-7xl mx-auto px-5 py-10">

        <div class="flex justify-between items-center flex-wrap gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold">My CVs</h1>
                <p class="text-gray-500 mt-2">
                    Manage your professional CV documents.
                </p>
            </div>

            <a href="{{ route('cvs.create') }}"
               class="bg-blue-600 text-white px-5 py-3 rounded-lg font-bold">
                + Create New CV
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-5">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

            @forelse($cvs as $cv)

                <div class="bg-white border rounded-2xl p-6 shadow-sm">

                    <div class="flex justify-between">
                        <div class="text-3xl">📄</div>

                        <span class="text-xs bg-blue-100 text-blue-700 rounded-full px-3 py-1">
                            {{ ucfirst($cv->template) }}
                        </span>
                    </div>

                    <h2 class="font-bold text-lg mt-5">
                        {{ $cv->title }}
                    </h2>

                    <p class="text-gray-500 text-sm mt-1">
                        {{ $cv->professional_title ?: 'Professional CV' }}
                    </p>

                    <div class="grid grid-cols-3 gap-2 mt-5 text-center text-xs">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <strong>{{ $cv->educations_count }}</strong>
                            <p>Education</p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-3">
                            <strong>{{ $cv->experiences_count }}</strong>
                            <p>Experience</p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-3">
                            <strong>{{ $cv->skills_count }}</strong>
                            <p>Skills</p>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-6">
                        <a href="{{ route('cvs.edit', $cv) }}"
                           class="flex-1 text-center bg-blue-600 text-white rounded-lg py-2 text-sm">
                            Edit CV
                        </a>

                        <form action="{{ route('cvs.destroy', $cv) }}"
                              method="POST"
                              onsubmit="return confirm('Delete this CV?')">
                            @csrf
                            @method('DELETE')

                            <button class="bg-red-50 text-red-600 rounded-lg px-3 py-2 text-sm">
                                Delete
                            </button>
                        </form>
                    </div>

                </div>

            @empty

                <div class="col-span-full text-center bg-white rounded-2xl p-12">
                    <div class="text-5xl">📄</div>
                    <h2 class="font-bold text-xl mt-4">
                        No CV documents yet
                    </h2>
                    <p class="text-gray-500 mt-2">
                        Create your first CV to get started.
                    </p>
                    <a href="{{ route('cvs.create') }}"
                       class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg mt-5">
                        Create My CV
                    </a>
                </div>

            @endforelse

        </div>

        <div class="mt-8">
            {{ $cvs->links() }}
        </div>

    </div>
</x-app-layout>
