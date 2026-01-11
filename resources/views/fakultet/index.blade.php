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
        <h1 class="text-3xl font-bold text-gray-900">Fakulteti</h1>
        <button id="addFacultyBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg">
            Dodaj Fakultet
        </button>
    </div>

    <div class="mb-4">
        <input type="text" id="searchFaculty" placeholder="Pretraži.." class="w-full max-w-md border-gray-300 rounded-lg px-4 py-2">
    </div>

    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th>Naziv</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Web</th>
                        <th>Univerzitet</th>
                        <th class="text-center">Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fakulteti as $f)
                    <tr data-search="{{ strtolower($f->naziv . ' ' . $f->email) }}">
                        <td>{{ $f->naziv }}</td>
                        <td>{{ $f->email }}</td>
                        <td>{{ $f->telefon }}</td>
                        <td>
                            @if($f->web)
                                <a href="{{ $f->web }}" target="_blank">{{ $f->web }}</a>
                            @endif
                        </td>
                        <td>{{ $f->univerzitet->naziv }}</td>
                        <td class="text-center">
                            <div class="flex justify-center space-x-2">
                                <button class="text-indigo-600 openEditModal px-3 py-1 rounded-md"
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
                                    <button type="submit" class="text-red-600 px-3 py-1 rounded-md">Obriši</button>
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
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 max-h-screen overflow-y-auto">
        <h2 class="text-xl font-semibold mb-4">Dodaj Fakultet</h2>

        <form action="{{ route('fakulteti.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label>Naziv</label>
                <input type="text" name="naziv" class="w-full border-gray-300 rounded-lg px-2 py-1" required>
            </div>

            <div class="mb-4">
                <label>Email</label>
                <input type="email" name="email" class="w-full border-gray-300 rounded-lg px-2 py-1" required>
            </div>

            <div class="mb-4">
                <label>Telefon</label>
                <input type="text" name="telefon" class="w-full border-gray-300 rounded-lg px-2 py-1" required>
            </div>

            <div class="mb-4">
                <label>Web</label>
                <input type="text" name="web" class="w-full border-gray-300 rounded-lg px-2 py-1">
            </div>

            <div class="mb-4">
                <label>Univerzitet</label>
                <select name="univerzitet_id" class="w-full border-gray-300 rounded-lg px-2 py-1" required>
                    <option value="">Izaberite univerzitet</option>
                    @foreach($univerziteti as $u)
                        <option value="{{ $u->id }}">{{ $u->naziv }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label>Uputstvo za ocjene (text)</label>
                <textarea name="uputstvo_za_ocjene" class="w-full border-gray-300 rounded-lg px-2 py-1"></textarea>
            </div>

            <div class="mb-4">
                <label>Upload dokument (txt, docx, pdf)</label>
                <input type="file" name="uputstvo_file" class="w-full border-gray-300 rounded-lg px-2 py-1">
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelAddModal" class="px-4 py-2 rounded-md border">Otkaži</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Sačuvaj</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Faculty Modal -->
<div id="editFacultyModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 max-h-screen overflow-y-auto">
        <h2 class="text-xl font-semibold mb-4">Izmijeni Fakultet</h2>

        <form id="editFacultyForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <input type="hidden" id="editFacultyId" name="id">

            <div class="mb-4">
                <label>Naziv</label>
                <input type="text" id="editName" name="naziv" class="w-full border-gray-300 rounded-lg px-2 py-1" required>
            </div>

            <div class="mb-4">
                <label>Email</label>
                <input type="email" id="editEmail" name="email" class="w-full border-gray-300 rounded-lg px-2 py-1" required>
            </div>

            <div class="mb-4">
                <label>Telefon</label>
                <input type="text" id="editPhone" name="telefon" class="w-full border-gray-300 rounded-lg px-2 py-1" required>
            </div>

            <div class="mb-4">
                <label>Web</label>
                <input type="text" id="editWeb" name="web" class="w-full border-gray-300 rounded-lg px-2 py-1">
            </div>

            <div class="mb-4">
                <label>Univerzitet</label>
                <select id="editUniversity" name="univerzitet_id" class="w-full border-gray-300 rounded-lg px-2 py-1" required>
                    <option value="">Izaberite univerzitet</option>
                    @foreach($univerziteti as $u)
                        <option value="{{ $u->id }}">{{ $u->naziv }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label>Uputstvo za ocjene (text)</label>
                <textarea id="editInstructions" name="uputstvo_za_ocjene" class="w-full border-gray-300 rounded-lg px-2 py-1"></textarea>
            </div>

            <div class="mb-4">
                <label>Upload dokument (txt, docx, pdf)</label>
                <input type="file" name="uputstvo_file" class="w-full border-gray-300 rounded-lg px-2 py-1">
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelEditModal" class="px-4 py-2 rounded-md border">Otkaži</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Sačuvaj</button>
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
    const rows = document.querySelectorAll('tr[data-search]');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        rows.forEach(row => row.style.display = row.getAttribute('data-search').includes(searchTerm) ? '' : 'none');
    });
});
</script>
</x-app-layout>
