<x-app-layout>
    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-800 p-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Whoops!</strong>
            <span class="block">Postoji poblem sa unosima:</span>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="py-10 max-w-7xl mx-auto px-6">
        <div class="mb-6">
            <a href="{{ route('fakulteti.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                &larr; Nazad na upravljanje Fakultetima
            </a>
        </div>

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Predmeti - {{ $fakultet->naziv }}</h1>
            <div class="flex items-center space-x-2">
                <button id="addSubjectBtn"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Dodaj predmet
                </button>
                <button id="importSubjectBtn" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105">
                    Uvezi predmete
                </button>
            </div>
        </div>

        <!-- Import Modal -->
        <div id="importSubjectModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h2 class="text-xl font-semibold mb-4">Uvezi predmete iz Excela</h2>
                <form action="{{ route('fakulteti.predmeti.import', $fakultet->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-1">Nivo studija</label>
                        <div class="flex items-center space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="level" value="basic" checked class="form-radio text-blue-600">
                                <span class="ml-2">Osnovne Studije</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="level" value="master" class="form-radio text-blue-600">
                                <span class="ml-2">Master Studije</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                         <label class="block text-gray-700 font-medium mb-1">Upload .xlsx File</label>
                         <input type="file" name="file" accept=".xlsx, .xls" required class="w-full border p-2 rounded">
                         <p class="text-sm text-gray-500 mt-1">
                             @if(Str::contains($fakultet->naziv, ['FIT', 'Fakultet za informacione tehnologije']))
                                Expected table: Native sheet & English.
                             @else
                                Expected headers: 'Šifra predmeta', 'Naziv predmeta', 'ECTS', 'Semestar'.
                             @endif
                         </p>
                    </div>
                    
                    <div class="flex justify-end space-x-2">
                        <button type="button" id="cancelImportModal" class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-100 shadow-lg transform transition hover:scale-105">Otkaži</button>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow-lg transform transition hover:scale-105">Uvezi</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-4">
            <form action="{{ route('fakulteti.predmeti.index', $fakultet->id) }}" method="GET" class="w-full max-w-md">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Pretraži predmete po nazivu ili šifri..."
                        class="w-full pl-10 pr-10 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    <div class="absolute left-3 top-2.5 text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    @if(request('search'))
                        <a href="{{ route('fakulteti.predmeti.index', $fakultet->id) }}" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Lista Predmeta</h2>
                <span
                    class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $predmeti->total() }}
                    Ukupno</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Šifra</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Naziv</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ECTS</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Semestar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nivo Studija</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Radnje</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($predmeti as $p)
                            <tr class="subject-row hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">{{ $p->sifra_predmeta }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $p->naziv }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $p->ects }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $p->semestar }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $p->nivoStudija->naziv ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center space-x-2">
                                            <a href="{{ route('nastavne-liste.index', $p->id) }}"
                                               class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-3 py-1 rounded-md transition-colors"
                                               title="Nastavna Lista">
                                                Nastavna Lista
                                            </a>
                                            <button
                                                class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors openEditModal"
                                                data-id="{{ $p->id }}" data-sifra="{{ $p->sifra_predmeta }}" data-naziv="{{ $p->naziv }}" data-ects="{{ $p->ects }}"
                                                data-semestar="{{ $p->semestar }}" data-nivo="{{ $p->nivo_studija_id }}">
                                                Izmijeni
                                            </button>
                                        <form action="{{ route('predmeti.destroy', $p->id) }}" method="POST"
                                            onsubmit="return confirm('Da li ste sigurni koje odbrisati ovaj predmet?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors">
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
            @if($predmeti->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $predmeti->links() }}
                </div>
            @endif
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
                    <label for="addSifra" class="block text-gray-700 font-medium mb-1">Šifra Predmeta</label>
                    <input type="text" id="addSifra" name="sifra_predmeta"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>

                <div class="mb-4">
                    <label for="addName" class="block text-gray-700 font-medium mb-1">Naziv</label>
                    <input type="text" id="addName" name="naziv"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>

                <div class="mb-4">
                    <label for="addNivo" class="block text-gray-700 font-medium mb-1">Nivo Studija</label>
                    <select id="addNivo" name="nivo_studija_id"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                        <option value="">Odaberite nivo studija</option>
                        @foreach($nivoStudija as $nivo)
                            <option value="{{ $nivo->id }}">{{ $nivo->naziv }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="addEcts" class="block text-gray-700 font-medium mb-1">ECTS</label>
                    <input type="number" id="addEcts" name="ects"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required min="1">
                </div>

                <div class="mb-4">
                    <label for="addSemester" class="block text-gray-700 font-medium mb-1">Semestar</label>
                    <input type="number" id="addSemester" name="semestar"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required min="1">
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelAddModal"
                        class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-100 shadow-lg transform transition hover:scale-105">
                        Otkaži
                    </button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-lg transform transition hover:scale-105">
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
                    <label for="editSifra" class="block text-gray-700 font-medium mb-1">Šifra Predmeta</label>
                    <input type="text" id="editSifra" name="sifra_predmeta"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>

                <div class="mb-4">
                    <label for="editName" class="block text-gray-700 font-medium mb-1">Naziv</label>
                    <input type="text" id="editName" name="naziv"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>

                <div class="mb-4">
                    <label for="editNivo" class="block text-gray-700 font-medium mb-1">Nivo Studija</label>
                    <select id="editNivo" name="nivo_studija_id"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                        <option value="">Odaberite nivo studija</option>
                        @foreach($nivoStudija as $nivo)
                            <option value="{{ $nivo->id }}">{{ $nivo->naziv }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="editEcts" class="block text-gray-700 font-medium mb-1">ECTS</label>
                    <input type="number" id="editEcts" name="ects"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required min="1">
                </div>

                <div class="mb-4">
                    <label for="editSemester" class="block text-gray-700 font-medium mb-1">Semestar</label>
                    <input type="number" id="editSemester" name="semestar"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required min="1">
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelEditModal"
                        class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-100 shadow-lg transform transition hover:scale-105">
                        Otkaži
                    </button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-lg transform transition hover:scale-105">
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

            // Import Modal Logic
            const importModal = document.getElementById('importSubjectModal');
            const importBtn = document.getElementById('importSubjectBtn');
            const cancelImport = document.getElementById('cancelImportModal');

            if (importBtn) {
                importBtn.addEventListener('click', () => {
                    importModal.classList.remove('hidden');
                    importModal.classList.add('flex');
                });
            }

            cancelImport.addEventListener('click', () => {
                importModal.classList.add('hidden');
                importModal.classList.remove('flex');
            });

            // Edit Modal Logic
            const editModal = document.getElementById('editSubjectModal');
            const cancelEdit = document.getElementById('cancelEditModal');
            const editForm = document.getElementById('editSubjectForm');

            document.querySelectorAll('.openEditModal').forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.getAttribute('data-id');
                    document.getElementById('editSubjectId').value = id;
                    document.getElementById('editSifra').value = button.getAttribute('data-sifra');
                    document.getElementById('editName').value = button.getAttribute('data-naziv');
                    document.getElementById('editEcts').value = button.getAttribute('data-ects');
                    document.getElementById('editSemester').value = button.getAttribute('data-semestar');
                    document.getElementById('editNivo').value = button.getAttribute('data-nivo');

                    editForm.action = `{{ route('predmeti.store') }}/${id}`;
                    editModal.classList.remove('hidden');
                    editModal.classList.add('flex');
                });
            });

            cancelEdit.addEventListener('click', () => {
                editModal.classList.add('hidden');
                editModal.classList.remove('flex');
            });

            // Search Logic removed - now server side
        });
    </script>
</x-app-layout>