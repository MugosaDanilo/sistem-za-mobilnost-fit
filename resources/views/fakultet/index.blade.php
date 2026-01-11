<x-app-layout>
    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-800 p-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Whoops!</strong>
            <span class="block">There were some problems with your input:</span>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="py-10 max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Fakulteti</h1>
            <button id="addFacultyBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105">
                Dodaj Fakultet
            </button>
        </div>

        <div class="mb-4">
            <input 
                type="text" 
                id="searchFaculty" 
                placeholder="Pretraži.." 
                class="w-full max-w-md border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2"
            >
        </div>

        <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Lista Fakulteta</h2>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ count($fakulteti) }} Total</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="facultyTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Naziv</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Web</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Univerzitet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($fakulteti as $f)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out" data-search="{{ strtolower($f->naziv . ' ' . $f->email) }}">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $f->naziv }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $f->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $f->telefon }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if($f->web)
                                    <a href="{{ $f->web }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline">{{ $f->web }}</a>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $f->univerzitet->naziv }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-center">
                                <div class="flex justify-center space-x-2">
                                    <button class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors openEditModal"
                                        data-id="{{ $f->id }}"
                                        data-naziv="{{ $f->naziv }}"
                                        data-email="{{ $f->email }}"
                                        data-telefon="{{ $f->telefon }}"
                                        data-web="{{ $f->web }}"
                                        data-uputstvo="{{ $f->uputstvo_za_ocjene }}"
                                        data-univerzitet="{{ $f->univerzitet_id }}">
                                        Izmijeni
                                    </button>

                                    <form action="{{ route('fakulteti.destroy', $f->id) }}" method="POST" onsubmit="return confirm('Da li ste sigurni?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors">Obriši</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Faculty Modal -->
    <div id="addFacultyModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 max-h-[90vh] overflow-y-auto relative">
            <h2 class="text-xl font-semibold mb-4">Dodaj Fakultet</h2>

            <form action="{{ route('fakulteti.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Naziv</label>
                    <input type="text" name="naziv" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Email</label>
                    <input type="email" name="email" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Telefon</label>
                    <input type="text" name="telefon" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Web</label>
                    <input type="text" name="web" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Univerzitet</label>
                    <select name="univerzitet_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Izaberite univerzitet</option>
                        @foreach($univerziteti as $u)
                            <option value="{{ $u->id }}">{{ $u->naziv }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Uputstvo za ocjene (text)</label>
                    <textarea name="uputstvo_za_ocjene" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Upload dokument (txt, docx, pdf)</label>
                    <input type="file" name="uputstvo_file" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelAddModal" class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-100 shadow-lg transform transition hover:scale-105">Otkaži</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-lg transform transition hover:scale-105">Sačuvaj</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Faculty Modal -->
    <div id="editFacultyModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 max-h-[90vh] overflow-y-auto relative">
            <h2 class="text-xl font-semibold mb-4">Izmijeni Fakultet</h2>

            <form id="editFacultyForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" id="editFacultyId" name="id">

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Naziv</label>
                    <input type="text" id="editName" name="naziv" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Email</label>
                    <input type="email" id="editEmail" name="email" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Telefon</label>
                    <input type="text" id="editPhone" name="telefon" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Web</label>
                    <input type="text" id="editWeb" name="web" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Univerzitet</label>
                    <select id="editUniversity" name="univerzitet_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Izaberite univerzitet</option>
                        @foreach($univerziteti as $u)
                            <option value="{{ $u->id }}">{{ $u->naziv }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Uputstvo za ocjene (text)</label>
                    <textarea id="editInstructions" name="uputstvo_za_ocjene" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Upload dokument (txt, docx, pdf)</label>
                    <input type="file" name="uputstvo_file" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelEditModal" class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-100 shadow-lg transform transition hover:scale-105">Otkaži</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-lg transform transition hover:scale-105">Sačuvaj</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Add Modal
        const addModal = document.getElementById('addFacultyModal');
        const addBtn = document.getElementById('addFacultyBtn');
        const cancelAdd = document.getElementById('cancelAddModal');
        addBtn.addEventListener('click', () => { addModal.classList.remove('hidden'); addModal.classList.add('flex'); });
        cancelAdd.addEventListener('click', () => { addModal.classList.add('hidden'); addModal.classList.remove('flex'); });

        // Edit Modal
        const editModal = document.getElementById('editFacultyModal');
        const cancelEdit = document.getElementById('cancelEditModal');
        const editForm = document.getElementById('editFacultyForm');

        document.querySelectorAll('.openEditModal').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('editFacultyId').value = button.getAttribute('data-id');
                document.getElementById('editName').value = button.getAttribute('data-naziv');
                document.getElementById('editEmail').value = button.getAttribute('data-email');
                document.getElementById('editPhone').value = button.getAttribute('data-telefon');
                document.getElementById('editWeb').value = button.getAttribute('data-web');
                document.getElementById('editInstructions').value = button.getAttribute('data-uputstvo');
                document.getElementById('editUniversity').value = button.getAttribute('data-univerzitet');

                // ISPRAVLJENA RUTA ZA UPDATE
                editForm.action = `/admin/fakulteti/${button.getAttribute('data-id')}`;
                
                editModal.classList.remove('hidden'); editModal.classList.add('flex');
            });
        });

        cancelEdit.addEventListener('click', () => { editModal.classList.add('hidden'); editModal.classList.remove('flex'); });

        // Search
        const searchInput = document.getElementById('searchFaculty');
        const rows = document.querySelectorAll('#facultyTable tbody tr'); // Updated selector
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            rows.forEach(row => {
                const searchData = row.getAttribute('data-search');
                if (searchData && searchData.includes(searchTerm)) {
                     row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    </script>
</x-app-layout>
