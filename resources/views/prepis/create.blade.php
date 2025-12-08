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
                                        <button type="button" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm" id="automecsve-btn">Automeč Sve</button>
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
    // ----- Glavna struktura za uparivanje -----
    const allSubjects = @json($predmeti);
    const agreementsContainer = document.getElementById('agreements-container');
    const fakultetSelect = document.getElementById('fakultet_id');
    const macovanjeTable = document.getElementById('macovanje');

    const macovanjePairs = []; // ovdje držimo sve parove
    let currentBatch = 0;      // opcionalno za grupisanje batch-a

    // ----- Popunjavanje stranih predmeta prema fakultetu -----
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
                if (exists) select.value = currentValue;
            }
        });
    }

    // ----- ECTS izračun -----
    function calculateTotalEcts(containerId) {
        const container = document.getElementById(containerId);
        const items = container.querySelectorAll('.dropped-item');
        let total = 0;
        items.forEach(item => {
            total += parseInt(item.dataset.ects) || 0;
        });
        return total;
    }

    function updateTotalEcts() {
        document.getElementById('ukupno-strani-ects').textContent = calculateTotalEcts('trenutnis');
        document.getElementById('ukupno-domaci-ects').textContent = calculateTotalEcts('trenutnid');
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

    // ----- Render macovanje tabele -----
    function renderMacovanjeTable() {
        macovanjeTable.innerHTML = '';

        let lastStraniId = null;
        let lastFitId = null;

        macovanjePairs.forEach((pair, index) => {
            const prikaziStrani = pair.straniId !== lastStraniId;
            const prikaziFit = pair.fitId !== lastFitId;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="border px-4 py-2">${prikaziStrani ? pair.straniName : ''}</td>
                <td class="border px-4 py-2 text-center">${prikaziStrani ? pair.straniEcts : ''}</td>
                <td class="border px-4 py-2">${prikaziFit ? pair.fitName : ''}</td>
                <td class="border px-4 py-2 text-center">${prikaziFit ? pair.fitEcts : ''}</td>
            `;
            macovanjeTable.appendChild(tr);

            lastStraniId = pair.straniId;
            lastFitId = pair.fitId;
        });

        updateMacovanjeTableTotals();
    }

    // ----- Restore item back to original list -----
    function restoreItemToOriginalList(item, isStrani) {
        const container = isStrani ? listaStrani : listaDomaci;
        const row = document.createElement('div');
        row.className = 'drag-item border border-gray-300 mb-1 rounded bg-white cursor-move hover:bg-gray-100 transition overflow-hidden';
        row.draggable = true;
        row.dataset.id = item.dataset.id;
        row.dataset.name = item.dataset.name;
        row.dataset.ects = item.dataset.ects;
        row.innerHTML = `
            <div class="flex">
                <div class="flex-1 p-2 text-xs">${item.dataset.name}</div>
                <div class="border-l border-gray-300 px-2 py-2 text-xs font-semibold bg-gray-200 w-12 text-center">${item.dataset.ects}</div>
            </div>
        `;
        container.appendChild(row);
    }

    // ----- Remove from macovanje -----
    window.removeFromMacovanje = function(index) {
        const pair = macovanjePairs[index];
        if (!pair) return;

        // Restore both sides
        const fitExists = listaDomaci.querySelector(`.drag-item[data-id="${pair.fitId}"]`);
        if (!fitExists) restoreItemToOriginalList({dataset:{id:pair.fitId,name:pair.fitName,ects:pair.fitEcts}}, false);

        const straniExists = listaStrani.querySelector(`.drag-item[data-id="${pair.straniId}"]`);
        if (!straniExists) restoreItemToOriginalList({dataset:{id:pair.straniId,name:pair.straniName,ects:pair.straniEcts}}, true);

        macovanjePairs.splice(index, 1);
        renderMacovanjeTable();
    };

    // ----- Drag & Drop -----
    let draggedItem = null;
    let draggedFromList = null;
    const dropzones = document.querySelectorAll('.drop-zone');

    document.addEventListener('dragstart', e => {
        if (e.target.classList.contains('drag-item')) {
            draggedItem = e.target;
            draggedFromList = e.target.closest('#listaStrani') ? 'strani' : 
                              e.target.closest('#listaDomaci') ? 'domaci' : null;
            e.target.style.opacity = '0.5';
        }
    });

    document.addEventListener('dragend', e => {
        if (e.target.classList.contains('drag-item')) {
            e.target.style.opacity = '1';
            draggedItem = null;
            draggedFromList = null;
        }
    });

    dropzones.forEach(zone => {
        zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.backgroundColor = '#e5e7eb'; });
        zone.addEventListener('dragleave', e => { zone.style.backgroundColor = '#f9fafb'; });
        zone.addEventListener('drop', e => {
            e.preventDefault();
            zone.style.backgroundColor = '#f9fafb';
            if (!draggedItem || !draggedFromList) return;

            const itemId = draggedItem.dataset.id;
            if (zone.querySelector(`.dropped-item[data-id="${itemId}"]`)) return;

            const clonedItem = draggedItem.cloneNode(true);
            clonedItem.draggable = false;
            clonedItem.classList.remove('drag-item');
            clonedItem.classList.add('dropped-item');

            const removeBtn = document.createElement('button');
            removeBtn.textContent = 'X';
            removeBtn.className = 'absolute top-0 right-0 bg-red-500 text-white w-5 h-5 rounded-full text-xs font-bold hover:bg-red-600';
            removeBtn.style.fontSize = '10px';
            removeBtn.onclick = () => { restoreItemToOriginalList(clonedItem, zone.id === 'trenutnis'); clonedItem.remove(); updateTotalEcts(); };

            clonedItem.style.position = 'relative';
            clonedItem.appendChild(removeBtn);
            zone.appendChild(clonedItem);
            draggedItem.remove();
            updateTotalEcts();

            draggedItem = null;
            draggedFromList = null;
        });
    });

    // ----- Potvrdi dugme -----
    document.getElementById('potvrdi').addEventListener('click', () => {
        const straniItems = Array.from(trenutnis.querySelectorAll('.dropped-item'));
        const domaciItems = Array.from(trenutnid.querySelectorAll('.dropped-item'));

        if (straniItems.length === 0 || domaciItems.length === 0) {
            alert('Morate imati predmete u oba trenutna predmeta!');
            return;
        }

        currentBatch++;

        // Create all combinations
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

        // Clear dropzones and restore items
        straniItems.forEach(item => { restoreItemToOriginalList(item, true); item.remove(); });
        domaciItems.forEach(item => { restoreItemToOriginalList(item, false); item.remove(); });

        updateTotalEcts();
        renderMacovanjeTable();
    });

    // ----- Initial populate -----
    populateDomaciList();
    populateStraniList();
    updateTotalEcts();
    renderMacovanjeTable();

    fakultetSelect.addEventListener('change', () => { populateStraniList(); updateAllForeignSubjects(); });

</script>

</x-app-layout>
