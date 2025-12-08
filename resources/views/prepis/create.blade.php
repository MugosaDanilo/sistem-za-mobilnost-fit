<x-app-layout>
<div class="py-1">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-2">
            <form action="{{ route('prepis.store') }}" method="POST" id="prepis-form">
                @csrf

                <!-- Student info -->
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
                            <input type="date" name="datum" id="datum" class="w-full border rounded px-2 py-1 text-sm" value="{{ old('datum', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>

                <!-- Macovanje Table -->
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
                            <tbody id="macovanje" class="divide-y divide-gray-200"></tbody>
                            <tfoot class="bg-gray-100">
                                <tr>
                                    <td class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">Ukupno Strani ECTS:</td>
                                    <td class="border border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-800" id="ukupno-strani-macovanje-ects">0</td>
                                    <td class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">Ukupno FIT ECTS:</td>
                                    <td class="border border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-800" id="ukupno-fit-ects">0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Drag & Drop Section -->
                <div class="grid grid-cols-2 gap-4 mb-2">
                    <!-- Strani i Trenutni -->
                    <div class="border p-4 rounded bg-white">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h5 class="font-semibold mb-2 text-sm">Strani univerzitet</h5>
                                <div id="listaStrani" class="border border-gray-300 rounded p-2 min-h-[200px] overflow-y-auto max-h-[300px] bg-white"></div>
                            </div>
                            <div>
                                <h5 class="font-semibold mb-2 text-sm">Trenutni predmet</h5>
                                <div id="trenutnis" class="border border-gray-400 min-h-[200px] p-2 rounded drop-zone bg-white overflow-y-auto max-h-[300px]"></div>
                                <div class="mt-2 text-sm font-semibold border-t pt-2">Ukupno: <span id="ukupno-strani-ects">0</span> ECTS</div>
                            </div>
                        </div>
                    </div>

                    <!-- Domaći i Trenutni -->
                    <div class="border p-4 rounded bg-white">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h5 class="font-semibold mb-2 text-sm">Trenutni predmet</h5>
                                <div id="trenutnid" class="border border-gray-400 min-h-[200px] p-2 rounded drop-zone bg-white overflow-y-auto max-h-[300px]"></div>
                                <div class="mt-2 text-sm font-semibold border-t pt-2">Ukupno: <span id="ukupno-domaci-ects">0</span> ECTS</div>
                                <div class="mt-3 flex gap-2">
                                    <button type="button" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm" id="automecsve-btn">Automeč Sve</button>
                                    <button type="button" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm" id="automec-btn">Automeč</button>
                                    <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm flex-1" id="potvrdi">Potvrdi</button>
                                </div>
                            </div>
                            <div style="max-width: 250px;">
                                <h5 class="font-semibold mb-2 text-sm">Domaći univerzitet</h5>
                                <div id="listaDomaci" class="border border-gray-300 rounded p-2 min-h-[200px] overflow-y-auto max-h-[300px] bg-white"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden agreements for form submission -->
                <div id="agreements-container" style="display: none;"></div>

                <div class="flex justify-end mt-2">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Sačuvaj</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const allSubjects = @json($predmeti);
const agreementsContainer = document.getElementById('agreements-container');
const macovanjeTable = document.getElementById('macovanje');

// Store matched pairs with batch tracking
const macovanjePairs = [];
let currentBatch = 0;

// Dropzone i drag&drop
let draggedItem = null;
let draggedFromList = null;
const dropzones = document.querySelectorAll('.drop-zone');

function calculateTotalEcts(containerId) {
    const container = document.getElementById(containerId);
    const items = container.querySelectorAll('.dropped-item');
    let total = 0;
    items.forEach(item => total += parseInt(item.dataset.ects) || 0);
    return total;
}

function updateTotalEcts() {
    document.getElementById('ukupno-strani-ects').textContent = calculateTotalEcts('trenutnis');
    document.getElementById('ukupno-domaci-ects').textContent = calculateTotalEcts('trenutnid');
}

