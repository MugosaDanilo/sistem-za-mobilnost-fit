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
            <h1 class="text-2xl font-bold text-gray-800">Predmeti - {{ $user->name }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded-lg">
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
                    @foreach($user->predmeti as $p)
                        <tr class="bg-white subject-row" data-search="{{ strtolower($p->naziv) }}">
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $p->naziv }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $p->ects }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $p->semestar }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <form action="{{ route('users.subjects.destroy', ['id' => $user->id, 'predmet_id' => $p->id]) }}" method="POST" onsubmit="return confirm('Da li ste sigurni?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded-md">
                                            Ukloni
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

    <div id="addSubjectModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4">Dodaj Predmet</h2>

            <form action="{{ route('users.subjects.store', $user->id) }}" method="POST">
                @csrf
                
                <div class="mb-4 relative">
                    <label for="subjectSearchInput" class="block text-gray-700 font-medium mb-1">Pretraži Predmet</label>
                    <input type="text" id="subjectSearchInput" placeholder="Unesite naziv predmeta..." 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2" autocomplete="off">
                    <div id="subjectSearchResults" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-40 overflow-y-auto hidden"></div>
                    <input type="hidden" name="predmet_id" id="selectedSubjectId" required>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelAddModal" class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-100">
                        Otkaži
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        Dodaj
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
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

        const subjectSearchInput = document.getElementById('subjectSearchInput');
        const subjectSearchResults = document.getElementById('subjectSearchResults');
        const selectedSubjectId = document.getElementById('selectedSubjectId');
        const allSubjects = @json($predmeti);

        function filterAndShowSubjects(query) {
            const filtered = query === '' 
                ? allSubjects 
                : allSubjects.filter(s => s.naziv.toLowerCase().includes(query));

            subjectSearchResults.innerHTML = '';
            if (filtered.length > 0) {
                filtered.forEach(subject => {
                    const div = document.createElement('div');
                    div.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                    div.textContent = `${subject.naziv} (${subject.ects} ECTS)`;
                    div.onclick = () => {
                        subjectSearchInput.value = subject.naziv;
                        selectedSubjectId.value = subject.id;
                        subjectSearchResults.classList.add('hidden');
                    };
                    subjectSearchResults.appendChild(div);
                });
                subjectSearchResults.classList.remove('hidden');
            } else {
                subjectSearchResults.classList.add('hidden');
            }
        }

        subjectSearchInput.addEventListener('input', (e) => {
            filterAndShowSubjects(e.target.value.toLowerCase());
        });

        subjectSearchInput.addEventListener('focus', () => {
            filterAndShowSubjects(subjectSearchInput.value.toLowerCase());
        });

        document.addEventListener('click', (e) => {
            if (!subjectSearchInput.contains(e.target) && !subjectSearchResults.contains(e.target)) {
                subjectSearchResults.classList.add('hidden');
            }
        });
    });
    </script>
</x-app-layout>
