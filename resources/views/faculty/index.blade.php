<x-app-layout>
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="py-10 max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Faculty</h1>

            <!-- Add Faculty Button -->
            <div x-data="{ openAdd: false }">
                <button @click="openAdd = true" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105">
                    Add Faculty
                </button>

                <!-- Add Faculty Modal -->
                <div x-show="openAdd" x-transition class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                    <div @click.away="openAdd = false" class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative overflow-y-auto max-h-screen">
                        <h2 class="text-xl font-semibold mb-4">Add Faculty</h2>
                        <form action="{{ route('faculty.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-gray-700 mb-1">Name</label>
                                <input type="text" name="name" class="w-full border rounded-lg px-3 py-2" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" class="w-full border rounded-lg px-3 py-2" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 mb-1">Phone</label>
                                <input type="text" name="phone" class="w-full border rounded-lg px-3 py-2" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 mb-1">Web</label>
                                <input type="text" name="web" class="w-full border rounded-lg px-3 py-2">
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 mb-1">University</label>
                                <select name="university_id" class="w-full border rounded-lg px-3 py-2" required>
                                    <option value="">Choose a university</option>
                                    @foreach($universities as $u)
                                        <option value="{{ $u->id }}">{{ $u->naziv }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 mb-1">Upload Instructions (.doc, .docx, .txt)</label>
                                <input type="file" name="instructions" accept=".doc,.docx,.txt" class="w-full border rounded-lg px-3 py-2" required>
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" @click="openAdd = false" class="px-4 py-2 border rounded-md hover:bg-gray-100">Quit</button>
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <input type="text" id="searchFaculty" placeholder="Search.." class="w-full max-w-md border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2">
        </div>

        <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">List of Faculties</h2>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ count($faculties) }} Total</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Web</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">University</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($faculties as $f)
                            <tr class="faculty-row hover:bg-gray-50 transition-colors duration-150 ease-in-out" data-search="{{ strtolower($f->name . ' ' . $f->email . ' ' . $f->univerzitet->naziv) }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $f->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $f->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $f->phone }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($f->web)
                                        <a href="{{ $f->web }}" target="_blank" class="text-blue-600 hover:underline">{{ $f->web }}</a>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $f->univerzitet->naziv }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium flex justify-center space-x-2">
                                    <div x-data="{ openEdit: false }">
                                        <button @click="openEdit = true" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors">
                                            Edit
                                        </button>

                                        <!-- Edit Modal -->
                                        <div x-show="openEdit" x-transition class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                                            <div @click.away="openEdit = false" class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative overflow-y-auto max-h-screen">
                                                <h2 class="text-xl font-semibold mb-4">Edit Faculty</h2>
                                                <form action="{{ route('faculty.update', $f->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="mb-4">
                                                        <label class="block text-gray-700 mb-1">Name</label>
                                                        <input type="text" name="name" value="{{ $f->name }}" class="w-full border rounded-lg px-3 py-2" required>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="block text-gray-700 mb-1">Email</label>
                                                        <input type="email" name="email" value="{{ $f->email }}" class="w-full border rounded-lg px-3 py-2" required>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="block text-gray-700 mb-1">Phone</label>
                                                        <input type="text" name="phone" value="{{ $f->phone }}" class="w-full border rounded-lg px-3 py-2" required>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="block text-gray-700 mb-1">Web</label>
                                                        <input type="text" name="web" value="{{ $f->web }}" class="w-full border rounded-lg px-3 py-2">
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="block text-gray-700 mb-1">University</label>
                                                        <select name="university_id" class="w-full border rounded-lg px-3 py-2" required>
                                                            <option value="">Choose a university</option>
                                                            @foreach($universities as $u)
                                                                <option value="{{ $u->id }}" @if($f->univerzitet_id == $u->id) selected @endif>{{ $u->naziv }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="block text-gray-700 mb-1">Upload Instructions (.doc, .docx, .txt)</label>
                                                        <input type="file" name="instructions" accept=".doc,.docx,.txt" class="w-full border rounded-lg px-3 py-2">
                                                    </div>
                                                    <div class="flex justify-end space-x-2">
                                                        <button type="button" @click="openEdit = false" class="px-4 py-2 border rounded-md hover:bg-gray-100">Quit</button>
                                                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <a href="{{ route('faculty.subjects.index', $f->id) }}" class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md transition-colors">
                                        Subjects
                                    </a>

                                    <form action="{{ route('faculty.destroy', $f->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchFaculty');
        const rows = document.querySelectorAll('.faculty-row');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            rows.forEach(row => {
                const searchText = row.getAttribute('data-search');
                row.style.display = searchText.includes(searchTerm) ? '' : 'none';
            });
        });
    });
    </script>
</x-app-layout>
