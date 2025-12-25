<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Map Subjects') }} - {{ $mappingRequest->fakultet->naziv }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('profesorDashboardShow') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                            &larr; Back to Dashboard
                        </a>
                    </div>

                    <div class="mb-6 select-none">
                        <h3 class="text-lg font-medium mb-4">Link Foreign Subjects to Your Subjects</h3>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Foreign Subjects Column -->
                            <div class="flex flex-col bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                                <h4 class="font-semibold text-gray-700 mb-2">Foreign Subjects ({{ $mappingRequest->fakultet->naziv }})</h4>
                                <div id="foreign-list" class="h-[500px] overflow-y-auto space-y-2 p-1 border border-gray-100 rounded bg-gray-50">
                                    @foreach($mappingRequest->subjects as $reqSubject)
                                        @if(!$reqSubject->fit_predmet_id)
                                            <div class="draggable-item bg-white p-2 rounded border border-gray-200 shadow-sm text-sm hover:border-indigo-400 transition-colors flex justify-between items-center group cursor-grab"
                                                 draggable="true"
                                                 data-id="{{ $reqSubject->id }}"
                                                 data-name="{{ $reqSubject->straniPredmet->naziv }}"
                                                 data-type="foreign">
                                                <span>{{ $reqSubject->straniPredmet->naziv }} ({{ $reqSubject->straniPredmet->ects }} ECTS)</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <!-- Drop Zone & Linked List -->
                            <div class="flex flex-col space-y-4">
                                <!-- Drop Zone -->
                                <div id="drop-zone" class="bg-blue-50 border-2 border-dashed border-blue-300 rounded-lg p-6 flex flex-col items-center justify-center transition-colors min-h-[150px]">
                                    <p class="text-blue-500 font-medium text-center mb-2">Drag foreign subject and your subject here to link</p>
                                    <div class="flex items-center space-x-4 w-full justify-center">
                                        <div id="drop-slot-foreign" class="w-1/2 h-12 bg-white border border-gray-200 rounded flex items-center justify-center text-xs text-gray-400 text-center px-2">
                                            Foreign Subject
                                        </div>
                                        <span class="text-gray-400">+</span>
                                        <div id="drop-slot-local" class="w-1/2 h-12 bg-white border border-gray-200 rounded flex items-center justify-center text-xs text-gray-400 text-center px-2">
                                            Your Subject
                                        </div>
                                    </div>
                                </div>

                                <!-- Linked List -->
                                <div class="flex-1 bg-white rounded-lg border border-gray-200 shadow-sm flex flex-col">
                                    <div class="p-3 border-b border-gray-200 bg-gray-50 rounded-t-lg flex justify-between items-center">
                                        <h4 class="font-semibold text-gray-700">Mapped Pairs</h4>
                                        <button id="save-btn" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-1 px-3 rounded shadow transition-colors">
                                            Save Mappings
                                        </button>
                                    </div>
                                    <div id="linked-list" class="h-[350px] overflow-y-auto p-2 space-y-2">
                                        <!-- Pre-filled mappings if any -->
                                        @foreach($mappingRequest->subjects as $reqSubject)
                                            @if($reqSubject->fit_predmet_id)
                                                <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded shadow-sm text-sm" data-req-id="{{ $reqSubject->id }}" data-fit-id="{{ $reqSubject->fit_predmet_id }}">
                                                    <div class="flex-1 grid grid-cols-2 gap-2">
                                                        <div class="font-medium text-gray-800 truncate" title="{{ $reqSubject->straniPredmet->naziv }}">{{ $reqSubject->straniPredmet->naziv }}</div>
                                                        <div class="text-gray-600 truncate" title="{{ $reqSubject->fitPredmet->naziv }}">{{ $reqSubject->fitPredmet->naziv }}</div>
                                                    </div>
                                                    <button type="button" class="ml-3 text-red-500 hover:text-red-700 font-bold px-2" onclick="unlinkPair(this, '{{ $reqSubject->id }}', '{{ $reqSubject->straniPredmet->naziv }}', '{{ $reqSubject->straniPredmet->ects }}')">&times;</button>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Professor Subjects Column -->
                            <div class="flex flex-col bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                                <h4 class="font-semibold text-gray-700 mb-2">Your Subjects</h4>
                                <input type="text" id="search-local" placeholder="Search Subject..." class="mb-2 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <div id="local-list" class="h-[500px] overflow-y-auto space-y-2 p-1 border border-gray-100 rounded bg-gray-50">
                                    @foreach($professorSubjects as $subject)
                                        <div class="draggable-item bg-white p-2 rounded border border-gray-200 shadow-sm text-sm hover:border-indigo-400 transition-colors flex justify-between items-center group cursor-grab"
                                             draggable="true"
                                             data-id="{{ $subject->id }}"
                                             data-name="{{ $subject->naziv }}"
                                             data-type="local">
                                            <span>{{ $subject->naziv }} ({{ $subject->ects }} ECTS)</span>
                                        </div>
                                    @endforeach
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
            // State
            let state = {
                pendingForeign: null,
                pendingLocal: null,
                mappings: [] // { request_subject_id, fit_predmet_id, foreign_name, local_name }
            };

            // DOM Elements
            const els = {
                foreignList: document.getElementById('foreign-list'),
                localList: document.getElementById('local-list'),
                linkedList: document.getElementById('linked-list'),
                dropZone: document.getElementById('drop-zone'),
                dropSlotForeign: document.getElementById('drop-slot-foreign'),
                dropSlotLocal: document.getElementById('drop-slot-local'),
                searchLocal: document.getElementById('search-local'),
                saveBtn: document.getElementById('save-btn'),
            };

            // Initialize existing mappings from DOM
            function initMappings() {
                const existing = els.linkedList.querySelectorAll('div[data-req-id]');
                existing.forEach(el => {
                    state.mappings.push({
                        request_subject_id: el.dataset.reqId,
                        fit_predmet_id: el.dataset.fitId,
                        // Names are not strictly needed for logic but good for state consistency if we re-render
                        foreign_name: el.querySelector('div > div:first-child').textContent,
                        local_name: el.querySelector('div > div:last-child').textContent
                    });
                });
                // updateSaveButton();
            }

            // --- Drag and Drop Logic ---

            function setupDragAndDrop() {
                const draggables = document.querySelectorAll('.draggable-item');
                draggables.forEach(item => {
                    item.addEventListener('dragstart', (e) => {
                        item.classList.add('dragging');
                        e.dataTransfer.setData('text/plain', JSON.stringify({
                            id: item.dataset.id,
                            type: item.dataset.type,
                            name: item.dataset.name
                        }));
                        e.dataTransfer.effectAllowed = 'move';
                    });

                    item.addEventListener('dragend', () => {
                        item.classList.remove('dragging');
                    });
                });

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
                        
                        if (itemData.type === 'foreign') {
                            state.pendingForeign = itemData;
                        } else { // local
                            state.pendingLocal = itemData;
                        }

                        renderDropZone();

                        if (state.pendingForeign && state.pendingLocal) {
                            addMapping(state.pendingForeign, state.pendingLocal);
                            state.pendingForeign = null;
                            state.pendingLocal = null;
                            renderDropZone();
                        }

                    } catch (err) {
                        console.error('Drop error', err);
                    }
                });
            }

            function renderDropZone() {
                if (state.pendingForeign) {
                    els.dropSlotForeign.textContent = state.pendingForeign.name;
                    els.dropSlotForeign.className = "w-1/2 h-12 bg-indigo-50 border border-indigo-300 text-indigo-700 rounded flex items-center justify-center text-xs text-center px-2 font-medium relative group cursor-pointer";
                    els.dropSlotForeign.innerHTML += `<span class="hidden group-hover:flex absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-4 h-4 items-center justify-center text-[10px]" onclick="clearSlot('foreign', event)">x</span>`;
                } else {
                    els.dropSlotForeign.textContent = "Foreign Subject";
                    els.dropSlotForeign.className = "w-1/2 h-12 bg-white border border-gray-200 rounded flex items-center justify-center text-xs text-gray-400 text-center px-2";
                }

                if (state.pendingLocal) {
                    els.dropSlotLocal.textContent = state.pendingLocal.name;
                    els.dropSlotLocal.className = "w-1/2 h-12 bg-indigo-50 border border-indigo-300 text-indigo-700 rounded flex items-center justify-center text-xs text-center px-2 font-medium relative group cursor-pointer";
                    els.dropSlotLocal.innerHTML += `<span class="hidden group-hover:flex absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-4 h-4 items-center justify-center text-[10px]" onclick="clearSlot('local', event)">x</span>`;
                } else {
                    els.dropSlotLocal.textContent = "Your Subject";
                    els.dropSlotLocal.className = "w-1/2 h-12 bg-white border border-gray-200 rounded flex items-center justify-center text-xs text-gray-400 text-center px-2";
                }
            }

            function addMapping(foreign, local) {
                // Check if foreign subject is already mapped
                if (state.mappings.some(m => m.request_subject_id == foreign.id)) {
                    alert('This foreign subject is already mapped.');
                    return;
                }

                state.mappings.push({
                    request_subject_id: foreign.id,
                    fit_predmet_id: local.id,
                    foreign_name: foreign.name,
                    local_name: local.name
                });

                // Remove from foreign list
                const foreignEl = els.foreignList.querySelector(`[data-id="${foreign.id}"]`);
                if (foreignEl) foreignEl.remove();

                renderLinkedList();
                // updateSaveButton();
            }

            function renderLinkedList() {
                // We only render NEW mappings here, or re-render all?
                // Simpler to re-render all based on state, but we have pre-filled ones.
                // Let's clear and re-render all from state.
                els.linkedList.innerHTML = '';
                state.mappings.forEach(m => {
                    const el = document.createElement('div');
                    el.className = 'flex items-center justify-between p-3 bg-white border border-gray-200 rounded shadow-sm text-sm';
                    el.dataset.reqId = m.request_subject_id;
                    el.dataset.fitId = m.fit_predmet_id;
                    el.innerHTML = `
                        <div class="flex-1 grid grid-cols-2 gap-2">
                            <div class="font-medium text-gray-800 truncate" title="${m.foreign_name}">${m.foreign_name}</div>
                            <div class="text-gray-600 truncate" title="${m.local_name}">${m.local_name}</div>
                        </div>
                        <button type="button" class="ml-3 text-red-500 hover:text-red-700 font-bold px-2" onclick="unlinkPair(this, '${m.request_subject_id}', '${m.foreign_name}', '0')">&times;</button>
                    `;
                    els.linkedList.appendChild(el);
                });
            }

            window.unlinkPair = function(btn, reqId, name, ects) {
                // Remove from state
                const idx = state.mappings.findIndex(m => m.request_subject_id == reqId);
                if (idx > -1) {
                    state.mappings.splice(idx, 1);
                }

                // Add back to foreign list
                const div = document.createElement('div');
                div.className = 'draggable-item bg-white p-2 rounded border border-gray-200 shadow-sm text-sm hover:border-indigo-400 transition-colors flex justify-between items-center group cursor-grab';
                div.draggable = true;
                div.dataset.id = reqId;
                div.dataset.name = name;
                div.dataset.type = 'foreign';
                div.innerHTML = `<span>${name}</span>`; // ECTS is lost if we don't store it, but name is enough for now or we can store it in dataset
                
                // Re-attach drag event
                div.addEventListener('dragstart', (e) => {
                    div.classList.add('dragging');
                    e.dataTransfer.setData('text/plain', JSON.stringify({
                        id: reqId,
                        type: 'foreign',
                        name: name
                    }));
                    e.dataTransfer.effectAllowed = 'move';
                });
                div.addEventListener('dragend', () => {
                    div.classList.remove('dragging');
                });

                els.foreignList.appendChild(div);

                renderLinkedList();
                // updateSaveButton();
            };

            window.clearSlot = function(type, e) {
                e.stopPropagation();
                if (type === 'foreign') state.pendingForeign = null;
                if (type === 'local') state.pendingLocal = null;
                renderDropZone();
            };

            // function updateSaveButton() {
            //     if (state.mappings.length > 0) {
            //         els.saveBtn.classList.remove('hidden');
            //     } else {
            //         els.saveBtn.classList.add('hidden');
            //     }
            // }

            // --- Search ---
            els.searchLocal.addEventListener('input', () => {
                const query = els.searchLocal.value.toLowerCase();
                const items = els.localList.querySelectorAll('.draggable-item');
                items.forEach(item => {
                    if (item.dataset.name.toLowerCase().includes(query)) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });
            });

            // --- Save ---
            els.saveBtn.addEventListener('click', async () => {
                // if (!confirm('Are you sure you want to save these mappings and complete the request?')) return;

                const mappings = state.mappings.map(m => ({
                    request_subject_id: m.request_subject_id,
                    fit_predmet_id: m.fit_predmet_id
                }));

                try {
                    const response = await fetch('{{ route("mapping-request.update", $mappingRequest->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            mappings: mappings
                        })
                    });

                    if (response.ok) {
                        // alert('Mappings saved successfully!');
                        window.location.href = '{{ route("profesorDashboardShow") }}';
                    } else {
                        alert('Failed to save mappings. Please try again.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred.');
                }
            });

            initMappings();
            setupDragAndDrop();
        });
    </script>
</x-app-layout>
