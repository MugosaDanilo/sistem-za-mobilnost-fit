<x-app-layout>
    <div class="pt-16 max-w-7xl mx-auto px-6">

        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 space-y-4 md:space-y-0">
            <h1 class="text-3xl font-bold text-gray-900">Mobility Dashboard</h1>
        </div>

        <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Mobility Overview</h2>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $mobilnosti->count() }} Total</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Faculty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($mobilnosti as $mobilnost)
                            <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                            {{ substr($mobilnost->student->ime, 0, 1) }}{{ substr($mobilnost->student->prezime, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $mobilnost->student->ime }} {{ $mobilnost->student->prezime }}</div>
                                            <div class="text-sm text-gray-500">{{ $mobilnost->student->br_indexa }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">{{ $mobilnost->fakultet->naziv }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($mobilnost->datum_pocetka)->format('d.m.Y') }} -
                                        {{ \Carbon\Carbon::parse($mobilnost->datum_kraja)->format('d.m.Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex items-center justify-end space-x-2">

                                    <!-- Details -->
                                    <a href="{{ route('admin.mobility.show', $mobilnost->id) }}"
                                       class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors">
                                        Details
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.mobility.destroy', $mobilnost->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors">
                                            Delete
                                        </button>
                                    </form>

                                    <!-- Tooltip / Modal -->
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = true"
                                                class="text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded-md transition-colors">
                                            Tooltip
                                        </button>

                                        <!-- Modal -->
                                        <div x-show="open" x-transition class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                                            <div @click.away="open = false" class="bg-white p-6 rounded-lg w-96 shadow-lg">
                                                <h3 class="text-lg font-semibold mb-2">Upload File</h3>
                                                <p class="text-sm text-gray-500 mb-4">Supported file types: <strong>.docx, .txt</strong></p>
                                                <form action="{{ route('admin.mobility.upload', $mobilnost->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="file" name="file" class="border p-2 w-full rounded mb-4" required>
                                                    <div class="flex justify-end space-x-2">
                                                        <button type="button" @click="open = false" class="px-3 py-1 rounded bg-gray-300 hover:bg-gray-400">Cancel</button>
                                                        <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700">Upload</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                    No mobility records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Alpine.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</x-app-layout>
