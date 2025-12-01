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

                <form action="{{ route('prepis.store') }}" method="POST">
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
                        <h3 class="text-lg font-medium mb-2">Povezivanje predmeta</h3>
                        <div id="agreements-container">
                            <div class="agreement-row flex space-x-4 mb-2">
                                <div class="w-1/2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">FIT Predmet</label>
                                    <select name="agreements[0][fit_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fit-predmet-select" required>
                                        <option value="">Odaberite FIT predmet</option>
                                        @foreach($predmeti as $predmet)
                                            <option value="{{ $predmet->id }}">{{ $predmet->naziv }} ({{ $predmet->ects }} ECTS)</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-1/2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Strani Predmet</label>
                                    <select name="agreements[0][strani_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm strani-predmet-select" required disabled>
                                        <option value="">Prvo odaberite fakultet</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-agreement" class="mt-2 text-sm text-blue-600 hover:text-blue-900">+ Dodaj još jedan par predmeta</button>
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
        const agreementsContainer = document.getElementById('agreements-container');
        const fakultetSelect = document.getElementById('fakultet_id');
        const macovanjeTable = document.getElementById('macovanje');

        function populateForeignSubjects(selectElement, facultyId) {
            selectElement.innerHTML = '<option value="">Odaberite strani predmet</option>';
            if (!facultyId) {
                selectElement.disabled = true;
                selectElement.innerHTML = '<option value="">Prvo odaberite fakultet</option>';
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
            updateMacovanjeTable();
        }

        function updateMacovanjeTable() {
            macovanjeTable.innerHTML = '';
            const rows = document.querySelectorAll('.agreement-row');
            
            rows.forEach((row, index) => {
                const fitSelect = row.querySelector('.fit-predmet-select');
                const straniSelect = row.querySelector('.strani-predmet-select');
                
                if (fitSelect.value && straniSelect.value) {
                    const fitOption = fitSelect.options[fitSelect.selectedIndex];
                    const straniOption = straniSelect.options[straniSelect.selectedIndex];
                    
                    const fitText = fitOption.textContent;
                    const straniText = straniOption.textContent;
                    
                    const fitEcts = fitText.match(/\((\d+)\s*ECTS\)/)?.[1] || '';
                    const straniEcts = straniText.match(/\((\d+)\s*ECTS\)/)?.[1] || '';
                    
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="border p-2">${fitText.replace(/\s*\(\d+\s*ECTS\)/, '')}</td>
                        <td class="border p-2">${fitEcts}</td>
                        <td class="border p-2">${straniText.replace(/\s*\(\d+\s*ECTS\)/, '')}</td>
                        <td class="border p-2">${straniEcts}</td>
                    `;
                    macovanjeTable.appendChild(tr);
                }
            });
        }

        fakultetSelect.addEventListener('change', updateAllForeignSubjects);

        document.getElementById('add-agreement').addEventListener('click', function() {
            const index = agreementsContainer.children.length;
            const row = document.createElement('div');
            row.className = 'agreement-row flex space-x-4 mb-2';
            row.innerHTML = `
                <div class="w-1/2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">FIT Predmet</label>
                    <select name="agreements[${index}][fit_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fit-predmet-select" required>
                        <option value="">Odaberite FIT predmet</option>
                        @foreach($predmeti as $predmet)
                            <option value="{{ $predmet->id }}">{{ $predmet->naziv }} ({{ $predmet->ects }} ECTS)</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-1/2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Strani Predmet</label>
                    <select name="agreements[${index}][strani_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm strani-predmet-select" required>
                        <option value="">Prvo odaberite fakultet</option>
                    </select>
                </div>
                <button type="button" class="text-red-600 hover:text-red-900 remove-agreement mt-6">X</button>
            `;
            agreementsContainer.appendChild(row);
            
            const newSelect = row.querySelector('.strani-predmet-select');
            populateForeignSubjects(newSelect, fakultetSelect.value);
            
            const fitSelect = row.querySelector('.fit-predmet-select');
            fitSelect.addEventListener('change', updateMacovanjeTable);
            newSelect.addEventListener('change', updateMacovanjeTable);
        });

        agreementsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-agreement')) {
                e.target.closest('.agreement-row').remove();
                updateMacovanjeTable();
            }
        });

        // Update table when selects change
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('fit-predmet-select') || e.target.classList.contains('strani-predmet-select')) {
                updateMacovanjeTable();
            }
        });

        // Initial table update
        updateMacovanjeTable();
    </script>
</x-app-layout>