function renderMacovanjeTable() {
    macovanjeTable.innerHTML = '';
    let lastBatch = null;

    macovanjePairs.forEach((pair, index) => {
        if(pair.batch !== lastBatch && lastBatch !== null) {
            // Dodaj deblju liniju između grupa
            const separator = document.createElement('tr');
            separator.innerHTML = `<td colspan="4" class="border-t-4 border-gray-500"></td>`;
            macovanjeTable.appendChild(separator);
        }

        const tr = document.createElement('tr');
        // FIT prikazujemo samo prvi put po grupi (jedan na više)
        const prikaziFit = pair.batch !== lastBatch;
        tr.innerHTML = `
            <td class="border px-4 py-2">${pair.straniName}</td>
            <td class="border px-4 py-2 text-center">${pair.straniEcts}</td>
            <td class="border px-4 py-2">${prikaziFit ? pair.fitName : ''}</td>
            <td class="border px-4 py-2 text-center">${prikaziFit ? pair.fitEcts : ''}</td>
        `;
        macovanjeTable.appendChild(tr);
        lastBatch = pair.batch;
    });

    // Update totals
    let totalStrani = 0, totalFit = 0;
    macovanjePairs.forEach(pair => { totalStrani += parseInt(pair.straniEcts); totalFit += parseInt(pair.fitEcts); });
    document.getElementById('ukupno-strani-macovanje-ects').textContent = totalStrani;
    document.getElementById('ukupno-fit-ects').textContent = totalFit;
}

// Restore item to original list
function restoreItemToOriginalList(item, isStrani) {
    const container = isStrani ? document.getElementById('listaStrani') : document.getElementById('listaDomaci');
    const row = document.createElement('div');
    row.className = 'drag-item border border-gray-300 mb-1 rounded bg-white cursor-move hover:bg-gray-100 transition overflow-hidden';
    row.draggable = true;
    row.dataset.id = item.dataset.id;
    row.dataset.name = item.dataset.name;
    row.dataset.ects = item.dataset.ects;
    row.innerHTML = `<div class="flex"><div class="flex-1 p-2 text-xs">${item.dataset.name}</div><div class="border-l border-gray-300 px-2 py-2 text-xs font-semibold bg-gray-200 w-12 text-center">${item.dataset.ects}</div></div>`;
    container.appendChild(row);
}

// Drag & Drop
document.addEventListener('dragstart', e => {
    if(e.target.classList.contains('drag-item')) {
        draggedItem = e.target;
        draggedFromList = e.target.closest('#listaStrani') ? 'strani' : e.target.closest('#listaDomaci') ? 'domaci' : null;
        e.target.style.opacity = '0.5';
    }
});

document.addEventListener('dragend', e => {
    if(e.target.classList.contains('drag-item')) e.target.style.opacity = '1';
});

dropzones.forEach(zone => {
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.backgroundColor = '#e5e7eb'; });
    zone.addEventListener('dragleave', e => zone.style.backgroundColor = '#f9fafb');
    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.style.backgroundColor = '#f9fafb';
        if(!draggedItem || !draggedFromList) return;

        const itemId = draggedItem.dataset.id;
        if(zone.querySelector(`.dropped-item[data-id="${itemId}"]`)) return;

        const clonedItem = draggedItem.cloneNode(true);
        clonedItem.classList.remove('drag-item');
        clonedItem.classList.add('dropped-item');
        clonedItem.draggable = false;

        // Remove button
        const removeBtn = document.createElement('button');
        removeBtn.textContent = 'X';
        removeBtn.className = 'absolute top-0 right-0 bg-red-500 text-white w-5 h-5 rounded-full text-xs font-bold hover:bg-red-600';
        removeBtn.onclick = () => { restoreItemToOriginalList(clonedItem, zone.id==='trenutnis'); clonedItem.remove(); updateTotalEcts(); };
        clonedItem.style.position = 'relative';
        clonedItem.appendChild(removeBtn);

        zone.appendChild(clonedItem);
        draggedItem.remove();
        updateTotalEcts();

        draggedItem = null; draggedFromList = null;
    });
});

