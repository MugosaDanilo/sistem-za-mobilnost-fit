<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sistem za priznavanje ispita') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-6">
                    <a href="{{ route('prepis.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                        &larr; Nazad na listu prepisa
                    </a>
                </div>

                <h4 class="font-semibold text-lg mb-4">INFO O STUDENTU</h4>

                <form action="{{ route('prepis.store') }}" method="POST" id="prepis-form">
                    @csrf

                    <div class="grid grid-cols-3 gap-6 mb-6">
                        <div class="border p-4 rounded">
                            <div class="mb-4">
                                <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Ime i prezime:</label>
                                <select name="student_id" id="student_id" class="w-full border rounded p-2" required>
                                    <option value="">Odaberite studenta</option>
                                    @foreach($studenti as $student)
                                        <option value="{{ $student->id }}">{{ $student->ime }} {{ $student->prezime }} ({{ $student->br_indexa }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="fakultet_id" class="block text-sm font-medium text-gray-700 mb-2">Dolazi sa:</label>
                                <select name="fakultet_id" id="fakultet_id" class="w-full border rounded p-2" required>
                                    <option value="">Odaberite fakultet</option>
                                    @foreach($fakulteti as $fakultet)
                                        <option value="{{ $fakultet->id }}">{{ $fakultet->naziv }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="datum" class="block text-sm font-medium text-gray-700 mb-2">Datum:</label>
                                <input type="date" name="datum" id="datum" class="w-full border rounded p-2" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Povezivanje predmeta (više na više)</h3>
                            <button type="button" id="automec-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded text-sm">
                                Automeč
                            </button>
                        </div>

                        <div id="groups-container">
                            <div class="group-item border border-gray-300 rounded-lg p-4 mb-4 bg-gray-50" draggable="true">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-semibold text-gray-700">Grupa 1</h4>
                                    <button type="button" class="remove-group text-red-600 hover:text-red-900 font-bold">×</button>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">FIT Predmeti (može više)</label>
                                        <div class="border rounded p-2 min-h-[150px] bg-white" id="fit-dropzone-0">
                                            <div class="flex flex-wrap gap-2" id="fit-selected-0">
                                                <span class="text-gray-500 text-sm">Prevuci FIT predmete ovde ili klikni da izabereš</span>
                                            </div>
                                        </div>
                                        <select name="groups[0][fit_predmeti][]" class="group-fit-select mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" multiple size="5" style="display: none;">
                                            @foreach($predmeti as $predmet)
                                                <option value="{{ $predmet->id }}" data-naziv="{{ $predmet->naziv }}" data-ects="{{ $predmet->ects }}">{{ $predmet->naziv }} ({{ $predmet->ects }} ECTS)</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="toggle-fit-select mt-2 text-xs text-blue-600 hover:text-blue-900">Prikaži/sakrij listu</button>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Strani Predmeti (može više)</label>
                                        <div class="border rounded p-2 min-h-[150px] bg-white" id="strani-dropzone-0">
                                            <div class="flex flex-wrap gap-2" id="strani-selected-0">
                                                <span class="text-gray-500 text-sm">Izaberi fakultet prvo</span>
                                            </div>
                                        </div>
                                        <select name="groups[0][strani_predmeti][]" class="group-strani-select mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" multiple size="5" disabled style="display: none;">
                                            <option value="">Izaberi fakultet prvo</option>
                                        </select>
                                        <button type="button" class="toggle-strani-select mt-2 text-xs text-blue-600 hover:text-blue-900" disabled>Prikaži/sakrij listu</button>
                                    </div>
                                </div>
                                
                                <div class="mt-3 text-sm text-gray-600">
                                    <span class="group-combinations">0 kombinacija</span> će biti kreirano
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" id="add-group" class="mt-2 text-sm text-blue-600 hover:text-blue-900 font-semibold">
                            + Dodaj novu grupu uparivanja
                        </button>
                    </div>

                    <!-- Mačovanje tabela -->
                    <div class="mt-8 border p-4 rounded">
                        <h4 class="font-semibold mb-4">Mačovanje</h4>
                        <table class="w-full border">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="border p-2">FIT Predmet</th>
                                    <th class="border p-2">ECTS</th>
                                    <th class="border p-2">Strani Predmet</th>
                                    <th class="border p-2">ECTS</th>
                                </tr>
                            </thead>
                            <tbody id="macovanje">
                                <!-- Ovo će biti popunjeno dinamički kroz JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end mt-6">
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
        const groupsContainer = document.getElementById('groups-container');
        const fakultetSelect = document.getElementById('fakultet_id');
        const macovanjeTable = document.getElementById('macovanje');
        let groupCounter = 1;
        let draggedElement = null;

        function populateForeignSubjects(selectElement, facultyId) {
            selectElement.innerHTML = '';
            if (!facultyId) {
                selectElement.disabled = true;
                selectElement.innerHTML = '<option value="">Izaberi fakultet prvo</option>';
                return;
            }
            selectElement.disabled = false;
            
            const filteredSubjects = allSubjects.filter(subject => subject.fakultet_id == facultyId);
            
            filteredSubjects.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.id;
                option.setAttribute('data-naziv', subject.naziv);
                option.setAttribute('data-ects', subject.ects);
                option.textContent = `${subject.naziv} (${subject.ects} ECTS)`;
                selectElement.appendChild(option);
            });
        }

        function updateAllForeignSubjects() {
            const facultyId = fakultetSelect.value;
            document.querySelectorAll('.group-strani-select').forEach(select => {
                const groupIndex = select.closest('.group-item').getAttribute('data-group-index') || 
                                   Array.from(groupsContainer.children).indexOf(select.closest('.group-item'));
                populateForeignSubjects(select, facultyId);
                updateStraniSelected(groupIndex);
            });
            updateMacovanjeTable();
        }

        function updateFitSelected(groupIndex) {
            const select = document.querySelector(`.group-item[data-group-index="${groupIndex}"] .group-fit-select`) ||
                          Array.from(document.querySelectorAll('.group-item'))[groupIndex]?.querySelector('.group-fit-select');
            if (!select) return;
            
            const dropzone = document.getElementById(`fit-selected-${groupIndex}`);
            if (!dropzone) return;
            
            dropzone.innerHTML = '';
            const selected = Array.from(select.selectedOptions);
            
            if (selected.length === 0) {
                dropzone.innerHTML = '<span class="text-gray-500 text-sm">Prevuci FIT predmete ovde ili klikni da izabereš</span>';
                return;
            }
            
            selected.forEach(option => {
                const badge = document.createElement('span');
                badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800';
                badge.innerHTML = `${option.getAttribute('data-naziv')} (${option.getAttribute('data-ects')} ECTS) <button type="button" class="ml-2 text-blue-600 hover:text-blue-900 remove-fit" data-value="${option.value}">×</button>`;
                dropzone.appendChild(badge);
            });
            
            updateCombinations(groupIndex);
        }

        function updateStraniSelected(groupIndex) {
            const select = document.querySelector(`.group-item[data-group-index="${groupIndex}"] .group-strani-select`) ||
                          Array.from(document.querySelectorAll('.group-item'))[groupIndex]?.querySelector('.group-strani-select');
            if (!select) return;
            
            const dropzone = document.getElementById(`strani-selected-${groupIndex}`);
            if (!dropzone) return;
            
            dropzone.innerHTML = '';
            const selected = Array.from(select.selectedOptions);
            
            if (selected.length === 0) {
                dropzone.innerHTML = '<span class="text-gray-500 text-sm">Prevuci strane predmete ovde ili klikni da izabereš</span>';
                return;
            }
            
            selected.forEach(option => {
                const badge = document.createElement('span');
                badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800';
                badge.innerHTML = `${option.getAttribute('data-naziv')} (${option.getAttribute('data-ects')} ECTS) <button type="button" class="ml-2 text-green-600 hover:text-green-900 remove-strani" data-value="${option.value}">×</button>`;
                dropzone.appendChild(badge);
            });
            
            updateCombinations(groupIndex);
        }

        function updateCombinations(groupIndex) {
            const groupItem = document.querySelector(`.group-item[data-group-index="${groupIndex}"]`) ||
                            Array.from(document.querySelectorAll('.group-item'))[groupIndex];
            if (!groupItem) return;
            
            const fitSelect = groupItem.querySelector('.group-fit-select');
            const straniSelect = groupItem.querySelector('.group-strani-select');
            const combinationsSpan = groupItem.querySelector('.group-combinations');
            
            const fitCount = fitSelect.selectedOptions.length;
            const straniCount = straniSelect.selectedOptions.length;
            const total = fitCount * straniCount;
            
            combinationsSpan.textContent = `${total} kombinacija`;
            updateMacovanjeTable();
        }

        function updateMacovanjeTable() {
            macovanjeTable.innerHTML = '';
            let hasData = false;
            
            document.querySelectorAll('.group-item').forEach((groupItem, groupIndex) => {
                const fitSelect = groupItem.querySelector('.group-fit-select');
                const straniSelect = groupItem.querySelector('.group-strani-select');
                
                if (!fitSelect || !straniSelect) return;
                
                const fitOptions = Array.from(fitSelect.selectedOptions);
                const straniOptions = Array.from(straniSelect.selectedOptions);
                
                fitOptions.forEach(fitOption => {
                    straniOptions.forEach(straniOption => {
                        hasData = true;
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="border p-2">${fitOption.getAttribute('data-naziv') || fitOption.textContent.replace(/\s*\(\d+\s*ECTS\)/, '')}</td>
                            <td class="border p-2">${fitOption.getAttribute('data-ects') || ''}</td>
                            <td class="border p-2">${straniOption.getAttribute('data-naziv') || straniOption.textContent.replace(/\s*\(\d+\s*ECTS\)/, '')}</td>
                            <td class="border p-2">${straniOption.getAttribute('data-ects') || ''}</td>
                        `;
                        macovanjeTable.appendChild(tr);
                    });
                });
            });
            
            if (!hasData) {
                macovanjeTable.innerHTML = '<tr><td colspan="4" class="border p-2 text-center text-gray-500">Nema uparivanja</td></tr>';
            }
        }

        fakultetSelect.addEventListener('change', updateAllForeignSubjects);

        // Toggle select visibility
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('toggle-fit-select')) {
                const groupItem = e.target.closest('.group-item');
                const select = groupItem.querySelector('.group-fit-select');
                select.style.display = select.style.display === 'none' ? 'block' : 'none';
            }
            
            if (e.target.classList.contains('toggle-strani-select')) {
                const groupItem = e.target.closest('.group-item');
                const select = groupItem.querySelector('.group-strani-select');
                if (!select.disabled) {
                    select.style.display = select.style.display === 'none' ? 'block' : 'none';
                }
            }
            
            // Remove badges
            if (e.target.classList.contains('remove-fit')) {
                const value = e.target.getAttribute('data-value');
                const groupItem = e.target.closest('.group-item');
                const select = groupItem.querySelector('.group-fit-select');
                const option = Array.from(select.options).find(opt => opt.value === value);
                if (option) {
                    option.selected = false;
                    const groupIndex = Array.from(groupsContainer.children).indexOf(groupItem);
                    updateFitSelected(groupIndex);
                }
            }
            
            if (e.target.classList.contains('remove-strani')) {
                const value = e.target.getAttribute('data-value');
                const groupItem = e.target.closest('.group-item');
                const select = groupItem.querySelector('.group-strani-select');
                const option = Array.from(select.options).find(opt => opt.value === value);
                if (option) {
                    option.selected = false;
                    const groupIndex = Array.from(groupsContainer.children).indexOf(groupItem);
                    updateStraniSelected(groupIndex);
                }
            }
        });

        // Select change handlers
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('group-fit-select')) {
                const groupItem = e.target.closest('.group-item');
                const groupIndex = Array.from(groupsContainer.children).indexOf(groupItem);
                updateFitSelected(groupIndex);
            }
            
            if (e.target.classList.contains('group-strani-select')) {
                const groupItem = e.target.closest('.group-item');
                const groupIndex = Array.from(groupsContainer.children).indexOf(groupItem);
                updateStraniSelected(groupIndex);
            }
        });

        // Drag and drop
        document.addEventListener('dragstart', function(e) {
            if (e.target.tagName === 'OPTION') {
                draggedElement = e.target;
                e.dataTransfer.effectAllowed = 'copy';
            }
        });

        document.addEventListener('dragover', function(e) {
            if (draggedElement && (e.target.id.includes('fit-dropzone') || e.target.id.includes('strani-dropzone') || 
                e.target.closest('[id*="dropzone"]'))) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'copy';
            }
        });

        document.addEventListener('drop', function(e) {
            e.preventDefault();
            if (!draggedElement) return;
            
            const dropzone = e.target.closest('[id*="dropzone"]') || e.target;
            if (!dropzone || !dropzone.id) return;
            
            const isFit = dropzone.id.includes('fit-dropzone');
            const groupIndex = parseInt(dropzone.id.match(/\d+/)?.[0] || '0');
            const groupItem = Array.from(groupsContainer.children)[groupIndex];
            
            if (!groupItem) return;
            
            const select = isFit ? 
                groupItem.querySelector('.group-fit-select') : 
                groupItem.querySelector('.group-strani-select');
            
            if (select && !select.disabled) {
                const option = Array.from(select.options).find(opt => opt.value === draggedElement.value);
                if (option && !option.selected) {
                    option.selected = true;
                    if (isFit) {
                        updateFitSelected(groupIndex);
                    } else {
                        updateStraniSelected(groupIndex);
                    }
                }
            }
            
            draggedElement = null;
        });

        // Add new group
        document.getElementById('add-group').addEventListener('click', function() {
            const groupHtml = `
                <div class="group-item border border-gray-300 rounded-lg p-4 mb-4 bg-gray-50" draggable="true" data-group-index="${groupCounter}">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-semibold text-gray-700">Grupa ${groupCounter + 1}</h4>
                        <button type="button" class="remove-group text-red-600 hover:text-red-900 font-bold">×</button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">FIT Predmeti (može više)</label>
                            <div class="border rounded p-2 min-h-[150px] bg-white" id="fit-dropzone-${groupCounter}">
                                <div class="flex flex-wrap gap-2" id="fit-selected-${groupCounter}">
                                    <span class="text-gray-500 text-sm">Prevuci FIT predmete ovde ili klikni da izabereš</span>
                                </div>
                            </div>
                            <select name="groups[${groupCounter}][fit_predmeti][]" class="group-fit-select mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" multiple size="5" style="display: none;">
                                @foreach($predmeti as $predmet)
                                    <option value="{{ $predmet->id }}" data-naziv="{{ $predmet->naziv }}" data-ects="{{ $predmet->ects }}">{{ $predmet->naziv }} ({{ $predmet->ects }} ECTS)</option>
                                @endforeach
                            </select>
                            <button type="button" class="toggle-fit-select mt-2 text-xs text-blue-600 hover:text-blue-900">Prikaži/sakrij listu</button>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Strani Predmeti (može više)</label>
                            <div class="border rounded p-2 min-h-[150px] bg-white" id="strani-dropzone-${groupCounter}">
                                <div class="flex flex-wrap gap-2" id="strani-selected-${groupCounter}">
                                    <span class="text-gray-500 text-sm">Izaberi fakultet prvo</span>
                                </div>
                            </div>
                            <select name="groups[${groupCounter}][strani_predmeti][]" class="group-strani-select mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" multiple size="5" disabled style="display: none;">
                                <option value="">Izaberi fakultet prvo</option>
                            </select>
                            <button type="button" class="toggle-strani-select mt-2 text-xs text-blue-600 hover:text-blue-900" disabled>Prikaži/sakrij listu</button>
                        </div>
                    </div>
                    
                    <div class="mt-3 text-sm text-gray-600">
                        <span class="group-combinations">0 kombinacija</span> će biti kreirano
                    </div>
                </div>
            `;
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = groupHtml;
            const newGroup = tempDiv.firstElementChild;
            groupsContainer.appendChild(newGroup);
            
            const newStraniSelect = newGroup.querySelector('.group-strani-select');
            if (fakultetSelect.value) {
                populateForeignSubjects(newStraniSelect, fakultetSelect.value);
                const toggleBtn = newGroup.querySelector('.toggle-strani-select');
                if (toggleBtn) toggleBtn.disabled = false;
            }
            
            groupCounter++;
            updateGroupNumbers();
        });

        // Remove group
        groupsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-group')) {
                const groupItem = e.target.closest('.group-item');
                if (groupsContainer.children.length > 1) {
                    groupItem.remove();
                    updateGroupNumbers();
                    updateMacovanjeTable();
                } else {
                    alert('Morate imati najmanje jednu grupu!');
                }
            }
        });

        function updateGroupNumbers() {
            document.querySelectorAll('.group-item').forEach((groupItem, index) => {
                groupItem.setAttribute('data-group-index', index);
                const title = groupItem.querySelector('h4');
                if (title) title.textContent = `Grupa ${index + 1}`;
            });
        }

        // Automec functionality
        document.getElementById('automec-btn').addEventListener('click', async function() {
            const fakultetId = fakultetSelect.value;
            if (!fakultetId) {
                alert('Selektuj fakultet prvo');
                return;
            }

            const allStraniPredmetIds = [];
            document.querySelectorAll('.group-strani-select').forEach(select => {
                Array.from(select.selectedOptions).forEach(option => {
                    if (option.value && !allStraniPredmetIds.includes(option.value)) {
                        allStraniPredmetIds.push(option.value);
                    }
                });
            });

            if (allStraniPredmetIds.length === 0) {
                alert('Selektuj bar jedan strani predmet u bilo kojoj grupi');
                return;
            }

            try {
                const response = await fetch('{{ route("prepis.automec-sugestija") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        strani_predmet_ids: allStraniPredmetIds,
                        fakultet_id: fakultetId
                    })
                });

                const suggestions = await response.json();
                const firstGroup = document.querySelector('.group-item');
                
                if (firstGroup) {
                    const fitSelect = firstGroup.querySelector('.group-fit-select');
                    Object.values(suggestions).forEach(suggestion => {
                        const option = Array.from(fitSelect.options).find(opt => opt.value == suggestion.fit_predmet_id);
                        if (option && !option.selected) {
                            option.selected = true;
                        }
                    });
                    const groupIndex = Array.from(groupsContainer.children).indexOf(firstGroup);
                    updateFitSelected(groupIndex);
                }

                if (Object.keys(suggestions).length > 0) {
                    alert('Mačovanje pokrenuto! Predloženi FIT predmeti su dodati u prvu grupu.');
                } else {
                    alert('Nema predmeta za mačovanje.');
                }
            } catch (error) {
                console.error('Greška:', error);
                alert('Greška prilikom mečovanja. Probaj opet.');
            }
        });

        // Form submission - convert groups to agreements format
        document.getElementById('prepis-form').addEventListener('submit', function(e) {
            const groups = [];
            let hasValidGroup = false;

            document.querySelectorAll('.group-item').forEach((groupItem) => {
                const fitSelect = groupItem.querySelector('.group-fit-select');
                const straniSelect = groupItem.querySelector('.group-strani-select');
                
                const fitIds = Array.from(fitSelect.selectedOptions).map(opt => opt.value).filter(v => v);
                const straniIds = Array.from(straniSelect.selectedOptions).map(opt => opt.value).filter(v => v);
                
                if (fitIds.length > 0 && straniIds.length > 0) {
                    hasValidGroup = true;
                    fitIds.forEach(fitId => {
                        straniIds.forEach(straniId => {
                            groups.push({
                                fit_predmet_id: fitId,
                                strani_predmet_id: straniId
                            });
                        });
                    });
                }
            });

            if (!hasValidGroup) {
                e.preventDefault();
                alert('Morate imati najmanje jednu grupu sa izabranim FIT i stranim predmetima!');
                return;
            }

            // Add hidden inputs with all combinations
            groups.forEach((agreement, index) => {
                const fitInput = document.createElement('input');
                fitInput.type = 'hidden';
                fitInput.name = `agreements[${index}][fit_predmet_id]`;
                fitInput.value = agreement.fit_predmet_id;
                this.appendChild(fitInput);

                const straniInput = document.createElement('input');
                straniInput.type = 'hidden';
                straniInput.name = `agreements[${index}][strani_predmet_id]`;
                straniInput.value = agreement.strani_predmet_id;
                this.appendChild(straniInput);
            });
        });

        // Initialize first group
        const firstGroup = document.querySelector('.group-item');
        if (firstGroup) {
            firstGroup.setAttribute('data-group-index', '0');
            updateFitSelected(0);
        }
    </script>
</x-app-layout>
