<x-app-layout>
    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-800 p-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <div class="py-10 max-w-6xl mx-auto px-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Prepis Management</h1>
            <a href="{{ route('prepis.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg">
                Add Prepis
            </a>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">ID</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Student</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Index</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Faculty</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Date</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($prepisi as $prepis)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $prepis->id }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $prepis->student->ime }} {{ $prepis->student->prezime }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $prepis->student->br_indexa }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $prepis->fakultet->naziv }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $prepis->datum->format('d.m.Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $prepis->status }}</td>
                            <td class="px-4 py-3">
                                <div class="flex space-x-2">
                                    <a href="{{ route('prepis.edit', $prepis->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-1 rounded-md">
                                        Edit
                                    </a>
                                    <form action="{{ route('prepis.destroy', $prepis->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this prepis?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded-md">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
