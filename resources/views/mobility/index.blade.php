<x-app-layout>

    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-800 p-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <div class="py-10 max-w-6xl mx-auto px-6">
        <div class="flex gap-8 items-start">

            @php
                $hasCourses = !empty(session('courses'));
            @endphp

            <div class="w-[45%] bg-white border border-gray-200 rounded-xl shadow p-6 transition-all duration-300">
                <h2 class="text-xl font-semibold mb-4">Information</h2>

                <div class="flex flex-col gap-4 mb-6">
                    <label for="student_id" class="font-semibold">Student</label>
                    <div class="relative">
                        <input type="text" 
                            id="student_search" 
                            placeholder="Search student..." 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            autocomplete="off">
                        
                        <div id="student_search_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden">
                        </div>

                        <select id="student_id" name="student_id" class="hidden">
                            <option value="">-- Select a student --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}"
                                    {{ old('student_id') == $student->id ? 'selected' : '' }}
                                    data-ime="{{ $student->ime }}"
                                    data-prezime="{{ $student->prezime }}"
                                    data-br_indexa="{{ $student->br_indexa }}">
                                    {{ $student->ime }} {{ $student->prezime }} ({{ $student->br_indexa }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="flex flex-col gap-4 mb-6">
                    <label for="fakultet_id" class="font-semibold">Faculty</label>
                    <div class="relative">
                        <input type="text" 
                            id="fakultet_search" 
                            placeholder="Search faculty..." 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            autocomplete="off">
                        
                        <div id="fakultet_search_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden">
                        </div>

                        <select id="fakultet_id" name="fakultet_id" class="hidden">
                            <option value="">-- Select a faculty --</option>
                            @foreach($fakulteti as $fakultet)
                                @if($fakultet->naziv !== 'FIT')
                                    <option value="{{ $fakultet->id }}" 
                                        {{ old('fakultet_id') == $fakultet->id ? 'selected' : '' }}
                                        data-naziv="{{ $fakultet->naziv }}">
                                        {{ $fakultet->naziv }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="flex flex-col gap-2">
                        <label for="datum_pocetka" class="font-semibold">Start date</label>
                        <input type="date" id="datum_pocetka" name="datum_pocetka" 
                            value="{{ old('datum_pocetka') }}"
                            class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="datum_kraja" class="font-semibold">End date</label>
                        <input type="date" id="datum_kraja" name="datum_kraja" 
                            value="{{ old('datum_kraja') }}"
                            class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <h3 class="text-lg font-semibold mb-3">Subjects</h3>
                <div id="subjectList" class="subjects-container mb-3"></div>

                <form id="uploadForm" action="{{ route((int)auth()->user()->type === 0 ? 'admin.mobility.upload' : 'profesor.mobility.upload') }}" method="POST" enctype="multipart/form-data" class="add-subject flex items-center gap-2 mt-auto">
                    @csrf
                    <input type="hidden" name="ime" id="hiddenIme">
                    <input type="hidden" name="prezime" id="hiddenPrezime">
                    <input type="hidden" name="fakultet" id="hiddenFakultet">
                    <input type="hidden" name="broj_indeksa" id="hiddenBrojIndeksa">


                    <input type="file" name="word_file" accept=".doc,.docx" class="hidden" id="wordFileInput">
                    <button type="button" class="btn bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105" onclick="document.getElementById('wordFileInput').click()">
                        Upload ToR
                    </button>

                    <button type="button"
                        class="btn bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105"
                        id="exportButton">
                        Export Word
                    </button>

                    <button type="button"
                        class="btn bg-purple-600 hover:bg-purple-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105"
                        id="saveButton">
                        Save LA
                    </button>

                </form>

                @if($hasCourses)
                    <div class="grid gap-3 mt-6" id="uploadedSubjects">
                        @foreach(session('courses') as $course)
                            @php
                                $name = is_array($course)
                                ? ($course['Course'] ?? $course['Naziv'] ?? $course['name'] ?? $course['Subject'] ?? $course['Predmet'] ?? null)
                                : $course;
                            @endphp
                            @if(!empty($name))
                                <div class="uploaded-subject border border-gray-200 rounded-md bg-gray-50 px-4 py-2 hover:bg-gray-100 transition cursor-pointer" data-name="{{ $name }}">
                                    <div class="flex items-start justify-between gap-3">
                                        <span class="subject-title">{{ $name }}</span>
                                    </div>
                                    <div class="linked-pills mt-2 flex flex-wrap gap-2 text-sm"></div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="w-[55%] bg-white border border-gray-200 rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Available Subjects</h2>

                <div id="availableSubjects" class="flex flex-col gap-3">
                  
                </div>
            </div>

        </div>
    </div>

    <script>
        const uploadedCourses = @json(session('courses', []));
        
        const fileInput = document.getElementById('wordFileInput');
        const form = document.getElementById('uploadForm');
        if (fileInput && form) {
            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) form.submit();
            });
        }

        const links = {};
        let activeLeft = null;
        const MAX_LINKS = 4;

        const leftCards = Array.from(document.querySelectorAll('.uploaded-subject'));
        const rightCards =  refreshRightCards();

        const fakultetPredmeti = @json($fakulteti->mapWithKeys(function($fak) {
            return [$fak->id => $fak->predmeti->pluck('naziv')];
        }));

        const fakultetSelect = document.getElementById('fakultet_id');
        const availableSubjectsContainer = document.getElementById('availableSubjects');

        fakultetSelect.addEventListener('change', () => {
            const fakultetId = fakultetSelect.value;
            availableSubjectsContainer.innerHTML = ''; 

            for (const key in links) delete links[key];
            document.querySelectorAll('.linked-pills').forEach(el => el.innerHTML = '');
            setActiveLeft(null);

            if (!fakultetId || !fakultetPredmeti[fakultetId]) return;

            fakultetPredmeti[fakultetId].forEach(subject => {
                const div = document.createElement('div');
                div.className = 'available-subject border border-gray-200 px-4 py-2 rounded-md bg-gray-50 hover:bg-gray-100 transition cursor-pointer';
                div.dataset.name = subject;
                div.textContent = subject;

                div.addEventListener('click', () => toggleLink(div)); // linking with the left side
                availableSubjectsContainer.appendChild(div);
            });
        });

        if (fakultetSelect.value) {
            fakultetSelect.dispatchEvent(new Event('change'));
        }

        function refreshRightCards() {
            return Array.from(document.querySelectorAll('.available-subject'));
        }



        function clearActiveBadges() {
            document.querySelectorAll('.uploaded-subject .active-badge').forEach(el => el.remove());
        }

        function addActiveBadge(card) {
            const badge = document.createElement('span');
            badge.className = 'active-badge absolute top-2 right-2 text-xs px-2 py-0.5 rounded-full bg-blue-600 text-white';
            badge.textContent = 'Selected';
            card.style.position = 'relative';
            card.appendChild(badge);
        }

        function setActiveLeft(card) {
            leftCards.forEach(c => c.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50', 'border-blue-500', 'shadow-md'));
            clearActiveBadges();

            rightCards.forEach(c => c.classList.remove('border-blue-400', 'bg-blue-50'));

            if (!card) {
                activeLeft = null;
                return;
            }

            activeLeft = card;
            card.classList.add('ring-2', 'ring-blue-500', 'border-blue-500', 'bg-blue-50', 'shadow-md');
            addActiveBadge(card);

            const leftName = card.dataset.name;
            const set = links[leftName] || new Set();
            rightCards.forEach(r => {
                if (set.has(r.dataset.name)) {
                    r.classList.add('border-blue-400', 'bg-blue-50');
                }
            });
        }

        function toggleLink(rightCard) {
            if (!activeLeft) return;
            const leftName = activeLeft.dataset.name;
            const rightName = rightCard.dataset.name;

            if (!links[leftName]) links[leftName] = new Set();

            const currentSet = links[leftName];

            if (currentSet.has(rightName)) {
                currentSet.delete(rightName);
                rightCard.classList.remove('border-blue-400', 'bg-blue-50');
            } else {
                if (currentSet.size >= MAX_LINKS) {
                    return;
                }
                currentSet.add(rightName);
                rightCard.classList.add('border-gray-400', 'bg-blue-50');
            }

            renderPillsForLeft(activeLeft);
        }

        function renderPillsForLeft(leftCard) {
            const leftName = leftCard.dataset.name;
            const pillsWrap = leftCard.querySelector('.linked-pills');
            if (!pillsWrap) return;

            pillsWrap.innerHTML = '';
            const set = links[leftName] || new Set();
            [...set].forEach(name => {
                const pill = document.createElement('span');
                pill.className = 'inline-flex items-center gap-2 px-2 py-1 rounded-full bg-blue-100 text-blue-700 border border-blue-200';
                pill.textContent = name;

                const x = document.createElement('button');
                x.type = 'button';
                x.textContent = '×';
                x.className = 'leading-none';
                x.onclick = (e) => {
                    e.stopPropagation();
                    links[leftName].delete(name);
                    const rc = rightCards.find(rc => rc.dataset.name === name);
                    rc && rc.classList.remove('border-blue-400', 'bg-blue-50');
                    renderPillsForLeft(leftCard);
                };

                pill.appendChild(x);
                pillsWrap.appendChild(pill);
            });
        }

        leftCards.forEach(card => {
            card.addEventListener('click', () => {
                if (activeLeft === card) {
                    setActiveLeft(null);
                } else {
                    setActiveLeft(card);
                    renderPillsForLeft(card);
                }
            });
        });

        rightCards.forEach(card => {
            card.addEventListener('click', () => toggleLink(card));
        });


        fileInput.addEventListener('change', () => {
            const studentSelect = document.getElementById('student_id');
            const selectedOption = studentSelect.options[studentSelect.selectedIndex];
            const fakultetSelect = document.getElementById('fakultet_id');

            document.getElementById('hiddenIme').value = selectedOption?.dataset.ime || '';
            document.getElementById('hiddenPrezime').value = selectedOption?.dataset.prezime || '';
            document.getElementById('hiddenFakultet').value = fakultetSelect.options[fakultetSelect.selectedIndex]?.dataset.naziv || '';
            document.getElementById('hiddenBrojIndeksa').value = selectedOption?.dataset.br_indexa || '';
            
            const hiddenStudentId = document.createElement('input');
            hiddenStudentId.type = 'hidden';
            hiddenStudentId.name = 'student_id';
            hiddenStudentId.value = studentSelect.value;
            form.appendChild(hiddenStudentId);

            const hiddenFakultetId = document.createElement('input');
            hiddenFakultetId.type = 'hidden';
            hiddenFakultetId.name = 'fakultet_id';
            hiddenFakultetId.value = fakultetSelect.value;
            form.appendChild(hiddenFakultetId);

            const hiddenDatumPocetka = document.createElement('input');
            hiddenDatumPocetka.type = 'hidden';
            hiddenDatumPocetka.name = 'datum_pocetka';
            hiddenDatumPocetka.value = document.getElementById('datum_pocetka')?.value || '';
            form.appendChild(hiddenDatumPocetka);

            const hiddenDatumKraja = document.createElement('input');
            hiddenDatumKraja.type = 'hidden';
            hiddenDatumKraja.name = 'datum_kraja';
            hiddenDatumKraja.value = document.getElementById('datum_kraja')?.value || '';
            form.appendChild(hiddenDatumKraja);

            form.submit();
        });

        document.getElementById('exportButton')?.addEventListener('click', () => {
            const studentSelect = document.getElementById('student_id');
            const selectedOption = studentSelect.options[studentSelect.selectedIndex];
            
            const ime = selectedOption?.dataset.ime || '';
            const prezime = selectedOption?.dataset.prezime || '';
            const brojIndeksa = selectedOption?.dataset.br_indexa || '';

            const fakultetSelect = document.getElementById('fakultet_id');
            const fakultet = fakultetSelect.options[fakultetSelect.selectedIndex]?.dataset.naziv || '';

            if (!ime || !prezime || !fakultet) {
                alert('Please enter the first name, last name, and faculty before exporting.');
                return;
            }

            if (!brojIndeksa) {
                alert('Please enter the index number before continuing.');
                return;
            }

            const hasAnyLinks = Object.values(links).some(set => set.size > 0);

            if (!hasAnyLinks) {
                alert('Please link at least one subject before exporting.');
                return;
            }


            const plainLinks = {};
            for (const [key, value] of Object.entries(links)) {
                plainLinks[key] = Array.from(value);
            }

            fetch("{{ route((int)auth()->user()->type === 0 ? 'admin.mobility.export' : 'profesor.mobility.export') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                body: JSON.stringify({
                    ime,
                    prezime,
                    fakultet,
                    brojIndeksa,
                    links: plainLinks,
                    courses: uploadedCourses
                })
            })
            .then(res => {
                if (!res.ok) throw new Error("Export failed");
                return res.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                const safeIme = ime.replace(/[^a-z0-9šđčćž]+/gi, '_');
                const safePrezime = prezime.replace(/[^a-z0-9šđčćž]+/gi, '_');
                a.download = `Mobilnost_${safeIme}_${safePrezime}.docx`;
                document.body.appendChild(a);
                a.click();
                a.remove();
            })
            .catch(err => alert(err.message));
    });

    document.getElementById('saveButton')?.addEventListener('click', () => {
        const studentSelect = document.getElementById('student_id');
        const selectedOption = studentSelect.options[studentSelect.selectedIndex];

        const ime = selectedOption?.dataset.ime || '';
        const prezime = selectedOption?.dataset.prezime || '';
        const brojIndeksa = selectedOption?.dataset.br_indexa || '';

        const fakultetSelect = document.getElementById('fakultet_id');
        const fakultet = fakultetSelect.options[fakultetSelect.selectedIndex]?.dataset.naziv || '';
      
        if (!ime || !prezime || !fakultet) {
            alert('Please enter the first name, last name, and faculty before saving.');
            return;
        }

        if (!brojIndeksa) {
            alert('Please enter the index number before continuing.');
            return;
        }

        const hasAnyLinks = Object.values(links).some(set => set.size > 0);

        if (!hasAnyLinks) {
            alert('Please link at least one subject before exporting.');
            return;
        }

        const plainLinks = {};
        for (const [key, value] of Object.entries(links)) {
            plainLinks[key] = Array.from(value);
        }

        const datumPocetka = document.getElementById('datum_pocetka')?.value;
        const datumKraja = document.getElementById('datum_kraja')?.value;

        if (!datumPocetka || !datumKraja) {
            alert('Please enter the mobility dates.');
            return;
        }

        const saveRoute = "{{ route((int)auth()->user()->type === 0 ? 'admin.mobility.save' : 'profesor.mobility.save') }}";

        fetch(saveRoute, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            body: JSON.stringify({
                ime,
                prezime,
                fakultet_id: fakultetSelect.value, 
                student_id: studentSelect.value,   
                broj_indeksa: brojIndeksa,
                datum_pocetka: datumPocetka,
                datum_kraja: datumKraja,
                links: plainLinks,
                courses: uploadedCourses
            })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
        })
        .catch(err => alert("Save failed: " + err));
    });

    const studentSelect = document.getElementById('student_id');
    const studentSearchInput = document.getElementById('student_search');
    const studentSearchResults = document.getElementById('student_search_results');
    
    const studentsData = Array.from(studentSelect.options)
        .filter(option => option.value)
        .map(option => ({
            id: option.value,
            text: option.text,
            ime: option.dataset.ime,
            prezime: option.dataset.prezime,
            br_indexa: option.dataset.br_indexa,
            element: option
        }));

    if (studentSelect.value) {
        const selected = studentsData.find(s => s.id === studentSelect.value);
        if (selected) {
            studentSearchInput.value = selected.text;
        }
    }

    studentSearchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        const filtered = studentsData.filter(student => 
            student.text.toLowerCase().includes(query)
        );

        renderSearchResults(filtered);
    });

    studentSearchInput.addEventListener('focus', () => {
        const query = studentSearchInput.value.toLowerCase();
        const filtered = studentsData.filter(student => 
            student.text.toLowerCase().includes(query)
        );
        renderSearchResults(filtered);
    });

    document.addEventListener('click', (e) => {
        if (!studentSearchInput.contains(e.target) && !studentSearchResults.contains(e.target)) {
            studentSearchResults.classList.add('hidden');
        }
    });

    function renderSearchResults(results) {
        studentSearchResults.innerHTML = '';
        
        if (results.length === 0) {
            const noResults = document.createElement('div');
            noResults.className = 'px-4 py-2 text-gray-500 italic';
            noResults.textContent = 'No results';
            studentSearchResults.appendChild(noResults);
        } else {
            results.forEach(student => {
                const div = document.createElement('div');
                div.className = 'px-4 py-2 hover:bg-blue-50 cursor-pointer transition-colors';
                div.textContent = student.text;
                
                div.addEventListener('click', () => {
                    studentSearchInput.value = student.text;
                    studentSelect.value = student.id;
                    studentSelect.dispatchEvent(new Event('change')); 
                    studentSearchResults.classList.add('hidden');
                });

                studentSearchResults.appendChild(div);
            });
        }

        studentSearchResults.classList.remove('hidden');
    }

    const fakultetSelectElement = document.getElementById('fakultet_id');
    const fakultetSearchInput = document.getElementById('fakultet_search');
    const fakultetSearchResults = document.getElementById('fakultet_search_results');
    
    const facultiesData = Array.from(fakultetSelectElement.options)
        .filter(option => option.value) // Skip placeholder
        .map(option => ({
            id: option.value,
            text: option.text,
            naziv: option.dataset.naziv,
            element: option
        }));

    if (fakultetSelectElement.value) {
        const selected = facultiesData.find(f => f.id === fakultetSelectElement.value);
        if (selected) {
            fakultetSearchInput.value = selected.text;
        }
    }

    fakultetSearchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        const filtered = facultiesData.filter(faculty => 
            faculty.text.toLowerCase().includes(query)
        );

        renderFacultySearchResults(filtered);
    });

    fakultetSearchInput.addEventListener('focus', () => {
        const query = fakultetSearchInput.value.toLowerCase();
        const filtered = facultiesData.filter(faculty => 
            faculty.text.toLowerCase().includes(query)
        );
        renderFacultySearchResults(filtered);
    });

    document.addEventListener('click', (e) => {
        if (!fakultetSearchInput.contains(e.target) && !fakultetSearchResults.contains(e.target)) {
            fakultetSearchResults.classList.add('hidden');
        }
    });

    function renderFacultySearchResults(results) {
        fakultetSearchResults.innerHTML = '';
        
        if (results.length === 0) {
            const noResults = document.createElement('div');
            noResults.className = 'px-4 py-2 text-gray-500 italic';
            noResults.textContent = 'No results';
            fakultetSearchResults.appendChild(noResults);
        } else {
            results.forEach(faculty => {
                const div = document.createElement('div');
                div.className = 'px-4 py-2 hover:bg-blue-50 cursor-pointer transition-colors';
                div.textContent = faculty.text;
                
                div.addEventListener('click', () => {
                    fakultetSearchInput.value = faculty.text;
                    fakultetSelectElement.value = faculty.id;
                    fakultetSelectElement.dispatchEvent(new Event('change')); 
                    fakultetSearchResults.classList.add('hidden');
                });

                fakultetSearchResults.appendChild(div);
            });
        }

        fakultetSearchResults.classList.remove('hidden');
    }

    </script>
</x-app-layout>
