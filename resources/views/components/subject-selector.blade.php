@props(['subjects', 'selected' => []])

<div x-data="{
    open: false,
    search: '',
    semester: '',
    ects: '',
    selectedIds: [],
    grades: {},
    subjects: {{ json_encode($subjects) }},
    currentLevelId: '',

    init() {
        
        let initialSelected = {{ json_encode($selected) }};
        
        if (Array.isArray(initialSelected)) {
             initialSelected.forEach(item => {
                if (typeof item === 'object' && item !== null) {
                    this.selectedIds.push(item.id);
                    if (item.pivot && item.pivot.grade) {
                        this.grades[item.id] = item.pivot.grade;
                    } 
                } else {
                    this.selectedIds.push(item);
                }
             });
        }
    },

    get filteredSubjects() {
        return this.subjects.filter(subject => {
            const matchesSearch = subject.naziv.toLowerCase().includes(this.search.toLowerCase());
            const matchesSemester = this.semester === '' || subject.semestar == this.semester;
            const matchesEcts = this.ects === '' || subject.ects == this.ects;
            
            const matchesLevel = this.currentLevelId === '' || subject.nivo_studija_id == this.currentLevelId;
            
            return matchesSearch && matchesSemester && matchesEcts && matchesLevel;
        });
    },

    get selectedSubjectsList() {
        return this.subjects.filter(subject => this.selectedIds.includes(subject.id));
    },

    toggleSelection(id) {
        if (this.selectedIds.includes(id)) {
            this.selectedIds = this.selectedIds.filter(i => i !== id);
            delete this.grades[id];
        } else {
            this.selectedIds.push(id);
            this.grades[id] = '';
        }
    },

    get selectedCount() {
        return this.selectedIds.length;
    }
}" class="w-full" 
@study-level-changed.window="currentLevelId = $event.detail"
@subjects-updated.window="subjects = $event.detail">

    <div class="mb-2">
        <label class="block text-gray-700 font-medium mb-1">Assign Subjects</label>
        <button type="button" @click="open = true" :disabled="currentLevelId === ''"
            :class="{'opacity-50 cursor-not-allowed': currentLevelId === ''}"
            class="bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-4 py-2 rounded-lg border border-indigo-200 transition-colors flex items-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span x-text="currentLevelId === '' ? 'Select Study Level First' : 'Select Subjects'"></span>
        </button>

        <!-- Selected Subjects Table -->
        <div x-show="selectedCount > 0" class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Subject
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Semester</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ECTS
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade
                            (6-10)</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="subject in selectedSubjectsList" :key="subject.id">
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="subject.naziv"></td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500" x-text="subject.semestar">
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="subject.ects + ' ECTS'"></td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                <input type="number" min="6" max="10" x-model="grades[subject.id]" placeholder="-"
                                    class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                                <button type="button" @click="toggleSelection(subject.id)"
                                    class="text-red-600 hover:text-red-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Hidden inputs for form submission -->
        <!-- We generate input name="predmeti[ID][grade]" value="GRADE" -->
        <template x-for="id in selectedIds" :key="id">
            <div>
                <!-- We need to send the ID even if grade is empty? Or logic in controller handles it? 
                      The controller expects predmeti[id] => ['grade' => val].
                      So we generate inputs such that req->predmeti is an associative array.
                 -->
                <input type="hidden" :name="'predmeti[' + id + '][grade]'" :value="grades[id]">
            </div>
        </template>
    </div>

    <!-- Modal -->
    <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4"
        style="display: none;"> <!-- style display none prevents flash before alpine loads -->

        <div @click.away="open = false"
            class="relative mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-xl bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Select Subjects</h3>
                <button @click="open = false" type="button" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 bg-gray-50 p-4 rounded-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                    <input x-model="search" type="text" placeholder="Enter name..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                    <select x-model="semester"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">All Semesters</option>
                        @foreach(range(1, 8) as $sem)
                            <option value="{{ $sem }}">{{ $sem }}. Semester</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ECTS Points</label>
                    <input x-model="ects" type="number" placeholder="ECTS"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>

            <!-- List -->
            <div class="overflow-y-auto max-h-96 border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">
                                #
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subject Name
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Semester
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ECTS
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="subject in filteredSubjects" :key="subject.id">
                            <tr @click="toggleSelection(subject.id)"
                                class="hover:bg-indigo-50 cursor-pointer transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" :checked="selectedIds.includes(subject.id)"
                                        class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                </td>
                                <td class="px-6 py-4" x-text="subject.naziv"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="subject.semestar">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                        x-text="subject.ects + ' ECTS'"></span>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredSubjects.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No subjects match your search.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-end space-x-2 border-t pt-4">
                <button @click="open = false" type="button"
                    class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 shadow-sm font-medium">
                    Close
                </button>
                <button @click="open = false" type="button"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-lg transform hover:scale-105 transition-all font-medium">
                    Save Selection
                </button>
            </div>
        </div>
    </div>
</div>