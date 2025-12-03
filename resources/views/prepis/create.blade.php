<x-app-layout>
    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-2">
                <form action="{{ route('prepis.store') }}" method="POST">
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
                                        <option value="{{ $fakultet->id }}">{{ $fakultet->naziv }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="datum" class="block text-xs font-medium text-gray-700 mb-1">Datum:</label>
                                <input type="date" name="datum" id="datum" class="w-full border rounded px-2 py-1 text-sm" required>
                            </div>
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
                                </div>
                            </div>
                        </div>

                        <!-- Domaći univerzitet i Trenutni predmet -->
                        <div class="border p-4 rounded bg-white">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h5 class="font-semibold mb-2 text-sm">Trenutni predmet</h5>
                                    <div id="trenutnid" class="border border-gray-400 min-h-[200px] p-2 rounded drop-zone bg-white overflow-y-auto max-h-[300px]"></div>
                                    <button type="button" class="mt-3 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm" id="potvrdi">Potvrdi</button>
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

                    <!-- Mačovanje tabela -->
                    <div class="mt-1 border p-6 rounded-lg shadow-sm bg-gray-50">
                        <h4 class="font-semibold text-lg mb-3 text-gray-800">Mačovanje</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse border border-gray-300 bg-white">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700">FIT Predmet</th>
                                        <th class="border border-gray-300 px-4 py-3 text-center text-sm font-semibold text-gray-700 w-24">ECTS</th>
                                        <th class="border-l-4 border-l-gray-500 border-r border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700">Strani Predmet</th>
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
                            </table>
                        </div>
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

        function updateMacovanjeTable() {
            macovanjeTable.innerHTML = '';
            const trenutnis = document.getElementById('trenutnis');
            const trenutnid = document.getElementById('trenutnid');
            
            const straniItems = Array.from(trenutnis.querySelectorAll('.dropped-item'));
            const domaciItems = Array.from(trenutnid.querySelectorAll('.dropped-item'));
            
            let hasData = false;
            const minLength = Math.min(straniItems.length, domaciItems.length);
            
            for (let i = 0; i < minLength; i++) {
                const straniItem = straniItems[i];
                const domaciItem = domaciItems[i];
                
                if (straniItem && domaciItem) {
                    hasData = true;
                    const straniName = straniItem.dataset.name || '';
                    const straniEcts = straniItem.dataset.ects || '';
                    const domaciName = domaciItem.dataset.name || '';
                    const domaciEcts = domaciItem.dataset.ects || '';
                    
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-gray-50 transition-colors';
                    tr.innerHTML = `
                        <td class="border border-gray-300 px-4 py-3 text-sm text-gray-800">${domaciName}</td>
                        <td class="border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-800">${domaciEcts}</td>
                        <td class="border-l-4 border-l-gray-500 border-r border-gray-300 px-4 py-3 text-sm text-gray-800">${straniName}</td>
                        <td class="border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-800">${straniEcts}</td>
                    `;
                    macovanjeTable.appendChild(tr);
                }
            }
            
            if (!hasData) {
                const emptyRow = document.createElement('tr');
                emptyRow.id = 'empty-row';
                emptyRow.innerHTML = `
                    <td colspan="4" class="border border-gray-300 px-4 py-8 text-center text-gray-500 italic">
                        Nema uparenih predmeta. Dodajte predmete iznad da biste vidjeli mačovanje.
                    </td>
                `;
                macovanjeTable.appendChild(emptyRow);
            }
        }

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
        
        // Popunjavanje domaće liste ovo je sa onim vasim id ijem
        function populateDomaciList() {
            listaDomaci.innerHTML = '';
            allSubjects.forEach(subject => {
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
        
        // Drag and drop 
        let draggedItem = null;
        const dropzones = document.querySelectorAll('.drop-zone');
        
        document.addEventListener('dragstart', function(e) {
            if (e.target.classList.contains('drag-item')) {
                draggedItem = e.target;
                e.target.style.opacity = '0.5';
            }
        });
        
        document.addEventListener('dragend', function(e) {
            if (e.target.classList.contains('drag-item')) {
                e.target.style.opacity = '1';
                draggedItem = null;
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
                
                if (draggedItem) {
                    const clonedItem = draggedItem.cloneNode(true);
                    clonedItem.style.opacity = '1';
                    clonedItem.draggable = false;
                    clonedItem.classList.remove('drag-item');
                    clonedItem.classList.add('dropped-item');
                    
                    // dugme za dodaj
                    const removeBtn = document.createElement('button');
                    removeBtn.textContent = 'X';
                    removeBtn.className = 'absolute top-0 right-0 bg-red-500 text-white w-5 h-5 rounded-full text-xs font-bold hover:bg-red-600';
                    removeBtn.style.fontSize = '10px';
                    removeBtn.onclick = function() {
                        clonedItem.remove();
                    };
                    
                    // relativno i apsolutno pozicioniranje
                    clonedItem.style.position = 'relative';
                    clonedItem.appendChild(removeBtn);
                    
                    zone.appendChild(clonedItem);
                }
            });
        });
        
        // Potvrdi dugme
        document.getElementById('potvrdi').addEventListener('click', function() {
            const trenutnis = document.getElementById('trenutnis');
            const trenutnid = document.getElementById('trenutnid');
            
            const straniItems = Array.from(trenutnis.querySelectorAll('.dropped-item'));
            const domaciItems = Array.from(trenutnid.querySelectorAll('.dropped-item'));
            
            // automatsko popunjavanje dogovora na osnovu stavki ovo malo doraditi
            if (straniItems.length > 0 && domaciItems.length > 0) {
                const minLength = Math.min(straniItems.length, domaciItems.length);
                agreementsContainer.innerHTML = '';
                
                for (let i = 0; i < minLength; i++) {
                    const straniId = straniItems[i].dataset.id;
                    const domaciId = domaciItems[i].dataset.id;
                    
                    const row = document.createElement('div');
                    row.className = 'agreement-row flex space-x-4 mb-2';
                    
                    // Kreiranje FIT opcija u listi
                    let fitOptions = '<option value="">Odaberite FIT predmet</option>';
                    allSubjects.forEach(subject => {
                        const selected = subject.id == domaciId ? 'selected' : '';
                        fitOptions += `<option value="${subject.id}" ${selected}>${subject.naziv} (${subject.ects} ECTS)</option>`;
                    });
                    
                    row.innerHTML = `
                        <div class="w-1/2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">FIT Predmet</label>
                            <select name="agreements[${i}][fit_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fit-predmet-select" required>
                                ${fitOptions}
                            </select>
                        </div>
                        <div class="w-1/2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Strani Predmet</label>
                            <select name="agreements[${i}][strani_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm strani-predmet-select" required>
                                <option value="">Odaberite strani predmet</option>
                            </select>
                        </div>
                    `;
                    agreementsContainer.appendChild(row);
                    
                    const straniSelect = row.querySelector('.strani-predmet-select');
                    populateForeignSubjects(straniSelect, fakultetSelect.value);
                    if (straniId) {
                        setTimeout(() => {
                            straniSelect.value = straniId;
                        }, 100);
                    }
                }
                
                // Updejtuj tabelu samo kada kliknemo dugme
                updateMacovanjeTable();
            }
        });
        
        // Inicijalizuj listu
        fakultetSelect.addEventListener('change', function() {
            populateStraniList();
            updateAllForeignSubjects();
        });
        
        populateDomaciList();
        populateStraniList();
    </script>
</x-app-layout>