// Popunjavanje liste Strani univerzitet po studentu
document.getElementById('student_id').addEventListener('change', async function() {
    const studentId = this.value;
    const listaStrani = document.getElementById('listaStrani');
    listaStrani.innerHTML = '';

    if(!studentId) return;

    try {
        const response = await fetch(`{{ route('prepis.student-data', ['id' => ':id']) }}`.replace(':id', studentId));
        const data = await response.json();
        // Auto-select fakultet
        if(data.fakultet_id) document.getElementById('fakultet_id').value = data.fakultet_id;

        if(data.polozeni_predmeti && data.polozeni_predmeti.length>0){
            data.polozeni_predmeti.forEach(predmet => {
                const row = document.createElement('div');
                row.className = 'drag-item border border-gray-300 mb-1 rounded bg-white cursor-move hover:bg-gray-100 transition overflow-hidden';
                row.draggable = true;
                row.dataset.id = predmet.id;
                row.dataset.name = predmet.naziv;
                row.dataset.ects = predmet.ects;
                row.innerHTML = `<div class="flex"><div class="flex-1 p-2 text-xs">${predmet.naziv}</div><div class="border-l border-gray-300 px-2 py-2 text-xs font-semibold bg-gray-200 w-12 text-center">${predmet.ects}</div></div>`;
                listaStrani.appendChild(row);
            });
        } else {
            listaStrani.innerHTML = '<p class="text-gray-500 text-xs italic p-2">Student nema položenih predmeta</p>';
        }
    } catch(err) { console.error(err); alert('Greška pri učitavanju podataka o studentu'); }
});

// Potvrdi dugme
document.getElementById('potvrdi').addEventListener('click', () => {
    const straniItems = Array.from(document.getElementById('trenutnis').querySelectorAll('.dropped-item'));
    const domaciItems = Array.from(document.getElementById('trenutnid').querySelectorAll('.dropped-item'));
    if(straniItems.length===0 || domaciItems.length===0){ alert('Morate imati predmete u oba trenutna predmeta!'); return; }
    currentBatch++;
    straniItems.forEach(s => domaciItems.forEach(d => macovanjePairs.push({fitId:d.dataset.id, fitName:d.dataset.name, fitEcts:d.dataset.ects, straniId:s.dataset.id, straniName:s.dataset.name, straniEcts:s.dataset.ects, batch:currentBatch})));
    straniItems.forEach(i=>{ restoreItemToOriginalList(i,true); i.remove(); });
    domaciItems.forEach(i=>{ restoreItemToOriginalList(i,false); i.remove(); });
    updateTotalEcts();
    renderMacovanjeTable();
});

// Form submit
document.getElementById('prepis-form').addEventListener('submit', function(e){
    if(macovanjePairs.length===0){ e.preventDefault(); alert('Morate imati najmanje jedno uparivanje u tabeli mačovanja!'); return; }
    agreementsContainer.innerHTML='';
    macovanjePairs.forEach((pair,index)=>{
        const fitInput=document.createElement('input'); fitInput.type='hidden'; fitInput.name=`agreements[${index}][fit_predmet_id]`; fitInput.value=pair.fitId; agreementsContainer.appendChild(fitInput);
        const straniInput=document.createElement('input'); straniInput.type='hidden'; straniInput.name=`agreements[${index}][strani_predmet_id]`; straniInput.value=pair.straniId; agreementsContainer.appendChild(straniInput);
    });
});

// Inicijalizacija
updateTotalEcts();
renderMacovanjeTable();
</script>

</x-app-layout>

