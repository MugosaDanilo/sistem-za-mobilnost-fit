<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Prepis') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                            <select name="student_id" id="student_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">Select Student</option>
                                @foreach($studenti as $student)
                                    <option value="{{ $student->id }}">{{ $student->ime }} {{ $student->prezime }} ({{ $student->br_indexa }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="fakultet_id" class="block text-sm font-medium text-gray-700">Faculty</label>
                            <select name="fakultet_id" id="fakultet_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">Select Faculty</option>
                                @foreach($fakulteti as $fakultet)
                                    <option value="{{ $fakultet->id }}">{{ $fakultet->naziv }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="datum" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="datum" id="datum" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>

                        <div class="mb-4">
                            <h3 class="text-lg font-medium mb-2">Subjects</h3>
                            <div id="agreements-container">
                                <div class="agreement-row flex space-x-4 mb-2">
                                    <div class="w-1/2">
                                        <label class="block text-sm font-medium text-gray-700">FIT Subject</label>
                                        <select name="agreements[0][fit_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fit-predmet-select" required>
                                            <option value="">Select FIT Subject</option>
                                            @foreach($predmeti as $predmet)
                                                {{-- Assuming FIT subjects have a specific ID or we just show all for now. 
                                                     Ideally we should filter by FIT faculty ID if known. 
                                                     For now, showing all as per previous implementation but user can select. --}}
                                                <option value="{{ $predmet->id }}">{{ $predmet->naziv }} ({{ $predmet->ects }} ECTS)</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-1/2">
                                        <label class="block text-sm font-medium text-gray-700">Foreign Subject</label>
                                        <select name="agreements[0][strani_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm strani-predmet-select" required disabled>
                                            <option value="">Select Faculty First</option>
                                        </select>
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

        function populateForeignSubjects(selectElement, facultyId) {
            selectElement.innerHTML = '<option value="">Select Foreign Subject</option>';
            if (!facultyId) {
                selectElement.disabled = true;
                selectElement.innerHTML = '<option value="">Select Faculty First</option>';
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
                // Try to keep selected value if valid, otherwise reset
                if (currentValue) {
                    // Check if current value exists in new options
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

        fakultetSelect.addEventListener('change', updateAllForeignSubjects);

        document.getElementById('add-agreement').addEventListener('click', function() {
            const index = agreementsContainer.children.length;
            const row = document.createElement('div');
            row.className = 'agreement-row flex space-x-4 mb-2';
            row.innerHTML = `
                <div class="w-1/2">
                    <select name="agreements[${index}][fit_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fit-predmet-select" required>
                        <option value="">Select FIT Subject</option>
                        @foreach($predmeti as $predmet)
                            <option value="{{ $predmet->id }}">{{ $predmet->naziv }} ({{ $predmet->ects }} ECTS)</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-1/2">
                    <select name="agreements[${index}][strani_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm strani-predmet-select" required>
                        <option value="">Select Faculty First</option>
                    </select>
                </div>
                <button type="button" class="text-red-600 hover:text-red-900 remove-agreement">X</button>
            `;
            agreementsContainer.appendChild(row);
            
            // Populate the new select immediately
            const newSelect = row.querySelector('.strani-predmet-select');
            populateForeignSubjects(newSelect, fakultetSelect.value);
        });

        agreementsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-agreement')) {
                e.target.closest('.agreement-row').remove();
            }
        });
    </script>
</x-app-layout>
