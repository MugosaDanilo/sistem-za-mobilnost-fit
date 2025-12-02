<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Prepis') }}
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

                    <form action="{{ route('prepis.update', $prepis->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="student_id" class="block text-sm font-medium text-gray-700">Student</label>
                            <select name="student_id" id="student_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">Select Student</option>
                                @foreach($studenti as $student)
                                    <option value="{{ $student->id }}" {{ $prepis->student_id == $student->id ? 'selected' : '' }}>{{ $student->ime }} {{ $student->prezime }} ({{ $student->br_indexa }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="fakultet_id" class="block text-sm font-medium text-gray-700">Faculty</label>
                            <select name="fakultet_id" id="fakultet_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">Select Faculty</option>
                                @foreach($fakulteti as $fakultet)
                                    <option value="{{ $fakultet->id }}" {{ $prepis->fakultet_id == $fakultet->id ? 'selected' : '' }}>{{ $fakultet->naziv }}</option>
                                @endforeach
                            </select>
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
                                            <select name="agreements[{{ $index }}][fit_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fit-predmet-select" required>
                                                <option value="">Select FIT Subject</option>
                                                @foreach($predmeti as $predmet)
                                                    <option value="{{ $predmet->id }}" {{ $agreement->fit_predmet_id == $predmet->id ? 'selected' : '' }}>{{ $predmet->naziv }} ({{ $predmet->ects }} ECTS)</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="w-1/2">
                                            <label class="block text-sm font-medium text-gray-700">Foreign Subject</label>
                                            <select name="agreements[{{ $index }}][strani_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm strani-predmet-select" required data-initial-value="{{ $agreement->strani_predmet_id }}">
                                                <option value="">Select Faculty First</option>
                                            </select>
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

        function populateForeignSubjects(selectElement, facultyId) {
            const initialValue = selectElement.getAttribute('data-initial-value');
            const currentValue = selectElement.value || initialValue;

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
                if (subject.id == currentValue) {
                    option.selected = true;
                }
                selectElement.appendChild(option);
            });
        }

        function updateAllForeignSubjects() {
            const facultyId = fakultetSelect.value;
            const foreignSelects = document.querySelectorAll('.strani-predmet-select');
            foreignSelects.forEach(select => {
                populateForeignSubjects(select, facultyId);
            });
        }

        fakultetSelect.addEventListener('change', updateAllForeignSubjects);

        document.getElementById('add-agreement').addEventListener('click', function() {
            const index = agreementsContainer.children.length; 
            const uniqueIndex = Date.now();
            
            const row = document.createElement('div');
            row.className = 'agreement-row flex space-x-4 mb-2';
            row.innerHTML = `
                <div class="w-1/2">
                    <select name="agreements[${uniqueIndex}][fit_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fit-predmet-select" required>
                        <option value="">Select FIT Subject</option>
                        @foreach($predmeti as $predmet)
                            <option value="{{ $predmet->id }}">{{ $predmet->naziv }} ({{ $predmet->ects }} ECTS)</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-1/2">
                    <select name="agreements[${uniqueIndex}][strani_predmet_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm strani-predmet-select" required>
                        <option value="">Select Faculty First</option>
                    </select>
                </div>
                <button type="button" class="text-red-600 hover:text-red-900 remove-agreement">X</button>
            `;
            agreementsContainer.appendChild(row);
            
            const newSelect = row.querySelector('.strani-predmet-select');
            populateForeignSubjects(newSelect, fakultetSelect.value);
        });

        agreementsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-agreement')) {
                e.target.closest('.agreement-row').remove();
            }
        });

        document.addEventListener('DOMContentLoaded', updateAllForeignSubjects);
    </script>
</x-app-layout>
