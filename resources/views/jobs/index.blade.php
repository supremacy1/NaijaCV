<x-app-layout>
    <div class="max-w-7xl mx-auto px-5 py-10">

        <div class="flex justify-between items-center flex-wrap gap-4 mb-8">

            <div>
                <h1 class="text-3xl font-extrabold">
                    Job Application Tracker
                </h1>
                <p class="text-gray-500 mt-2">
                    Keep track of every job opportunity.
                </p>
            </div>

            <a href="{{ route('jobs.create') }}"
               class="bg-blue-600 text-white px-5 py-3 rounded-lg font-bold">
                + Add Application
            </a>

        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-5">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm overflow-x-auto">

            <table class="w-full text-left min-w-[700px]">

                <thead class="bg-gray-50 text-gray-500 text-sm">
                    <tr>
                        <th class="p-5">Company</th>
                        <th class="p-5">Position</th>
                        <th class="p-5">Date Applied</th>
                        <th class="p-5">Status</th>
                        <th class="p-5">Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($jobs as $job)

                        <tr class="border-t">

                            <td class="p-5 font-bold">
                                {{ $job->company }}
                            </td>

                            <td class="p-5 text-gray-600">
                                {{ $job->role }}
                            </td>

                            <td class="p-5 text-gray-500">
                                {{ $job->date_applied->format('d M Y') }}
                            </td>

                            <td class="p-5">
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    @if($job->status === 'Applied') bg-blue-100 text-blue-700
                                    @elseif($job->status === 'Interview') bg-yellow-100 text-yellow-700
                                    @elseif($job->status === 'Offer') bg-green-100 text-green-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ $job->status }}
                                </span>
                            </td>

                            <td class="p-5">
                                <div class="flex gap-3 items-center">

                                    <a href="{{ route('jobs.edit', $job) }}"
                                       class="text-blue-600 font-bold text-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('jobs.destroy', $job) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete this application?')">
                                        @csrf
                                        @method('DELETE')

                                        <button class="text-red-600 font-bold text-sm">
                                            Delete
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="p-12 text-center">
                                <div class="text-4xl mb-3">💼</div>

                                <h3 class="font-bold text-lg">
                                    No applications recorded
                                </h3>

                                <p class="text-gray-500 text-sm mt-2">
                                    Start tracking your job applications.
                                </p>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

            <div class="p-5">
                {{ $jobs->links() }}
            </div>

        </div>

    </div>
</x-app-layout>
