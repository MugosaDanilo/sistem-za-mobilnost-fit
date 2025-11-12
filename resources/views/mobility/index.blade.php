<x-app-layout>
    <div class="py-10 max-w-6xl mx-auto px-6">
        <div class="flex gap-8 items-start">

            @php
                $hasCourses = !empty(session('courses'));
            @endphp

            <div class="w-[45%] bg-white border border-gray-200 rounded-xl shadow p-6 transition-all duration-300">
                <h2 class="text-xl font-semibold mb-4">Information</h2>

               <div class="flex flex-col gap-4 mb-6">
                <input type="text" id="ime" name="ime" value="{{ old('ime') }}" placeholder="First Name"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" />

                <input type="text" id="prezime" name="prezime" value="{{ old('prezime') }}" placeholder="Last Name"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" />

                <input type="text" id="fakultet" name="fakultet" value="{{ old('fakultet') }}" placeholder="Faculty Name"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            </div>


                <h3 class="text-lg font-semibold mb-3">Subjects</h3>
                <div id="subjectList" class="subjects-container mb-3"></div>

                <form id="uploadForm" action="{{ route(auth()->user()->type === 0 ? 'admin.mobility.upload' : 'profesor.mobility.upload') }}" method="POST" enctype="multipart/form-data" class="add-subject flex items-center gap-2 mt-auto">
                    @csrf
                    <input type="hidden" name="ime" id="hiddenIme">
                    <input type="hidden" name="prezime" id="hiddenPrezime">
                    <input type="hidden" name="fakultet" id="hiddenFakultet">

                    <input type="file" name="word_file" accept=".doc,.docx" class="hidden" id="wordFileInput">
                    <button type="button" class="btn bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg" onclick="document.getElementById('wordFileInput').click()">
                        Upload ToR
                    </button>

                    <button type="button"
                        class="btn bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg"
                        id="exportButton">
                        Export Word
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
                    @php
                    $subjects = [
                        'Engineering Mathematics',
                        'Basic Programming',
                        'CAD Design',
                        'Information Technology',
                        'English Language 1',
                        'Statistics',
                        'Computer Networks',
                        'Introduction to Databases',
                        'Object-Oriented Programming 1',
                        'English Language 2',
                        'Operating Systems',
                        'Web Programming',
                    ];
                    @endphp
                    @foreach($subjects as $subject)
                        <div class="available-subject border border-gray-200 px-4 py-2 rounded-md bg-gray-50 hover:bg-gray-100 transition cursor-pointer" data-name="{{ $subject }}">
                            {{ $subject }}
                        </div>
                    @endforeach
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
        const rightCards = Array.from(document.querySelectorAll('.available-subject'));

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
            document.getElementById('hiddenIme').value = document.getElementById('ime').value;
            document.getElementById('hiddenPrezime').value = document.getElementById('prezime').value;
            document.getElementById('hiddenFakultet').value = document.getElementById('fakultet').value;
            form.submit();
        });

        document.getElementById('exportButton')?.addEventListener('click', () => {
            const ime = document.getElementById('ime')?.value.trim();
            const prezime = document.getElementById('prezime')?.value.trim();
            const fakultet = document.getElementById('fakultet')?.value.trim();

            if (!ime || !prezime || !fakultet) {
                alert('Molimo unesite ime, prezime i fakultet prije eksportovanja.');
                return;
            }

            const hasAnyLinks = Object.values(links).some(set => set.size > 0);

            if (!hasAnyLinks) {
                alert('Molimo povežite barem jedan predmet prije eksportovanja.');
                return;
            }


            const plainLinks = {};
            for (const [key, value] of Object.entries(links)) {
                plainLinks[key] = Array.from(value);
            }

            fetch("{{ route(auth()->user()->type === 0 ? 'admin.mobility.export' : 'profesor.mobility.export') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                body: JSON.stringify({
                    ime,
                    prezime,
                    fakultet,
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

    </script>
</x-app-layout>
