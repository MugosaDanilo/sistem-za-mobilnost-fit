<x-app-layout>
    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-2">
                <form action="{{ route('prepis.store') }}" method="POST" id="prepis-form">
                        @csrf

                    <div class="border p-4 rounded mb-3 bg-white">
                        <h4 class="font-semibold text-lg mb-3">INFO O STUDENTU</h4>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label for="student_id" class="block text-xs font-medium text-gray-700 mb-1">Ime i prezime:</label>
                                <select name="student_id" id="student_id" class="w-full border rounded px-2 py-1 text-sm" required>
                                    <option value="">Odaberite studenta</option>
                                @foreach($studenti as $student)
                                    <option value="{{ $student->id }}">{{ $student->ime }} {{ $student->prezime }} ({{ $student->br_indexa }})</option>
                                @endforeach
                            </select>
                        </div>

                            <div>
                                <label for="fakultet_id" class="block text-xs font-medium text-gray-700 mb-1">Dolazi sa:</label>
                                <select name="fakultet_id" id="fakultet_id" class="w-full border rounded px-2 py-1 text-sm" required>
                                    <option value="">Odaberite fakultet</option>
                                @foreach($fakulteti as $fakultet)
                                    @if(isset($fitFakultet) && $fitFakultet && $fitFakultet->id == $fakultet->id)
                                        <option value="{{ $fakultet->id }}" disabled>{{ $fakultet->naziv }}</option>
                                    @else
                                        <option value="{{ $fakultet->id }}">{{ $fakultet->naziv }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                            <div>
                                <label for="datum" class="block text-xs font-medium text-gray-700 mb-1">Datum:</label>
                                <input type="date" name="datum" id="datum" class="w-full border rounded px-2 py-1 text-sm" value="{{ old('datum', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>


                    <!-- Mačovanje tabela -->
                    <div class="mt-1 border p-6 rounded-lg shadow-sm bg-gray-50">
                        <h4 class="font-semibold text-lg mb-3 text-gray-800">Mačovanje</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse border border-gray-300 bg-white">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700 w-1/2">Strani Predmet</th>
                                        <th class="border border-gray-300 px-4 py-3 text-center text-sm font-semibold text-gray-700 w-24">ECTS</th>
                                        <th class="border-l-4 border-l-gray-500 border-r border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700 w-1/2">FIT Predmet</th>
                                        <th class="border border-gray-300 px-4 py-3 text-center text-sm font-semibold text-gray-700 w-24">ECTS</th>
                                    </tr>
                                </thead>
                                <tbody id="macovanje" class="divide-y divide-gray-200">
                                    <!-- Ovo će biti popunjeno dinamički kroz JavaScript -->
                                    <tr id="empty-row" class="hidden">
                                        <td colspan="4" class="border border-gray-300 px-4 py-8 text-center text-gray-500 italic">
                                            Nema uparenih predmeta. Dodajte predmete iznad da biste vidjeli mačovanje.
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-100">
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">
                                            <span>Ukupno Strani ECTS:</span>
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-800">
                                            <span id="ukupno-strani-macovanje-ects">0</span>
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">
                                            <span>Ukupno FIT ECTS:</span>
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-800">
                                            <span id="ukupno-fit-ects">0</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>



                    <div class="grid grid-cols-2 gap-4 mb-2">
                        <!-- Strani univerzitet i Trenutni predmet -->
                        <div class="border p-4 rounded bg-white">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h5 class="font-semibold mb-2 text-sm">Strani univerzitet</h5>
                                    <div id="listaStrani" class="border border-gray-300 rounded p-2 min-h-[200px] overflow-y-auto max-h-[300px] bg-white">
                                        <!-- Ovo će biti popunjeno dinamički kroz JavaScript -->
                                    </div>
                                </div>
                                <div>
                                    <h5 class="font-semibold mb-2 text-sm">Trenutni predmet</h5>
                                    <div id="trenutnis" class="border border-gray-400 min-h-[200px] p-2 rounded drop-zone bg-white overflow-y-auto max-h-[300px]"></div>
                                    <div class="mt-2 text-sm font-semibold border-t pt-2">
                                        <span>Ukupno: <span id="ukupno-strani-ects">0</span> ECTS</span>
                                    </div>
                                </div>
                            </div>
                            </div>

                        <!-- Domaći univerzitet i Trenutni predmet -->
                        <div class="border p-4 rounded bg-white">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h5 class="font-semibold mb-2 text-sm">Trenutni predmet</h5>
                                    <div id="trenutnid" class="border border-gray-400 min-h-[200px] p-2 rounded drop-zone bg-white overflow-y-auto max-h-[300px]"></div>
                                    <div class="mt-2 text-sm font-semibold border-t pt-2">
                                        <span>Ukupno: <span id="ukupno-domaci-ects">0</span> ECTS</span>
                                    </div>
                                    <div class="mt-3 flex gap-2">
                                        <button type="button" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm" id="automec-btn">Autome Sve</button>
                                        <button type="button" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm" id="automec-btn">Automeč</button>
                                        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm flex-1" id="potvrdi">Potvrdi</button>

                                    </div>
                                </div>
                                <div style="max-width: 250px;">
                                    <h5 class="font-semibold mb-2 text-sm">Domaći univerzitet</h5>
                                    <div id="listaDomaci" class="border border-gray-300 rounded p-2 min-h-[200px] overflow-y-auto max-h-[300px] bg-white">
                                        <!-- Ovo će biti popunjeno dinamički kroz JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden agreements container for form submission -->
                    <div id="agreements-container" style="display: none;">
                        <!-- Agreements will be added dynamically via JavaScript -->
                        </div>



                    <div class="flex justify-end mt-2">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Sačuvaj
                        </button>
                        </div>
                    </form>
            </div>
        </div>
    </div>

    <script>
        const allSubjects = @json($predmeti);
        const agreementsContainer = document.getElementById('agreements-container');
        const fakultetSelect = document.getElementById('fakultet_id');
        const macovanjeTable = document.getElementById('macovanje');
        
        // Store all matched pairs from macovanje table with batch tracking
        const macovanjePairs = [];
        let currentBatch = 0;

        function populateForeignSubjects(selectElement, facultyId) {
            selectElement.innerHTML = '<option value="">Odaberite strani predmet</option>';
            if (!facultyId) {
                selectElement.disabled = true;
                selectElement.innerHTML = '<option value="">Prvo odaberi fakultet</option>';
                return;
            }
            selectElement.disabled = false;
            
            const filteredSubjects = allSubjects.filter(subject => subject.fakultet_id == facultyId);
            
            filteredSubjects.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.id;
                option.textContent = `${subject.naziv} (${subject.ects} ECTS)`;
                selectElement.appendChild(option);
            });
        }

        function updateAllForeignSubjects() {
            const facultyId = fakultetSelect.value;
            const foreignSelects = document.querySelectorAll('.strani-predmet-select');
            foreignSelects.forEach(select => {
                const currentValue = select.value;
                populateForeignSubjects(select, facultyId);
                if (currentValue) {
                    let exists = false;
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].value == currentValue) {
                            exists = true;
                            break;
                        }
                    }
                    if (exists) {
                        select.value = currentValue;
                    }
                }
            });
        }

        function calculateTotalEcts(containerId) {
            const container = document.getElementById(containerId);
            const items = container.querySelectorAll('.dropped-item');
            let total = 0;
            items.forEach(item => {
                const ects = parseInt(item.dataset.ects) || 0;
                total += ects;
            });
            return total;
        }

        function updateTotalEcts() {
            const straniTotal = calculateTotalEcts('trenutnis');
            const domaciTotal = calculateTotalEcts('trenutnid');
            
            document.getElementById('ukupno-strani-ects').textContent = straniTotal;
            document.getElementById('ukupno-domaci-ects').textContent = domaciTotal;
        }

        function updateMacovanjeTableTotals() {
            let totalFitEcts = 0;
            let totalStraniEcts = 0;
            
            macovanjePairs.forEach(pair => {
                totalFitEcts += parseInt(pair.fitEcts) || 0;
                totalStraniEcts += parseInt(pair.straniEcts) || 0;
            });
            
            document.getElementById('ukupno-fit-ects').textContent = totalFitEcts;
            document.getElementById('ukupno-strani-macovanje-ects').textContent = totalStraniEcts;
        }

        function renderMacovanjeTable() {
            macovanjeTable.innerHTML = '';
            
            if (macovanjePairs.length === 0) {
                const emptyRow = document.createElement('tr');
                emptyRow.id = 'empty-row';
                emptyRow.innerHTML = `
                    <td colspan="4" class="border border-gray-300 px-4 py-8 text-center text-gray-500 italic">
                        Nema uparenih predmeta. Dodajte predmete iznad da biste vidjeli mačovanje.
                    </td>
                `;
                macovanjeTable.appendChild(emptyRow);
                updateMacovanjeTableTotals();
                return;
            }
            
            let lastBatch = -1;
            macovanjePairs.forEach((pair, index) => {
                // Add separator line if this is a new batch
                if (pair.batch !== undefined && pair.batch !== lastBatch && lastBatch !== -1) {
                    const separatorRow = document.createElement('tr');
                    separatorRow.className = 'border-separator';
                    separatorRow.innerHTML = `
                        <td colspan="4" class="border-t-4 border-t-gray-600 px-4 py-2"></td>
                    `;
                    macovanjeTable.appendChild(separatorRow);
                }
                lastBatch = pair.batch !== undefined ? pair.batch : 0;
                
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 transition-colors';
                tr.dataset.pairIndex = index;
                tr.innerHTML = `
                    <td class="border border-gray-300 px-4 py-3 text-sm text-gray-800 w-1/2">
                        ${pair.straniName}
                        <button type="button" class="ml-2 text-red-600 hover:text-red-900 text-xs" onclick="removeFromMacovanje(${index})">×</button>
                    </td>
                    <td class="border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-800">${pair.straniEcts}</td>
                    <td class="border-l-4 border-l-gray-500 border-r border-gray-300 px-4 py-3 text-sm text-gray-800 w-1/2">
                        ${pair.fitName}
                        <button type="button" class="ml-2 text-red-600 hover:text-red-900 text-xs" onclick="removeFromMacovanje(${index})">×</button>
                    </td>
                    <td class="border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-800">${pair.fitEcts}</td>
                `;
                macovanjeTable.appendChild(tr);
            });
            
            updateMacovanjeTableTotals();
        }

        // Function to remove pair from macovanje and restore to trenutni predmet
        window.removeFromMacovanje = function(index) {
            const pair = macovanjePairs[index];
            if (!pair) return;
            
            // Restore FIT predmet to listaDomaci if not already there
            const fitExists = document.querySelector(`#listaDomaci .drag-item[data-id="${pair.fitId}"]`);
            if (!fitExists) {
                const fitRow = document.createElement('div');
                fitRow.className = 'drag-item border border-gray-300 mb-1 rounded bg-white cursor-move hover:bg-gray-100 transition overflow-hidden';
                fitRow.draggable = true;
                fitRow.dataset.id = pair.fitId;
                fitRow.dataset.name = pair.fitName;
                fitRow.dataset.ects = pair.fitEcts;
                fitRow.innerHTML = `
                    <div class="flex">
                        <div class="flex-1 p-2 text-xs">${pair.fitName}</div>
                        <div class="border-l border-gray-300 px-2 py-2 text-xs font-semibold bg-gray-200 w-12 text-center">${pair.fitEcts}</div>
                    </div>
                `;
                listaDomaci.appendChild(fitRow);
            }
            
            // Restore strani predmet to listaStrani if not already there
            const straniExists = document.querySelector(`#listaStrani .drag-item[data-id="${pair.straniId}"]`);
            if (!straniExists) {
                const straniRow = document.createElement('div');
                straniRow.className = 'drag-item border border-gray-300 mb-1 rounded bg-white cursor-move hover:bg-gray-100 transition overflow-hidden';
                straniRow.draggable = true;
                straniRow.dataset.id = pair.straniId;
                straniRow.dataset.name = pair.straniName;
                straniRow.dataset.ects = pair.straniEcts;
                straniRow.innerHTML = `
                    <div class="flex">
                        <div class="flex-1 p-2 text-xs">${pair.straniName}</div>
                        <div class="border-l border-gray-300 px-2 py-2 text-xs font-semibold bg-gray-200 w-12 text-center">${pair.straniEcts}</div>
                    </div>
                `;
                listaStrani.appendChild(straniRow);
            }
            
            // Remove from macovanjePairs
            macovanjePairs.splice(index, 1);
            renderMacovanjeTable();
        };

        fakultetSelect.addEventListener('change', updateAllForeignSubjects);

        // Popunjavanje lista stranih i domacih predmeta
        const listaStrani = document.getElementById('listaStrani');
        const listaDomaci = document.getElementById('listaDomaci');
        
        // Filtriranje stranih predmeta prema izabranom fakultetu
        function populateStraniList() {
            const facultyId = fakultetSelect.value;
            listaStrani.innerHTML = '';
            
            if (!facultyId) {
                listaStrani.innerHTML = '<p class="text-gray-500 text-xs italic p-2">Prvo odaberi fakultet</p>';
                return;
            }
            
            const straniSubjects = allSubjects.filter(subject => subject.fakultet_id == facultyId);
            straniSubjects.forEach(subject => {
                // Check if this item is already in trenutnis
                const alreadyDropped = document.querySelector(`#trenutnis .dropped-item[data-id="${subject.id}"]`);
                if (alreadyDropped) return; // Skip if already dropped
                
            const row = document.createElement('div');
                row.className = 'drag-item border border-gray-300 mb-1 rounded bg-white cursor-move hover:bg-gray-100 transition overflow-hidden';
                row.draggable = true;
                row.dataset.id = subject.id;
                row.dataset.name = subject.naziv;
                row.dataset.ects = subject.ects;
            row.innerHTML = `
                    <div class="flex">
                        <div class="flex-1 p-2 text-xs">${subject.naziv}</div>
                        <div class="border-l border-gray-300 px-2 py-2 text-xs font-semibold bg-gray-200 w-12 text-center">${subject.ects}</div>
                </div>
                `;
                listaStrani.appendChild(row);
            });
        }
        
        // Popunjavanje domaće liste
        function populateDomaciList() {
            listaDomaci.innerHTML = '';
            allSubjects.forEach(subject => {
                // Check if this item is already in trenutnid
                const alreadyDropped = document.querySelector(`#trenutnid .dropped-item[data-id="${subject.id}"]`);
                if (alreadyDropped) return; // Skip if already dropped
                
                const row = document.createElement('div');
                row.className = 'drag-item border border-gray-300 mb-1 rounded bg-white cursor-move hover:bg-gray-100 transition overflow-hidden';
                row.draggable = true;
                row.dataset.id = subject.id;
                row.dataset.name = subject.naziv;
                row.dataset.ects = subject.ects;
                row.innerHTML = `
                    <div class="flex">
                        <div class="flex-1 p-2 text-xs">${subject.naziv}</div>
                        <div class="border-l border-gray-300 px-2 py-2 text-xs font-semibold bg-gray-200 w-12 text-center">${subject.ects}</div>
                </div>
                `;
                listaDomaci.appendChild(row);
            });
        }
        
        // Function to restore item to original list
        function restoreItemToOriginalList(item, isStrani) {
            const itemId = item.dataset.id;
            const itemName = item.dataset.name;
            const itemEcts = item.dataset.ects;
            
            if (isStrani) {
                // Restore to strani list
                const row = document.createElement('div');
                row.className = 'drag-item border border-gray-300 mb-1 rounded bg-white cursor-move hover:bg-gray-100 transition overflow-hidden';
                row.draggable = true;
                row.dataset.id = itemId;
                row.dataset.name = itemName;
                row.dataset.ects = itemEcts;
                row.innerHTML = `
                    <div class="flex">
                        <div class="flex-1 p-2 text-xs">${itemName}</div>
                        <div class="border-l border-gray-300 px-2 py-2 text-xs font-semibold bg-gray-200 w-12 text-center">${itemEcts}</div>
                    </div>
                `;
                listaStrani.appendChild(row);
            } else {
                // Restore to domaci list
                const row = document.createElement('div');
                row.className = 'drag-item border border-gray-300 mb-1 rounded bg-white cursor-move hover:bg-gray-100 transition overflow-hidden';
                row.draggable = true;
                row.dataset.id = itemId;
                row.dataset.name = itemName;
                row.dataset.ects = itemEcts;
                row.innerHTML = `
                    <div class="flex">
                        <div class="flex-1 p-2 text-xs">${itemName}</div>
                        <div class="border-l border-gray-300 px-2 py-2 text-xs font-semibold bg-gray-200 w-12 text-center">${itemEcts}</div>
                    </div>
                `;
                listaDomaci.appendChild(row);
            }
        }
        
        // Drag and drop 
        let draggedItem = null;
        let draggedFromList = null;
        const dropzones = document.querySelectorAll('.drop-zone');
        
        document.addEventListener('dragstart', function(e) {
            if (e.target.classList.contains('drag-item')) {
                draggedItem = e.target;
                draggedFromList = e.target.closest('#listaStrani') ? 'strani' : 
                                 e.target.closest('#listaDomaci') ? 'domaci' : null;
                e.target.style.opacity = '0.5';
            }
        });
        
        document.addEventListener('dragend', function(e) {
            if (e.target.classList.contains('drag-item')) {
                e.target.style.opacity = '1';
                draggedItem = null;
                draggedFromList = null;
            }
        });
        
        dropzones.forEach(zone => {
            zone.addEventListener('dragover', function(e) {
                e.preventDefault();
                zone.style.backgroundColor = '#e5e7eb';
            });
            
            zone.addEventListener('dragleave', function(e) {
                zone.style.backgroundColor = '#f9fafb';
            });
            
            zone.addEventListener('drop', function(e) {
                e.preventDefault();
                zone.style.backgroundColor = '#f9fafb';
                
                if (draggedItem && draggedFromList) {
                    // Check if item already exists in dropzone
                    const itemId = draggedItem.dataset.id;
                    const existingItem = zone.querySelector(`.dropped-item[data-id="${itemId}"]`);
                    if (existingItem) {
                        draggedItem = null;
                        draggedFromList = null;
                        return; // Item already exists
                    }
                    
                    const clonedItem = draggedItem.cloneNode(true);
                    clonedItem.style.opacity = '1';
                    clonedItem.draggable = false;
                    clonedItem.classList.remove('drag-item');
                    clonedItem.classList.add('dropped-item');
                    
                    // Remove button
                    const removeBtn = document.createElement('button');
                    removeBtn.textContent = 'X';
                    removeBtn.className = 'absolute top-0 right-0 bg-red-500 text-white w-5 h-5 rounded-full text-xs font-bold hover:bg-red-600';
                    removeBtn.style.fontSize = '10px';
                    removeBtn.onclick = function() {
                        // Restore to original list
                        const isStrani = zone.id === 'trenutnis';
                        restoreItemToOriginalList(clonedItem, isStrani);
                        clonedItem.remove();
                        updateTotalEcts();
                    };
                    
                    clonedItem.style.position = 'relative';
                    clonedItem.appendChild(removeBtn);
                    
                    zone.appendChild(clonedItem);
                    
                    // Remove from original list
                    draggedItem.remove();
                    
                    updateTotalEcts();
                }
                
                draggedItem = null;
                draggedFromList = null;
            });
        });
        
        // Potvrdi dugme - transfer to macovanje table and clear trenutni predmet
        document.getElementById('potvrdi').addEventListener('click', function() {
            const trenutnis = document.getElementById('trenutnis');
            const trenutnid = document.getElementById('trenutnid');
            
            const straniItems = Array.from(trenutnis.querySelectorAll('.dropped-item'));
            const domaciItems = Array.from(trenutnid.querySelectorAll('.dropped-item'));
            
            if (straniItems.length === 0 || domaciItems.length === 0) {
                alert('Morate imati predmete u oba trenutna predmeta!');
                return;
            }
            
            // Increment batch number for this group
            currentBatch++;
            
            // Create all combinations (many-to-many) with batch tracking
            straniItems.forEach(straniItem => {
                domaciItems.forEach(domaciItem => {
                    macovanjePairs.push({
                        fitId: domaciItem.dataset.id,
                        fitName: domaciItem.dataset.name,
                        fitEcts: domaciItem.dataset.ects,
                        straniId: straniItem.dataset.id,
                        straniName: straniItem.dataset.name,
                        straniEcts: straniItem.dataset.ects,
                        batch: currentBatch
                    });
                });
            });
            
            // Clear trenutni predmet tables and restore items to original lists
            straniItems.forEach(item => {
                restoreItemToOriginalList(item, true);
                item.remove();
            });
            
            domaciItems.forEach(item => {
                restoreItemToOriginalList(item, false);
                item.remove();
            });
            
            updateTotalEcts();
            renderMacovanjeTable();
        });
        
        // Automec functionality
        document.getElementById('automec-btn').addEventListener('click', async function() {
            const fakultetId = fakultetSelect.value;
            if (!fakultetId) {
                alert('Selektuj fakultet prvo');
                return;
            }

            const trenutnis = document.getElementById('trenutnis');
            const straniItems = Array.from(trenutnis.querySelectorAll('.dropped-item'));
            
            if (straniItems.length === 0) {
                alert('Selektuj bar jedan strani predmet u trenutnom predmetu');
                return;
            }

            const straniPredmetIds = straniItems.map(item => item.dataset.id);

            try {
                const response = await fetch('{{ route("prepis.automec-sugestija") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        strani_predmet_ids: straniPredmetIds,
                        fakultet_id: fakultetId
                    })
                });

                const suggestions = await response.json();
                const trenutnid = document.getElementById('trenutnid');
                
                // Clear existing items in trenutnid
                trenutnid.querySelectorAll('.dropped-item').forEach(item => {
                    restoreItemToOriginalList(item, false);
                    item.remove();
                });
                
                // Add suggested FIT predmeti to trenutnid
                Object.values(suggestions).forEach(suggestion => {
                    const fitSubject = allSubjects.find(s => s.id == suggestion.fit_predmet_id);
                    if (fitSubject) {
                        // Check if already in trenutnid
                        const exists = trenutnid.querySelector(`.dropped-item[data-id="${fitSubject.id}"]`);
                        if (exists) return;
                        
                        // Remove from listaDomaci if exists
                        const fromList = listaDomaci.querySelector(`.drag-item[data-id="${fitSubject.id}"]`);
                        if (fromList) fromList.remove();
                        
                        const clonedItem = document.createElement('div');
                        clonedItem.className = 'dropped-item border border-gray-300 mb-1 rounded bg-white relative';
                        clonedItem.dataset.id = fitSubject.id;
                        clonedItem.dataset.name = fitSubject.naziv;
                        clonedItem.dataset.ects = fitSubject.ects;
                        clonedItem.innerHTML = `
                            <div class="flex">
                                <div class="flex-1 p-2 text-xs">${fitSubject.naziv}</div>
                                <div class="border-l border-gray-300 px-2 py-2 text-xs font-semibold bg-gray-200 w-12 text-center">${fitSubject.ects}</div>
                            </div>
                            <button type="button" class="absolute top-0 right-0 bg-red-500 text-white w-5 h-5 rounded-full text-xs font-bold hover:bg-red-600" style="font-size: 10px;" onclick="this.closest('.dropped-item').remove(); updateTotalEcts();">X</button>
                        `;
                        trenutnid.appendChild(clonedItem);
                    }
                });
                
                updateTotalEcts();

                if (Object.keys(suggestions).length > 0) {
                    alert('Mačovanje pokrenuto! Predloženi FIT predmeti su dodati u trenutni predmet.');
                } else {
                    alert('Nema predmeta za mačovanje.');
                }
            } catch (error) {
                console.error('Greška:', error);
                alert('Greška prilikom mečovanja. Probaj opet.');
            }
        });

        // Form submission - save from macovanje table
        document.getElementById('prepis-form').addEventListener('submit', function(e) {
            if (macovanjePairs.length === 0) {
                e.preventDefault();
                alert('Morate imati najmanje jedno uparivanje u tabeli mačovanja!');
                return;
            }
            
            // Clear existing agreements
            agreementsContainer.innerHTML = '';
            
            // Create hidden inputs from macovanjePairs
            macovanjePairs.forEach((pair, index) => {
                const fitInput = document.createElement('input');
                fitInput.type = 'hidden';
                fitInput.name = `agreements[${index}][fit_predmet_id]`;
                fitInput.value = pair.fitId;
                agreementsContainer.appendChild(fitInput);
                
                const straniInput = document.createElement('input');
                straniInput.type = 'hidden';
                straniInput.name = `agreements[${index}][strani_predmet_id]`;
                straniInput.value = pair.straniId;
                agreementsContainer.appendChild(straniInput);
            });
        });
        
        // Inicijalizuj listu
        fakultetSelect.addEventListener('change', function() {
            populateStraniList();
            updateAllForeignSubjects();
        });
        
        // Handle student selection - auto-fill fakultet and polozeni predmeti
        const studentSelect = document.getElementById('student_id');
        studentSelect.addEventListener('change', async function() {
            const studentId = this.value;
            if (!studentId) {
                // Clear fakultet and listaStrani if no student selected
                fakultetSelect.value = '';
                listaStrani.innerHTML = '<p class="text-gray-500 text-xs italic p-2">Prvo odaberi fakultet</p>';
                return;
            }

            try {
                const response = await fetch(`{{ route('prepis.student-data', ['id' => ':id']) }}`.replace(':id', studentId), {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                
                // Auto-select fakultet if available
                if (data.fakultet_id) {
                    fakultetSelect.value = data.fakultet_id;
                    // Don't call populateStraniList() here - we'll populate with polozeni predmeti instead
                    updateAllForeignSubjects();
                }

                // Populate listaStrani with polozeni predmeti (only from selected fakultet if available)
                if (data.polozeni_predmeti && data.polozeni_predmeti.length > 0) {
                    listaStrani.innerHTML = '';
                    const selectedFakultetId = fakultetSelect.value;
                    
                    data.polozeni_predmeti.forEach(predmet => {
                        // If fakultet is selected, only show predmeti from that fakultet
                        if (selectedFakultetId && predmet.fakultet_id != selectedFakultetId) {
                            return; // Skip predmeti from other fakulteti
                        }
                        
                        // Check if this item is already in trenutnis
                        const alreadyDropped = document.querySelector(`#trenutnis .dropped-item[data-id="${predmet.id}"]`);
                        if (alreadyDropped) return; // Skip if already dropped
                        
                        const row = document.createElement('div');
                        row.className = 'drag-item border border-gray-300 mb-1 rounded bg-white cursor-move hover:bg-gray-100 transition overflow-hidden';
                        row.draggable = true;
                        row.dataset.id = predmet.id;
                        row.dataset.name = predmet.naziv;
                        row.dataset.ects = predmet.ects;
                        row.innerHTML = `
                            <div class="flex">
                                <div class="flex-1 p-2 text-xs">${predmet.naziv}</div>
                                <div class="border-l border-gray-300 px-2 py-2 text-xs font-semibold bg-gray-200 w-12 text-center">${predmet.ects}</div>
                            </div>
                        `;
                        listaStrani.appendChild(row);
                    });
                    
                    // Poruka ako nema predmeta
                    if (listaStrani.children.length === 0) {
                        listaStrani.innerHTML = '<p class="text-gray-500 text-xs italic p-2">Student nema položene ispite za ovaj fakultet</p>';
                    }
                } else {
                    // Ako nema polozenih ispita
                    listaStrani.innerHTML = '<p class="text-gray-500 text-xs italic p-2">Student nema položene ispite</p>';
                }
            } catch (error) {
                console.error('Error fetching student data:', error);
                alert('Greška pri učitavanju podataka o studentu');
            }
        });
        
        populateDomaciList();
        populateStraniList();
        updateTotalEcts();
        renderMacovanjeTable();
    </script>
</x-app-layout>
