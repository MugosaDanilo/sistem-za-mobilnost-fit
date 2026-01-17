<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Final Match: Student Subjects to Professors') }}
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

                    <!-- Student Selection -->
                    <div class="mb-6">
                        <label for="student_id" class="block text-sm font-medium text-gray-700">Select Student</label>
                        <div class="relative searchable-container" data-type="student">
                            <input type="text" class="search-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Search Student..." autocomplete="off">
                            <div class="search-results absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden"></div>
                            <select name="student_id" id="student_id" class="hidden">
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" data-text="{{ $student->ime }} {{ $student->prezime }} ({{ $student->br_indexa }})">{{ $student->ime }} {{ $student->prezime }} ({{ $student->br_indexa }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Drag and Drop Interface -->
                    <div class="mb-6 select-none">
                        <h3 class="text-lg font-medium mb-4">Link Student Subject to Professor</h3>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Student Subjects Column (Source) -->
                            <div class="flex flex-col bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                                <h4 class="font-semibold text-gray-700 mb-2">Student's Subjects</h4>
                                <input type="text" id="search-subject" placeholder="Search Subject..." class="mb-2 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" disabled>
                                <div id="subject-list" class="h-[500px] overflow-y-auto space-y-2 p-1 border border-gray-100 rounded bg-gray-50">
                                    <p class="text-gray-500 text-sm text-center mt-4">Select a student/faculty to view subjects</p>
                                </div>
                            </div>

                            <!-- Drop Zone & Linked List -->
                            <div class="flex flex-col space-y-4">
                                <!-- Drop Zone -->
                                <div id="drop-zone" class="bg-blue-50 border-2 border-dashed border-blue-300 rounded-lg p-6 flex flex-col items-center justify-center transition-colors min-h-[150px]">
                                    <p class="text-blue-500 font-medium text-center mb-2">Drag subject and professor here to link</p>
                                    <div class="flex items-center space-x-4 w-full justify-center">
                                        
                                        <div id="drop-slot-subject" class="w-1/2 h-12 bg-white border border-gray-200 rounded flex items-center justify-center text-xs text-gray-400 text-center px-2">
                                            Subject
                                        </div>
                                        <span class="text-gray-400">+</span>
                                        <div id="drop-slot-prof" class="w-1/2 h-12 bg-white border border-gray-200 rounded flex items-center justify-center text-xs text-gray-400 text-center px-2">
                                            Professor
                                        </div>
                                    </div>
                                </div>

                                <!-- Linked List -->
                                <div class="flex-1 bg-white rounded-lg border border-gray-200 shadow-sm flex flex-col">
                                    <div class="p-3 border-b border-gray-200 bg-gray-50 rounded-t-lg flex justify-between items-center">
                                        <h4 class="font-semibold text-gray-700">Matched Pairs</h4>
                                        <button id="send-btn" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-1 px-3 rounded shadow transition-colors hidden">
                                            Send Requests
                                        </button>
                                    </div>
                                    <div id="linked-list" class="h-[350px] overflow-y-auto p-2 space-y-2">
                                        <!-- Linked Pairs will be injected here -->
                                    </div>
                                </div>
                            </div>

                            <!-- Professors Column -->
                             <div class="flex flex-col bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                                <h4 class="font-semibold text-gray-700 mb-2">Available Professors</h4>
                                <input type="text" id="search-prof" placeholder="Search Professor..." class="mb-2 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <div id="prof-list" class="h-[500px] overflow-y-auto space-y-2 p-1 border border-gray-100 rounded bg-gray-50">
                                    <!-- Professor Items will be injected here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .draggable-item {
            cursor: grab;
            user-select: none;
            position: relative;
        }
        .draggable-item:active {
            cursor: grabbing;
            z-index: 10;
        }
        .draggable-item.dragging {
            opacity: 0.5;
            transform: scale(0.95);
        }
        .drop-active {
            background-color: #e0e7ff; /* indigo-100 */
            border-color: #6366f1; /* indigo-500 */
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const allProfessors = @json($professors);
            
            // State
            let state = {
                professors: [],
                subjects: [],
                linkedPairs: [],
                pendingProf: null,
                pendingSubject: null,
                selectedStudentId: null
            };

            // DOM Elements
            const els = {
                profList: document.getElementById('prof-list'),
                subjectList: document.getElementById('subject-list'),
                linkedList: document.getElementById('linked-list'),
                dropZone: document.getElementById('drop-zone'),
                dropSlotProf: document.getElementById('drop-slot-prof'),
                dropSlotSubject: document.getElementById('drop-slot-subject'),
                searchProf: document.getElementById('search-prof'),
                searchSubject: document.getElementById('search-subject'),
                studentSelect: document.getElementById('student_id'),
                sendBtn: document.getElementById('send-btn'),
            };

            // Initialize Data
            function init() {
                state.professors = allProfessors;
                render();
                setupDragAndDrop();
                setupSearch();
                setupStudentChange();
                setupSearchableDropdowns();
                setupSendButton();
            }

            // --- Rendering ---

            function render() {
                renderProfList();
                renderSubjectList();
                renderLinkedList();
                renderDropZone();
                updateSendButton();
            }

            function renderProfList() {
                const query = els.searchProf.value.toLowerCase();
                els.profList.innerHTML = '';
                
                state.professors
                    .filter(p => (p.name).toLowerCase().includes(query))
                    .forEach(p => {
                        const el = createDraggableItem(p, 'professor');
                        els.profList.appendChild(el);
                    });
            }

            function renderSubjectList() {
                els.subjectList.innerHTML = '';
                
                if (!state.selectedStudentId) {
                    els.subjectList.innerHTML = '<p class="text-gray-500 text-sm text-center mt-4">Select a student to view subjects</p>';
                    els.searchSubject.disabled = true;
                    return;
                }
                
                els.searchSubject.disabled = false;
                const query = els.searchSubject.value.toLowerCase();

                state.subjects
                    .filter(s => s.naziv && s.naziv.toLowerCase().includes(query)) // Ensure s.naziv exists
                    .forEach(s => {
                        const el = createDraggableItem(s, 'subject');
                        els.subjectList.appendChild(el);
                    });
            }

            function renderLinkedList() {
                els.linkedList.innerHTML = '';
                state.linkedPairs.forEach((pair, index) => {
                    const el = document.createElement('div');
                    el.className = 'flex items-center justify-between p-3 bg-white border border-gray-200 rounded shadow-sm text-sm';
                    el.innerHTML = `
                        <div class="flex-1 grid grid-cols-2 gap-2">
                            <div class="font-medium text-gray-800 truncate" title="${pair.subject.naziv}">${pair.subject.naziv}</div>
                            <div class="text-gray-600 truncate" title="${pair.prof.name}">-> ${pair.prof.name}</div>
                        </div>
                        <button type="button" class="ml-3 text-red-500 hover:text-red-700 font-bold px-2" onclick="unlinkPair(${index})">&times;</button>
                    `;
                    els.linkedList.appendChild(el);
                });
            }

            function renderDropZone() {
                if (state.pendingProf) {
                    els.dropSlotProf.textContent = state.pendingProf.name;
                    els.dropSlotProf.className = "w-1/2 h-12 bg-indigo-50 border border-indigo-300 text-indigo-700 rounded flex items-center justify-center text-xs text-center px-2 font-medium relative group cursor-pointer";
                    els.dropSlotProf.innerHTML += `<span class="hidden group-hover:flex absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-4 h-4 items-center justify-center text-[10px]" onclick="clearSlot('professor', event)">x</span>`;
                } else {
                    els.dropSlotProf.textContent = "Professor";
                    els.dropSlotProf.className = "w-1/2 h-12 bg-white border border-gray-200 rounded flex items-center justify-center text-xs text-gray-400 text-center px-2";
                }

                if (state.pendingSubject) {
                    els.dropSlotSubject.textContent = state.pendingSubject.naziv;
                    els.dropSlotSubject.className = "w-1/2 h-12 bg-indigo-50 border border-indigo-300 text-indigo-700 rounded flex items-center justify-center text-xs text-center px-2 font-medium relative group cursor-pointer";
                    els.dropSlotSubject.innerHTML += `<span class="hidden group-hover:flex absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-4 h-4 items-center justify-center text-[10px]" onclick="clearSlot('subject', event)">x</span>`;
                } else {
                    els.dropSlotSubject.textContent = "Subject";
                    els.dropSlotSubject.className = "w-1/2 h-12 bg-white border border-gray-200 rounded flex items-center justify-center text-xs text-gray-400 text-center px-2";
                }
            }

            function updateSendButton() {
                if (state.linkedPairs.length > 0) {
                    els.sendBtn.classList.remove('hidden');
                } else {
                    els.sendBtn.classList.add('hidden');
                }
            }

            function createDraggableItem(item, type) {
                const div = document.createElement('div');
                div.className = 'draggable-item bg-white p-2 rounded border border-gray-200 shadow-sm text-sm hover:border-indigo-400 transition-colors flex justify-between items-center group';
                div.draggable = true;
                div.dataset.id = item.id;
                div.dataset.type = type;
                
                // Content
                const content = document.createElement('span');
                if (type === 'professor') {
                     content.textContent = item.name;
                } else {
                     content.textContent = `${item.naziv} (${item.ects} ECTS)`;
                }
                div.appendChild(content);
                
                div.addEventListener('dragstart', (e) => {
                    div.classList.add('dragging');
                    e.dataTransfer.setData('text/plain', JSON.stringify({ id: item.id, type: type }));
                    e.dataTransfer.effectAllowed = 'move';
                });

                div.addEventListener('dragend', () => {
                    div.classList.remove('dragging');
                });

                return div;
            }

            // --- Logic ---

            async function loadStudentSubjects(studentId) {
                try {
                    const response = await fetch(`{{ url('/admin/prepisi/student-subjects') }}/${studentId}`);
                    if (!response.ok) throw new Error('Failed to fetch subjects');
                    const data = await response.json();
                    state.subjects = data;
                    renderSubjectList();
                } catch (error) {
                    console.error('Error fetching subjects:', error);
                    alert('Could not load student subjects.');
                }
            }
            
            function pairExists(profId, subjectId) {
                return state.linkedPairs.some(p => p.prof.id == profId && p.subject.id == subjectId);
            }

            window.unlinkPair = function(index) {
                state.linkedPairs.splice(index, 1);
                render();
            };

            window.clearSlot = function(type, e) {
                e.stopPropagation();
                if (type === 'professor') state.pendingProf = null;
                if (type === 'subject') state.pendingSubject = null;
                render();
            };

            // --- Drag and Drop Setup ---

            function setupDragAndDrop() {
                const zone = els.dropZone;

                zone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    zone.classList.add('drop-active');
                });

                zone.addEventListener('dragleave', () => {
                    zone.classList.remove('drop-active');
                });

                zone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    zone.classList.remove('drop-active');
                    
                    const data = e.dataTransfer.getData('text/plain');
                    if (!data) return;
                    
                    try {
                        const itemData = JSON.parse(data);
                        
                        if (itemData.type === 'professor') {
                            const prof = allProfessors.find(p => p.id == itemData.id);
                             if (prof) state.pendingProf = prof;
                        } else { // subject
                             const subj = state.subjects.find(s => s.id == itemData.id);
                             if (subj) state.pendingSubject = subj;
                        }

                        if (state.pendingProf && state.pendingSubject) {
                            if (!pairExists(state.pendingProf.id, state.pendingSubject.id)) {
                                state.linkedPairs.push({ prof: state.pendingProf, subject: state.pendingSubject });
                                state.pendingProf = null;
                                state.pendingSubject = null;
                            } else {
                                alert('This match already exists.');
                            }
                        }

                        render();

                    } catch (err) {
                        console.error('Drop error', err);
                    }
                });
            }

            // --- Search & Events ---

            function setupSearch() {
                els.searchProf.addEventListener('input', renderProfList);
                els.searchSubject.addEventListener('input', renderSubjectList);
            }

            function setupStudentChange() {
                els.studentSelect.addEventListener('change', () => {
                    const studentId = els.studentSelect.value;
                    state.selectedStudentId = studentId;
                    state.pendingSubject = null;
                    state.linkedPairs = []; // Clear current pairs on student change? Probably safer.
                    if (studentId) {
                        loadStudentSubjects(studentId);
                    } else {
                        state.subjects = [];
                        renderSubjectList();
                    }
                    render();
                });
            }

            function setupSearchableDropdowns() {
                 document.querySelectorAll('.searchable-container').forEach(container => {
                    const input = container.querySelector('.search-input');
                    const resultsDiv = container.querySelector('.search-results');
                    const select = container.querySelector('select');
                    
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
                        const filtered = optionsData.filter(o => o.text.toLowerCase().includes(query));
                        
                        resultsDiv.innerHTML = '';
                        if (filtered.length === 0) {
                            resultsDiv.innerHTML = '<div class="px-4 py-2 text-gray-500 italic">No results found</div>';
                        } else {
                            filtered.forEach(res => {
                                const div = document.createElement('div');
                                div.className = 'px-4 py-2 hover:bg-blue-50 cursor-pointer transition-colors';
                                div.textContent = res.text;
                                div.addEventListener('click', () => {
                                    input.value = res.text;
                                    select.value = res.id;
                                    select.dispatchEvent(new Event('change'));
                                    resultsDiv.classList.add('hidden');
                                });
                                resultsDiv.appendChild(div);
                            });
                        }
                        resultsDiv.classList.remove('hidden');
                    });

                    input.addEventListener('focus', () => {
                        input.dispatchEvent(new Event('input'));
                    });

                    document.addEventListener('click', (e) => {
                        if (!container.contains(e.target)) {
                            resultsDiv.classList.add('hidden');
                        }
                    });
                });
            }

            function setupSendButton() {
                els.sendBtn.addEventListener('click', async () => {
                    if (state.linkedPairs.length === 0) return;
                    
                    if (!confirm('Are you sure you want to send these requests to the professors?')) return;

                    const matches = state.linkedPairs.map(p => ({
                        professor_id: p.prof.id,
                        subject_id: p.subject.id
                    }));

                    try {
                        const response = await fetch('{{ route("prepis.match.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                student_id: state.selectedStudentId,
                                matches: matches
                            })
                        });

                        if (response.ok) {
                            alert('Requests sent successfully!');
                            window.location.href = "{{ route('prepis.index') }}";
                        } else {
                            alert('Failed to send requests. Please try again.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred.');
                    }
                });
            }

            init();
        });
    </script>
</x-app-layout>
