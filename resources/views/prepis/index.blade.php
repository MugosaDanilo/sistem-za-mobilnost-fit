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
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Student</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Faculty</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Date</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($prepisi as $prepis)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                        {{ substr($prepis->student->ime, 0, 1) }}{{ substr($prepis->student->prezime, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $prepis->student->ime }} {{ $prepis->student->prezime }}</div>
                                        <div class="text-sm text-gray-500">{{ $prepis->student->br_indexa }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $prepis->fakultet->naziv }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $prepis->datum->format('d.m.Y') }}</td>
                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('prepis.show', $prepis->id) }}" class="hover:opacity-80 transition-opacity">
                                    @php
                                        $status = $prepis->derived_status;
                                        $colorClass = match($status) {
                                            'odobren' => 'bg-green-100 text-green-800',
                                            'odbijen' => 'bg-red-100 text-red-800',
                                            default => 'bg-yellow-100 text-yellow-800',
                                        };
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </a>
                            </td>
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
