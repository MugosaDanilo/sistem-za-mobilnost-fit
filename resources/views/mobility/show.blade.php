<x-app-layout>
    <div class="py-10 max-w-6xl mx-auto px-6">
        
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detalji o mobilnosti</h1>
            <div class="flex gap-2">
            
                <button onclick="openDocumentsModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105">
                    Pregled dokumenata
                </button>
                <a href="{{ route('adminDashboardShow') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105">
                    Nazad na kontrolnu tablu
                </a>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-700">Informacije o studentu</h2>
                    <p class="mt-2 text-gray-600"><span class="font-medium">Ime:</span> {{ $mobilnost->student->ime }} {{ $mobilnost->student->prezime }}</p>
                    <p class="text-gray-600"><span class="font-medium">Indeks:</span> {{ $mobilnost->student->br_indexa }}</p>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-700">Informacije o mobilnosti</h2>
                    <p class="mt-2 text-gray-600"><span class="font-medium">Fakultet:</span> {{ $mobilnost->fakultet->naziv }}</p>
                    <p class="text-gray-600"><span class="font-medium">Tip mobilnosti:</span> {{ $mobilnost->tip_mobilnosti ?? '-' }}</p>
                    <p class="text-gray-600"><span class="font-medium">Period:</span> {{ \Carbon\Carbon::parse($mobilnost->datum_pocetka)->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($mobilnost->datum_kraja)->format('d.m.Y') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Learning agreements</h2>
            </div>
            <div class="overflow-x-auto">
                <form id="gradesForm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">FIT Predmeti</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Strani Predmeti</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ECTS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider flex items-center gap-2">
                                    Ocjena
                                    @if($mobilnost->fakultet && $mobilnost->fakultet->file_path)
                                        <a href="{{ route('fakulteti.download', $mobilnost->fakultet->id) }}" title="Preuzmi sistem ocjenjivanja" class="text-blue-500 hover:text-blue-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </a>
                                    @endif
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($mobilnost->learningAgreements as $la)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $la->fitPredmet ? $la->fitPredmet->naziv : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $la->straniPredmet ? $la->straniPredmet->naziv : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $la->straniPredmet ? $la->straniPredmet->ects : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <select name="grades[{{ $la->id }}]" {{ $mobilnost->is_locked ? 'disabled' : '' }}
                                            class="w-24 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm {{ $mobilnost->is_locked ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                                            <option value="">-</option>
                                            @foreach(['A', 'B', 'C', 'D', 'E', 'F'] as $grade)
                                                <option value="{{ $grade }}" {{ $la->ocjena == $grade ? 'selected' : '' }}>
                                                    {{ $grade }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end items-center gap-4">
                        <span id="saveMessage" class="text-sm font-medium"></span>
                        @if(!$mobilnost->is_locked)
                            
                            <button type="button" onclick="openDisableModal()" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105">
                                Zaključi unos
                            </button>

                            <button type="button" onclick="saveAllGrades()" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-lg transform transition hover:scale-105 duration-150 ease-in-out">
                                Sačuvaj sve ocjene
                            </button>

                        @endif

                           
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Disable Input Modal -->
    <div id="disableModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-1/3">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Zaključi ocjene</h3>
            </div>
            <div class="p-6">
                <p id="missingGradesWarning" class="text-red-600 font-bold mb-2 hidden">Ocjene nisu dodijeljene!</p>
                <p class="text-gray-600">Da li ste sigurni da želite da zaključite ove ocjene? Ova radnja je trajna i ne može se opozvati.</p>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-2">
                <button onclick="closeDisableModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105 duration-150 ease-in-out">Otkazi</button>
                <form action="{{ route('admin.mobility.lock', $mobilnost->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105 duration-150 ease-in-out">Zaključi unos</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Documents Modal -->
    <div id="documentsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75 backdrop-blur-sm hidden transition-all duration-300">
        <div class="bg-white rounded-xl shadow-2xl w-3/4 max-w-5xl transform transition-all scale-100 max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Dokumenti za mobilnost</h3>
                    <p class="text-gray-500 text-sm mt-1">Upravljanje dokumentima i kategorijama</p>
                </div>
                <div class="flex gap-3 items-center">
                    <!-- Filter -->
                    <select id="docFilter" onchange="renderDocuments()" class="bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-lg leading-tight focus:outline-none focus:bg-white focus:border-indigo-500 text-sm shadow-sm transition min-w-[160px]">
                        <option value="all">Sve kategorije</option>
                    </select>

                    <!-- Add Category Button -->
                    @if(!$mobilnost->is_locked)
                    <button onclick="openCategoryModal()" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2 rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Nova Kategorija</span>
                    </button>
                    @endif
                    
                    <!-- Close Button -->
                    <button onclick="closeDocumentsModal()" class="text-gray-400 hover:text-gray-600 transition p-2 rounded-full hover:bg-gray-200">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Modal Content (Scrollable) -->
            <div class="p-8 overflow-y-auto flex-1 bg-gray-50/50">
                
                <!-- Upload Section (Collapsible or Prominent) -->
                @if(!$mobilnost->is_locked)
                <div class="bg-white p-5 rounded-xl border border-dashed border-indigo-200 shadow-sm mb-8 flex flex-col md:flex-row gap-4 items-end md:items-center justify-between">
                    <div class="flex-1 w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dodaj novi dokument</label>
                        <div class="flex gap-3">
                            <select id="newDocCategory" class="block w-1/3 bg-gray-50 border border-gray-300 text-gray-700 py-2 px-3 rounded-lg leading-tight focus:outline-none focus:bg-white focus:border-indigo-500 text-sm">
                                <option value="">Izaberi kategoriju...</option>
                            </select>
                            <input type="file" id="newDocInput" class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100 cursor-pointer
                            "/>
                        </div>
                         <p id="uploadStatus" class="text-xs mt-2 pl-1 h-4"></p>
                    </div>
                    <button onclick="uploadDocument()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Upload
                    </button>
                </div>
                @endif

                <!-- Documents List -->
                <div id="documentsList" class="space-y-6">
                    <div class="flex justify-center items-center h-32 text-gray-400">
                        <svg class="animate-spin h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="ml-3 text-sm font-medium">Učitavanje dokumenata...</span>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-8 py-5 border-t border-gray-100 bg-gray-50 rounded-b-xl flex justify-between items-center">
                 <a href="{{ route('admin.mobility.documents.zip', $mobilnost->id) }}" class="flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-semibold text-sm transition group">
                    <div class="p-2 bg-indigo-100 rounded-lg group-hover:bg-indigo-200 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </div>
                    Preuzmi sve (ZIP)
                </a>
                <button onclick="closeDocumentsModal()" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold px-6 py-2 rounded-lg shadow-sm transition">
                    Zatvori
                </button>
            </div>
        </div>
    </div>

    <!-- Category Modal -->
    <div id="categoryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75 backdrop-blur-sm hidden transition-all duration-300">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all scale-100 p-0 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Nova kategorija</h3>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Naziv kategorije</label>
                <input type="text" id="newCategoryName" placeholder="npr. Potvrde, Ugovori..." class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm transition">
                <p id="catError" class="text-red-500 text-xs mt-2 min-h-[1rem]"></p>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                <button onclick="closeCategoryModal()" class="px-4 py-2 bg-white text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                    Otkaži
                </button>
                <button onclick="saveCategory()" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 shadow-md transition transform hover:scale-105">
                    Sačuvaj
                </button>
            </div>
        </div>
    </div>

    <script>
        function openDisableModal() {
            // Check for missing grades on client side
            const warning = document.getElementById('missingGradesWarning');
            let hasMissing = false;
            
            // Iterate all grade selects
            document.querySelectorAll('select[name^="grades["]').forEach(select => {
                if (!select.value) hasMissing = true;
            });

            if (hasMissing) {
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }

            document.getElementById('disableModal').classList.remove('hidden');
        }

        function closeDisableModal() {
            document.getElementById('disableModal').classList.add('hidden');
        }

        function openDocumentsModal() {
            document.getElementById('documentsModal').classList.remove('hidden');
            loadDocuments();
        }

        function closeDocumentsModal() {
            document.getElementById('documentsModal').classList.add('hidden');
        }

        let allDocuments = [];
        let allCategories = [];

        function loadDocuments() {
            const listDiv = document.getElementById('documentsList');
            const isLocked = {{ $mobilnost->is_locked ? 'true' : 'false' }};
            listDiv.innerHTML = '<p class="text-gray-500 text-sm">Loading...</p>';

            // Fetch categories first, then docs
            fetch('{{ route("admin.mobility.categories") }}')
                .then(res => res.json())
                .then(cats => {
                    allCategories = cats;
                    updateCategorySelects();
                    return fetch('{{ route("admin.mobility.documents", $mobilnost->id) }}');
                })
                .then(res => res.json())
                .then(docs => {
                    allDocuments = docs;
                    renderDocuments();
                })
                .catch(err => {
                    console.error(err);
                    listDiv.innerHTML = '<p class="text-red-500 text-sm">Greška pri učitavanju dokumenata.</p>';
                });
        }

        function updateCategorySelects() {
            const filter = document.getElementById('docFilter');
            const input = document.getElementById('newDocCategory');
            
            // Save current selection if possible
            const currentFilter = filter.value;
            
            if (!filter) return;

            filter.innerHTML = '<option value="all">Sve kategorije</option>';
            if (input) {
                input.innerHTML = '<option value="">Izaberi kategoriju...</option>';
            }

            allCategories.forEach(cat => {
                filter.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
                if (input) {
                    input.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
                }
            });

            if(currentFilter) filter.value = currentFilter;
        }

        function renderDocuments() {
            const listDiv = document.getElementById('documentsList');
            const filterVal = document.getElementById('docFilter').value;
            const isLocked = {{ $mobilnost->is_locked ? 'true' : 'false' }};
            
            listDiv.innerHTML = '';

            if (allDocuments.length === 0) {
                listDiv.innerHTML = '<div class="text-center py-8"><p class="text-gray-500 text-sm">Nema dokumenata.</p></div>';
                return;
            }

            let catsToShow = allCategories;
            if (filterVal !== 'all') {
                catsToShow = allCategories.filter(c => c.id == filterVal);
            }

            catsToShow.forEach(cat => {
                const docsInCat = allDocuments.filter(d => d.category_id == cat.id);
                
                // Construct Category Block
                const catBlock = document.createElement('div');
                catBlock.className = 'mb-8 animate-fade-in-up';
                
                const catHeader = document.createElement('h5');
                catHeader.className = 'flex items-center gap-3 text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 border-b border-gray-200 pb-2';
                catHeader.innerHTML = `
                    <span class="bg-indigo-100 text-indigo-700 py-1 px-2 rounded-md text-[10px] shadow-sm">${cat.name}</span>
                    <span class="flex-1 border-t border-gray-100"></span>
                `;
                catBlock.appendChild(catHeader);
                
                if (docsInCat.length === 0) {
                    const noDocs = document.createElement('div');
                    noDocs.className = 'text-center py-4 bg-gray-50 rounded-lg border border-dashed border-gray-200';
                    noDocs.innerHTML = '<span class="text-gray-400 text-xs italic">Nema dokumenata u ovoj kategoriji</span>';
                    catBlock.appendChild(noDocs);
                } else {
                    const grid = document.createElement('div');
                    grid.className = 'grid grid-cols-1 gap-3';

                    docsInCat.forEach(doc => {
                        const isDeletable = doc.type === 'other';
                        
                        const item = document.createElement('div');
                        item.className = 'group flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 shadow-sm hover:shadow-md hover:border-indigo-300 transition-all duration-200';
                        
                        const leftSide = document.createElement('div');
                        leftSide.className = 'flex items-center gap-4 overflow-hidden';
                        
                        // File Icon
                        const iconDiv = document.createElement('div');
                        iconDiv.className = 'bg-indigo-50 text-indigo-600 p-2.5 rounded-lg group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-200';
                        iconDiv.innerHTML = '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
                        
                        // Name
                        const nameSpan = document.createElement('span');
                        nameSpan.className = 'text-sm font-medium text-gray-700 group-hover:text-gray-900 truncate';
                        nameSpan.textContent = doc.name;
                        
                        leftSide.appendChild(iconDiv);
                        leftSide.appendChild(nameSpan);
                        item.appendChild(leftSide);
                        
                        const rightSide = document.createElement('div');
                        rightSide.className = 'flex items-center gap-2 pl-4';
                        
                        // Download Action
                        const downloadBtn = document.createElement('a');
                        downloadBtn.href = `/admin/mobility/${doc.mobilnost_id}/documents/${doc.id}/download`;
                        downloadBtn.target = '_blank';
                        downloadBtn.className = 'text-gray-400 hover:text-indigo-600 p-1.5 rounded-md hover:bg-indigo-50 transition-colors';
                        downloadBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>';
                        downloadBtn.title = 'Preuzmi';
                        
                        rightSide.appendChild(downloadBtn);

                        if (isDeletable && !isLocked) {
                            const delBtn = document.createElement('button');
                            delBtn.className = 'text-gray-400 hover:text-red-600 p-1.5 rounded-md hover:bg-red-50 transition-colors';
                            delBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>';
                            delBtn.title = 'Obriši';
                            delBtn.onclick = () => deleteDocument(doc.id);
                            rightSide.appendChild(delBtn);
                        } else if (!isDeletable) {
                             const lockIcon = document.createElement('span');
                             lockIcon.className = 'text-xs text-gray-400 font-medium px-2 py-0.5 bg-gray-100 rounded border border-gray-200';
                             lockIcon.textContent = 'SISTEM';
                             rightSide.appendChild(lockIcon);
                        }
                        
                        item.appendChild(rightSide);
                        grid.appendChild(item);
                    });
                     catBlock.appendChild(grid);
                }
                
                listDiv.appendChild(catBlock);
            });
        }
        
        function openCategoryModal() {
            document.getElementById('categoryModal').classList.remove('hidden');
        }

        function closeCategoryModal() {
            document.getElementById('categoryModal').classList.add('hidden');
        }

        function saveCategory() {
            const name = document.getElementById('newCategoryName').value;
            if (!name) return;

            fetch('{{ route("admin.mobility.categories.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name: name })
            })
            .then(res => {
                if (!res.ok) throw new Error('Failed to create category');
                return res.json();
            })
            .then(cat => {
                 allCategories.push(cat);
                 updateCategorySelects();
                 closeCategoryModal();
                 document.getElementById('newCategoryName').value = '';
            })
            .catch(err => {
                console.error(err);
                document.getElementById('catError').textContent = 'Greška pri kreiranju kategorije.';
            });
        }

        function uploadDocument() {
            const input = document.getElementById('newDocInput');
            const file = input.files[0];
            if (!file) return;

            const categoryId = document.getElementById('newDocCategory').value;
            
            if (!categoryId) {
                alert('Molimo izaberite kategoriju.');
                return;
            }

            // Status is already defined above? No, let's clean up the whole function start to be safe.
            const status = document.getElementById('uploadStatus');
            status.textContent = 'Uploading...';
            status.className = 'text-xs mt-1 text-gray-500';

            const formData = new FormData();
            formData.append('file', file);
            formData.append('category_id', categoryId);
            
            fetch('{{ route("admin.mobility.documents.upload", $mobilnost->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) throw new Error('Upload failed');
                return res.json();
            })
            .then(data => {
                status.textContent = 'Upload successful!';
                status.className = 'text-xs mt-1 text-green-600';
                input.value = ''; // clear input
                loadDocuments(); // reload list
                setTimeout(() => { status.textContent = ''; }, 2000);
            })
            .catch(err => {
                console.error(err);
                status.textContent = 'Upload failed.';
                status.className = 'text-xs mt-1 text-red-600';
            });
        }

        function deleteDocument(docId) {
            if (!confirm('Are you sure you want to delete this file?')) return;

            fetch(`/admin/mobility/{{ $mobilnost->id }}/documents/${docId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('Delete failed');
                return res.json();
            })
            .then(() => {
                loadDocuments();
            })
            .catch(err => {
                console.error(err);
                alert('Failed to delete document.');
            });
        }

        function saveAllGrades() {
            const form = document.getElementById('gradesForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const msgSpan = document.getElementById('saveMessage');
            
            // Convert formData to nested object structure for 'grades' array
            const grades = {};
            for (let [key, value] of formData.entries()) {
                const match = key.match(/grades\[(\d+)\]/);
                if (match) {
                    grades[match[1]] = value;
                }
            }

            msgSpan.textContent = 'Saving...';
            msgSpan.className = 'text-sm font-medium text-gray-500';

            fetch(`/admin/mobility/{{ $mobilnost->id }}/grades`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ grades: grades })
            })
            .then(res => res.json())
            .then(data => {
                msgSpan.textContent = 'Sve ocjene uspješno sačuvane!';
                msgSpan.className = 'text-sm font-medium text-green-600';
                setTimeout(() => {
                    msgSpan.textContent = '';
                }, 3000);
            })
            .catch(err => {
                msgSpan.textContent = 'Greška prilikom čuvanja ocjene.';
                msgSpan.className = 'text-sm font-medium text-red-600';
                console.error(err);
            });
        }
    </script>
</x-app-layout>
