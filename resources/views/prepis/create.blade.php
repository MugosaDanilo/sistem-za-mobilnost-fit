<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Prepis') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('prepis.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                            &larr; Back to Prepis Management
                        </a>
                    </div>

                    <form action="{{ route('prepis.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="student_id" class="block text-sm font-medium text-gray-700">Student</label>
                            <div class="relative searchable-container" data-type="student">
                                <input type="text" class="search-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Search Student..." autocomplete="off">
                                <div class="search-results absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden"></div>
                                <select name="student_id" id="student_id" class="hidden" required>
                                    <option value="">Select Student</option>
                                    @foreach($studenti as $student)
                                        <option value="{{ $student->id }}" data-text="{{ $student->ime }} {{ $student->prezime }} ({{ $student->br_indexa }})">{{ $student->ime }} {{ $student->prezime }} ({{ $student->br_indexa }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="fakultet_id" class="block text-sm font-medium text-gray-700">Faculty</label>
                            <div class="relative searchable-container" data-type="faculty">
                                <input type="text" class="search-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Search Faculty..." autocomplete="off">
                                <div class="search-results absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden"></div>
                                <select name="fakultet_id" id="fakultet_id" class="hidden" required>
                                    <option value="">Select Faculty</option>
                                    @foreach($fakulteti as $fakultet)
                                        <option value="{{ $fakultet->id }}" data-text="{{ $fakultet->naziv }}">{{ $fakultet->naziv }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="datum" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="datum" id="datum" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>

                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="text-lg font-medium">Predmeti</h3>
                                <button type="button" id="automec-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                    Automeč
                                </button>
                            </div>
                            <div id="agreements-container">
                                <div class="agreement-row flex space-x-4 mb-2">
                                    <div class="w-1/2">
                                        <label class="block text-sm font-medium text-gray-700">FIT Subject</label>
                                        <div class="relative searchable-container" data-type="fit-subject">
                                            <input type="text" class="search-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Search FIT Subject..." autocomplete="off">
                                            <div class="search-results absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden"></div>
                                            <select name="agreements[0][fit_predmet_id]" class="hidden fit-predmet-select" required>
                                                <option value="">Select FIT Subject</option>
                                                @foreach($predmeti as $predmet)
                                                    <option value="{{ $predmet->id }}" data-text="{{ $predmet->naziv }} ({{ $predmet->ects }} ECTS)">{{ $predmet->naziv }} ({{ $predmet->ects }} ECTS)</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <label class="block text-sm font-medium text-gray-700">Foreign Subject</label>
                                        <div class="relative searchable-container" data-type="foreign-subject">
                                            <input type="text" class="search-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Select Faculty First" autocomplete="off" disabled>
                                            <div class="search-results absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden"></div>
                                            <select name="agreements[0][strani_predmet_id]" class="hidden strani-predmet-select" required>
                                                <option value="">Select Faculty First</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add-agreement" class="mt-2 text-sm text-blue-600 hover:text-blue-900">+ Add Another Subject Pair</button>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const allSubjects = @json($predmeti);
        const agreementsContainer = document.getElementById('agreements-container');
        const fakultetSelect = document.getElementById('fakultet_id');

        document.querySelectorAll('.searchable-container').forEach(setupSearchableInput);

        function setupSearchableInput(container) {
            const input = container.querySelector('.search-input');
            const resultsDiv = container.querySelector('.search-results');
            const select = container.querySelector('select');
            const type = container.dataset.type;

            let optionsData = Array.from(select.options)
                .filter(opt => opt.value)
                .map(opt => ({
                    id: opt.value,
                    text: opt.dataset.text || opt.text,
                    element: opt
                }));

            if (select.value) {
                const selected = optionsData.find(o => o.id === select.value);
                if (selected) input.value = selected.text;
            }

            input.addEventListener('input', () => {
                const query = input.value.toLowerCase();
                
                if (type === 'foreign-subject') {
                    const facultyId = fakultetSelect.value;
                    if (!facultyId) return;
                    
                    const filtered = allSubjects.filter(s => 
                        s.fakultet_id == facultyId && 
                        (s.naziv.toLowerCase().includes(query) || (s.ects + ' ECTS').toLowerCase().includes(query))
                    ).map(s => ({
                        id: s.id,
                        text: `${s.naziv} (${s.ects} ECTS)`
                    }));
                    renderResults(filtered);
                } else {
                    const filtered = optionsData.filter(o => o.text.toLowerCase().includes(query));
                    renderResults(filtered);
                }
            });

            input.addEventListener('focus', () => {
                input.dispatchEvent(new Event('input'));
            });

            function renderResults(results) {
                resultsDiv.innerHTML = '';
                if (results.length === 0) {
                    const div = document.createElement('div');
                    div.className = 'px-4 py-2 text-gray-500 italic';
                    div.textContent = 'No results found';
                    resultsDiv.appendChild(div);
                } else {
                    results.forEach(res => {
                        const div = document.createElement('div');
                        div.className = 'px-4 py-2 hover:bg-blue-50 cursor-pointer transition-colors';
                        div.textContent = res.text;
                        div.addEventListener('click', () => {
                            input.value = res.text;
                            select.value = res.id;
                            
                            // For foreign subject, we might need to add the option tag if it doesn't exist
                            if (type === 'foreign-subject') {
                                select.innerHTML = `<option value="${res.id}" selected>${res.text}</option>`;
                            }

                            select.dispatchEvent(new Event('change'));
                            resultsDiv.classList.add('hidden');
                        });
                        resultsDiv.appendChild(div);
                    });
                }
                resultsDiv.classList.remove('hidden');
            }

            document.addEventListener('click', (e) => {
                if (!container.contains(e.target)) {
                    resultsDiv.classList.add('hidden');
                }
            });
        }

        fakultetSelect.addEventListener('change', () => {
            const facultyId = fakultetSelect.value;
            const foreignContainers = document.querySelectorAll('.searchable-container[data-type="foreign-subject"]');
            
            foreignContainers.forEach(container => {
                const input = container.querySelector('.search-input');
                const select = container.querySelector('select');
                
                input.value = '';
                select.value = '';
                select.innerHTML = '<option value="">Select Foreign Subject</option>';
                
                if (facultyId) {
                    input.disabled = false;
                    input.placeholder = "Search Foreign Subject...";
                } else {
                    input.disabled = true;
                    input.placeholder = "Select Faculty First";
                }
            });
        });

        document.getElementById('add-agreement').addEventListener('click', function() {
            const index = agreementsContainer.querySelectorAll('.agreement-row').length;
            const row = document.createElement('div');
            row.className = 'agreement-row flex space-x-4 mb-2';
            row.innerHTML = `
                <div class="w-1/2">
                    <div class="relative searchable-container" data-type="fit-subject">
                        <input type="text" class="search-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Search FIT Subject..." autocomplete="off">
                        <div class="search-results absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden"></div>
                        <select name="agreements[${index}][fit_predmet_id]" class="hidden fit-predmet-select" required>
                            <option value="">Select FIT Subject</option>
                            @foreach($predmeti as $predmet)
                                <option value="{{ $predmet->id }}" data-text="{{ $predmet->naziv }} ({{ $predmet->ects }} ECTS)">{{ $predmet->naziv }} ({{ $predmet->ects }} ECTS)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="w-1/2">
                    <div class="relative searchable-container" data-type="foreign-subject">
                        <input type="text" class="search-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="${fakultetSelect.value ? 'Search Foreign Subject...' : 'Select Faculty First'}" autocomplete="off" ${fakultetSelect.value ? '' : 'disabled'}>
                        <div class="search-results absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden"></div>
                        <select name="agreements[${index}][strani_predmet_id]" class="hidden strani-predmet-select" required>
                            <option value="">Select Foreign Subject</option>
                        </select>
                    </div>
                </div>
                <button type="button" class="text-red-600 hover:text-red-900 remove-agreement">X</button>
            `;
            agreementsContainer.appendChild(row);
            
            row.querySelectorAll('.searchable-container').forEach(setupSearchableInput);
        });

        agreementsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-agreement')) {
                e.target.closest('.agreement-row').remove();
            }
        });

        // Automec funkcionalnost
        document.getElementById('automec-btn').addEventListener('click', async function() {
            const fakultetId = fakultetSelect.value;
            if (!fakultetId) {
                alert('Selektuj fakultet prvo');
                return;
            }

            // Svi odabrani FIT predmeti
            const fitPredmetSelects = document.querySelectorAll('.fit-predmet-select');
            const fitPredmetIds = Array.from(fitPredmetSelects)
                .map(select => select.value)
                .filter(id => id !== '');

            if (fitPredmetIds.length === 0) {
                alert('Selektuj bar jedan FIT predmet');
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
                        fit_predmet_ids: fitPredmetIds,
                        fakultet_id: fakultetId
                    })
                });

                const suggestions = await response.json();

                // Svaki red
                fitPredmetSelects.forEach((fitSelect, index) => {
                    const fitPredmetId = fitSelect.value;
                    if (fitPredmetId && suggestions[fitPredmetId]) {
                        const row = fitSelect.closest('.agreement-row');
                        const straniSelect = row.querySelector('.strani-predmet-select');
                        
                       
                        if (straniSelect.disabled) {
                            populateForeignSubjects(straniSelect, fakultetId);
                        }
                        
                        
                        setTimeout(() => {
                            straniSelect.value = suggestions[fitPredmetId].strani_predmet_id;
                        }, 100);
                    }
                });

                if (Object.keys(suggestions).length > 0) {
                    alert('Mačovanje pokrenuto!');
                } else {
                    alert('Nema predmeta za mačovanje.');
                }
            } catch (error) {
                console.error('Greška:', error);
                alert('Greška prilikom mečovanja. Probaj opet.');
            }
        });
    </script>
</x-app-layout>
