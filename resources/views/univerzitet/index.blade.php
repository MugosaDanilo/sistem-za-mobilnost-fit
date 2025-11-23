<x-app-layout>
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="py-10 max-w-6xl mx-auto px-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Universities</h1>
            <a href="{{ route('univerzitet.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg">
                Add University
            </a>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Name</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Country</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">City</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Email</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($univerziteti as $u)
                        <tr class="bg-white">
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $u->naziv }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $u->drzava }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $u->grad }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $u->email }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('univerzitet.edit', $u->id) }}" 
                                       class="bg-yellow-400 hover:bg-yellow-500 text-white text-sm px-3 py-1 rounded transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('univerzitet.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1 rounded transition">
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
