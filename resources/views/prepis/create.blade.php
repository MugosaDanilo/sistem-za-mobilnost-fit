<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kreiraj novi prepis') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('prepis.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                            &larr; Nazad na listu prepisa
                        </a>
                    </div>

                    <form action="{{ route('prepis.store') }}" method="POST" id="prepis-form">
                        @csrf

                        <div class="mb-4">
                            <label for="student_id" class="block text-sm font-medium text-gray-700">Student</label>
                            <select name="student_id" id="student_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">Izaberi studenta</option>
                                @foreach($studenti as $student)
                                    <option value="{{ $student->id }}">{{ $student->ime }} {{ $student->prezime }} ({{ $student->br_indexa }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="fakultet_id" class="block text-sm font-medium text-gray-700">Fakultet</label>
                            <select name="fakultet_id" id="fakultet_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">Izaberi fakultet</option>
                                @foreach($fakulteti as $fakultet)
                                    <option value="{{ $fakultet->id }}">{{ $fakultet->naziv }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="datum" class="block text-sm font-medium text-gray-700">Datum</label>
                            <input type="date" name="datum" id="datum" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>

                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">Uparivanje predmeta (više na više)</h3>
                                <button type="button" id="automec-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded text-sm">
                                    Automeč
                                </button>
                            </div>

                            <div id="groups-container">
                                <div class="group-item border border-gray-300 rounded-lg p-4 mb-4 bg-gray-50">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="font-semibold text-gray-700">Grupa 1</h4>
                                        <button type="button" class="remove-group text-red-600 hover:text-red-900 font-bold">×</button>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">FIT Predmeti (može više)</label>
                                            <select name="groups[0][fit_predmeti][]" class="group-fit-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" multiple size="5">
                                                @foreach($predmeti as $predmet)
                                                    <option value="{{ $predmet->id }}">{{ $predmet->naziv }} ({{ $predmet->ects }} ECTS)</option>
                                                @endforeach
                                            </select>
                                            <p class="text-xs text-gray-500 mt-1">Drži Ctrl (Cmd na Mac) za više izbora</p>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Strani Predmeti (može više)</label>
                                            <select name="groups[0][strani_predmeti][]" class="group-strani-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" multiple size="5" disabled>
                                                <option value="">Izaberi fakultet prvo</option>
                                            </select>
                                            <p class="text-xs text-gray-500 mt-1">Drži Ctrl (Cmd na Mac) za više izbora</p>
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

                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Sačuvaj
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const allSubjects = @json($predmeti);
        const groupsContainer = document.getElementById('groups-container');
        const fakultetSelect = document.getElementById('fakultet_id');
        let groupCounter = 1;

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
                option.textContent = `${subject.naziv} (${subject.ects} ECTS)`;
                selectElement.appendChild(option);
            });
        }

        function updateAllForeignSubjects() {
            const facultyId = fakultetSelect.value;
            const straniSelects = document.querySelectorAll('.group-strani-select');
            straniSelects.forEach(select => {
                const selectedValues = Array.from(select.selectedOptions).map(opt => opt.value);
                populateForeignSubjects(select, facultyId);
                // Restore selected values if they still exist
                selectedValues.forEach(val => {
                    const option = Array.from(select.options).find(opt => opt.value === val);
                    if (option) {
                        option.selected = true;
                    }
                });
            });
            updateAllCombinations();
        }

        function updateCombinations(groupItem) {
            const fitSelect = groupItem.querySelector('.group-fit-select');
            const straniSelect = groupItem.querySelector('.group-strani-select');
            const combinationsSpan = groupItem.querySelector('.group-combinations');
            
            const fitCount = fitSelect.selectedOptions.length;
            const straniCount = straniSelect.selectedOptions.length;
            const total = fitCount * straniCount;
            
            combinationsSpan.textContent = `${total} kombinacija`;
        }

        function updateAllCombinations() {
            document.querySelectorAll('.group-item').forEach(groupItem => {
                updateCombinations(groupItem);
            });
        }

        fakultetSelect.addEventListener('change', updateAllForeignSubjects);

        // Add new group
        document.getElementById('add-group').addEventListener('click', function() {
            const groupHtml = `
                <div class="group-item border border-gray-300 rounded-lg p-4 mb-4 bg-gray-50">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-semibold text-gray-700">Grupa ${groupCounter + 1}</h4>
                        <button type="button" class="remove-group text-red-600 hover:text-red-900 font-bold">×</button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">FIT Predmeti (može više)</label>
                            <select name="groups[${groupCounter}][fit_predmeti][]" class="group-fit-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" multiple size="5">
                                @foreach($predmeti as $predmet)
                                    <option value="{{ $predmet->id }}">{{ $predmet->naziv }} ({{ $predmet->ects }} ECTS)</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Drži Ctrl (Cmd na Mac) za više izbora</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Strani Predmeti (može više)</label>
                            <select name="groups[${groupCounter}][strani_predmeti][]" class="group-strani-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" multiple size="5">
                                <option value="">Izaberi fakultet prvo</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Drži Ctrl (Cmd na Mac) za više izbora</p>
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
            
            // Populate foreign subjects if faculty is selected
            const newStraniSelect = newGroup.querySelector('.group-strani-select');
            if (fakultetSelect.value) {
                populateForeignSubjects(newStraniSelect, fakultetSelect.value);
            }
            
            // Add event listeners
            newGroup.querySelector('.group-fit-select').addEventListener('change', () => updateCombinations(newGroup));
            newStraniSelect.addEventListener('change', () => updateCombinations(newGroup));
            
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
                    updateAllCombinations();
                } else {
                    alert('Morate imati najmanje jednu grupu!');
                }
            }
        });

        // Update combinations when selections change
        document.querySelectorAll('.group-fit-select, .group-strani-select').forEach(select => {
            select.addEventListener('change', function() {
                const groupItem = this.closest('.group-item');
                updateCombinations(groupItem);
            });
        });

        function updateGroupNumbers() {
            document.querySelectorAll('.group-item').forEach((groupItem, index) => {
                const title = groupItem.querySelector('h4');
                title.textContent = `Grupa ${index + 1}`;
            });
        }

        // Automec functionality
        document.getElementById('automec-btn').addEventListener('click', async function() {
            const fakultetId = fakultetSelect.value;
            if (!fakultetId) {
                alert('Selektuj fakultet prvo');
                return;
            }

            // Collect all selected foreign subjects from all groups
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

                // Apply suggestions to first group's FIT subjects
                const firstGroup = document.querySelector('.group-item');
                if (firstGroup) {
                    const fitSelect = firstGroup.querySelector('.group-fit-select');
                    
                    // Clear current selections
                    Array.from(fitSelect.options).forEach(opt => opt.selected = false);
                    
                    // Select suggested FIT subjects
                    Object.values(suggestions).forEach(suggestion => {
                        const option = Array.from(fitSelect.options).find(opt => opt.value == suggestion.fit_predmet_id);
                        if (option) {
                            option.selected = true;
                        }
                    });
                    
                    updateCombinations(firstGroup);
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

            document.querySelectorAll('.group-item').forEach((groupItem, groupIndex) => {
                const fitSelect = groupItem.querySelector('.group-fit-select');
                const straniSelect = groupItem.querySelector('.group-strani-select');
                
                const fitIds = Array.from(fitSelect.selectedOptions).map(opt => opt.value).filter(v => v);
                const straniIds = Array.from(straniSelect.selectedOptions).map(opt => opt.value).filter(v => v);
                
                if (fitIds.length > 0 && straniIds.length > 0) {
                    hasValidGroup = true;
                    // Create all combinations
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
    </script>
</x-app-layout>
