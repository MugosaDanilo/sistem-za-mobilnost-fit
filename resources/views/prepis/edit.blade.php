<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Prepis') }}
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

                    <form action="{{ route('prepis.update', $prepis->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="student_id" class="block text-sm font-medium text-gray-700">Student</label>
                            <div class="relative searchable-container" data-type="student">
                                <input type="text" class="search-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Search Student..." autocomplete="off">
                                <div class="search-results absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden"></div>
                                <select name="student_id" id="student_id" class="hidden" required>
                                    <option value="">Select Student</option>
                                    @foreach($studenti as $student)
                                        <option value="{{ $student->id }}" 
                                            {{ $prepis->student_id == $student->id ? 'selected' : '' }}
                                            data-text="{{ $student->ime }} {{ $student->prezime }} ({{ $student->br_indexa }})">
                                            {{ $student->ime }} {{ $student->prezime }} ({{ $student->br_indexa }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="fakultet_id" class="block text-sm font-medium text-gray-700">Faculty</label>
                            <div class="relative searchable-container" data-type="faculty">
                                <input type="text" class="search-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Search Faculty..." autocomplete="off">
                                <div class="search-results absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden"></div>
                                <select name="fakultet_id" id="fakultet_id" class="hidden" required>
                                    <option value="">Select Faculty</option>
                                    @foreach($fakulteti as $fakultet)
                                        <option value="{{ $fakultet->id }}" 
                                            {{ $prepis->fakultet_id == $fakultet->id ? 'selected' : '' }}
                                            data-text="{{ $fakultet->naziv }}">
                                            {{ $fakultet->naziv }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="datum" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="datum" id="datum" value="{{ $prepis->datum->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>

                        <div class="mb-4">
                            <h3 class="text-lg font-medium mb-2">Subjects</h3>
                            <div id="agreements-container">
                                @foreach($prepis->agreements as $index => $agreement)
                                <div class="agreement-row flex space-x-4 mb-2">
                                    <div class="w-1/2">
                                        <label class="block text-sm font-medium text-gray-700">FIT Subject</label>
                                        <div class="relative searchable-container" data-type="fit-subject">
                                            <input type="text" class="search-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Search FIT Subject..." autocomplete="off">
                                            <div class="search-results absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden"></div>
                                            <select name="agreements[{{ $index }}][fit_predmet_id]" class="hidden fit-predmet-select" required>
                                                <option value="">Select FIT Subject</option>
                                                @foreach($predmeti as $predmet)
                                                    <option value="{{ $predmet->id }}" 
                                                        {{ $agreement->fit_predmet_id == $predmet->id ? 'selected' : '' }}
                                                        data-text="{{ $predmet->naziv }} ({{ $predmet->ects }} ECTS)">
                                                        {{ $predmet->naziv }} ({{ $predmet->ects }} ECTS)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <label class="block text-sm font-medium text-gray-700">Foreign Subject</label>
                                        <div class="relative searchable-container" data-type="foreign-subject">
                                            <input type="text" class="search-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Search Foreign Subject..." autocomplete="off">
                                            <div class="search-results absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden"></div>
                                            <select name="agreements[{{ $index }}][strani_predmet_id]" class="hidden strani-predmet-select" required>
                                                <option value="{{ $agreement->strani_predmet_id }}" selected data-text="{{ $agreement->straniPredmet->naziv }} ({{ $agreement->straniPredmet->ects }} ECTS)">
                                                    {{ $agreement->straniPredmet->naziv }} ({{ $agreement->straniPredmet->ects }} ECTS)
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="button" class="text-red-600 hover:text-red-900 remove-agreement">X</button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-agreement" class="mt-2 text-sm text-blue-600 hover:text-blue-900">+ Add Another Subject Pair</button>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update
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
    </script>
</x-app-layout>
