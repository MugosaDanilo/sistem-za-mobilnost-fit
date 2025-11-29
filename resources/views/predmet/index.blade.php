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

    <div class="py-10 max-w-6xl mx-auto px-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Predmeti - {{ $fakultet->naziv }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('fakulteti.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded-lg">
                    Nazad
                </a>
                <button id="addSubjectBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg">
                    Dodaj Predmet
                </button>
            </div>
        </div>

        <div class="mb-4">
            <input 
                type="text" 
                id="searchSubject" 
                placeholder="Pretraži.." 
                class="w-full max-w-md border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2"
            >
        </div>

        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Naziv</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">ECTS</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Semestar</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Akcije</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($predmeti as $p)
                        <tr class="bg-white subject-row" data-search="{{ strtolower($p->naziv) }}">
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $p->naziv }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $p->ects }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $p->semestar }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-1 rounded-md openEditModal"
                                        data-id="{{ $p->id }}"
                                        data-naziv="{{ $p->naziv }}"
                                        data-ects="{{ $p->ects }}"
                                        data-semestar="{{ $p->semestar }}">
                                        Izmijeni
                                    </button>
                                    <form action="{{ route('predmeti.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Da li ste sigurni?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded-md">
                                            Obriši
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

    <!-- Add Subject Modal -->
    <div id="addSubjectModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative overflow-y-auto max-h-screen">
            <h2 class="text-xl font-semibold mb-4">Dodaj Predmet</h2>

            <form action="{{ route('predmeti.store') }}" method="POST">
                @csrf
                <input type="hidden" name="fakultet_id" value="{{ $fakultet->id }}">

                <div class="mb-4">
                    <label for="addName" class="block text-gray-700 font-medium mb-1">Naziv</label>
                    <input type="text" id="addName" name="naziv" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="addEcts" class="block text-gray-700 font-medium mb-1">ECTS</label>
                    <input type="number" id="addEcts" name="ects" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required min="1">
                </div>

                <div class="mb-4">
                    <label for="addSemester" class="block text-gray-700 font-medium mb-1">Semestar</label>
                    <input type="number" id="addSemester" name="semestar" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required min="1">
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelAddModal" class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-100">
                        Otkaži
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        Sačuvaj
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Subject Modal -->
    <div id="editSubjectModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative overflow-y-auto max-h-screen">
            <h2 class="text-xl font-semibold mb-4">Izmijeni Predmet</h2>

            <form id="editSubjectForm" method="POST">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="id" id="editSubjectId">
                <input type="hidden" name="fakultet_id" value="{{ $fakultet->id }}">

                <div class="mb-4">
                    <label for="editName" class="block text-gray-700 font-medium mb-1">Naziv</label>
                    <input type="text" id="editName" name="naziv" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="editEcts" class="block text-gray-700 font-medium mb-1">ECTS</label>
                    <input type="number" id="editEcts" name="ects" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required min="1">
                </div>

                <div class="mb-4">
                    <label for="editSemester" class="block text-gray-700 font-medium mb-1">Semestar</label>
                    <input type="number" id="editSemester" name="semestar" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required min="1">
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelEditModal" class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-100">
                        Otkaži
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        Sačuvaj Izmjene
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Add Modal Logic
        const addModal = document.getElementById('addSubjectModal');
        const addBtn = document.getElementById('addSubjectBtn');
        const cancelAdd = document.getElementById('cancelAddModal');

        addBtn.addEventListener('click', () => {
            addModal.classList.remove('hidden');
            addModal.classList.add('flex');
        });

        cancelAdd.addEventListener('click', () => {
            addModal.classList.add('hidden');
            addModal.classList.remove('flex');
        });

        // Edit Modal Logic
        const editModal = document.getElementById('editSubjectModal');
        const cancelEdit = document.getElementById('cancelEditModal');
        const editForm = document.getElementById('editSubjectForm');

        document.querySelectorAll('.openEditModal').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                document.getElementById('editSubjectId').value = id;
                document.getElementById('editName').value = button.getAttribute('data-naziv');
                document.getElementById('editEcts').value = button.getAttribute('data-ects');
                document.getElementById('editSemester').value = button.getAttribute('data-semestar');

                editForm.action = `{{ route('predmeti.store') }}/${id}`;
                editModal.classList.remove('hidden');
                editModal.classList.add('flex');
            });
        });

        cancelEdit.addEventListener('click', () => {
            editModal.classList.add('hidden');
            editModal.classList.remove('flex');
        });

        // Search Logic
        const searchInput = document.getElementById('searchSubject');
        const rows = document.querySelectorAll('.subject-row');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            rows.forEach(row => {
                const searchText = row.getAttribute('data-search');
                if (searchText.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    </script>
</x-app-layout>
