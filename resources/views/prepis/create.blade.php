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

            <!-- Lijeva strana: Informacije i upload -->
            <div class="w-[45%] bg-white border border-gray-200 rounded-xl shadow p-6 transition-all duration-300">
                <h2 class="text-xl font-semibold mb-4">Information</h2>

                <!-- Student -->
                <div class="flex flex-col gap-4 mb-6">
                    <label for="student_id" class="font-semibold">Student</label>
                    <div class="relative">
                        <input type="text" 
                            id="student_search" 
                            placeholder="Pretraži studenta..." 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            autocomplete="off">
                        
                        <div id="student_search_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden">
                        </div>

                        <select id="student_id" name="student_id" class="hidden">
                            <option value="">-- Odaberite studenta --</option>
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

                <!-- Fakultet -->
                <div class="flex flex-col gap-4 mb-6">
                    <label for="fakultet_id" class="font-semibold">Fakultet</label>
                    <div class="relative">
                        <input type="text" 
                            id="fakultet_search" 
                            placeholder="Pretraži fakultet..." 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            autocomplete="off">
                        
                        <div id="fakultet_search_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden">
                        </div>

                        <select id="fakultet_id" name="fakultet_id" class="hidden">
                            <option value="">-- Odaberite fakultet --</option>
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

                <!-- Datumi -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="flex flex-col gap-2">
                        <label for="datum_pocetka" class="font-semibold">Datum početka</label>
                        <input type="date" id="datum_pocetka" name="datum_pocetka" 
                            value="{{ old('datum_pocetka') }}"
                            class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="datum_kraja" class="font-semibold">Datum kraja</label>
                        <input type="date" id="datum_kraja" name="datum_kraja" 
                            value="{{ old('datum_kraja') }}"
                            class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Subjects -->
                <h3 class="text-lg font-semibold mb-3">Subjects</h3>
                <div id="subjectList" class="subjects-container mb-3"></div>

                <!-- Upload form -->
                <form id="uploadForm" action="{{ route(auth()->user()->type === 0 ? 'admin.mobility.upload' : 'profesor.mobility.upload') }}" method="POST" enctype="multipart/form-data" class="add-subject flex items-center gap-2 mt-auto">
                    @csrf
                    <input type="hidden" name="ime" id="hiddenIme">
                    <input type="hidden" name="prezime" id="hiddenPrezime">
                    <input type="hidden" name="fakultet" id="hiddenFakultet">
                    <input type="hidden" name="broj_indeksa" id="hiddenBrojIndeksa">

                    <input type="file" name="word_file" accept=".doc,.docx" class="hidden" id="wordFileInput">
                    <button type="button" class="btn bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg" onclick="document.getElementById('wordFileInput').click()">
                        Upload ToR
                    </button>

                    <button type="button"
                        class="btn bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg"
                        id="exportButton">
                        Export Word
                    </button>

                    <button type="button"
                        class="btn bg-purple-600 hover:bg-purple-700 text-white font-semibold px-4 py-2 rounded-lg"
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

            <!-- Desna strana: Available subjects -->
            <div class="w-[55%] bg-white border border-gray-200 rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Available Subjects</h2>

                <!-- Dva kontejnera: FIT i ostali fakulteti -->
                <h3 class="font-semibold mb-2">Domaći univerzitet (FIT)</h3>
                <div id="domaciSubjects" class="flex flex-col gap-3 mb-4"></div>

                <h3 class="font-semibold mb-2">Strani univerzitet</h3>
                <div id="availableSubjects" class="flex flex-col gap-3"></div>
            </div>

        </div>
    </div>

    <script>
/* -------------------------------
   GLOBAL STATE
--------------------------------*/
const links = {};              // { leftName : Set(rightNames) }
let activeLeft = null;
const MAX_LINKS = 10;

/* -------------------------------
   DOM REFERENCES
--------------------------------*/
const leftCards = Array.from(document.querySelectorAll('.uploaded-subject'));
const domaciContainer = document.getElementById('domaciSubjects');
const straniContainer = document.getElementById('availableSubjects');
const fakultetSelect = document.getElementById('fakultet_id');

/* -------------------------------
   DATA FROM BACKEND
--------------------------------*/
const fitPredmeti = @json(
    $fakulteti->firstWhere('naziv','FIT')->predmeti->pluck('naziv')
);

const straniPredmeti = @json(
    $fakulteti
        ->filter(fn($f)=>$f->naziv!=='FIT')
        ->mapWithKeys(fn($f)=>[$f->id=>$f->predmeti->pluck('naziv')])
);

/* -------------------------------
   INIT – FIT SUBJECTS ALWAYS LOADED
--------------------------------*/
function renderFIT() {
    domaciContainer.innerHTML = '';
    fitPredmeti.forEach(name => {
        domaciContainer.appendChild(createRightCard(name));
    });
}

/* -------------------------------
   RENDER STRANI SUBJECTS
--------------------------------*/
function renderStrani(fakId) {
    straniContainer.innerHTML = '';
    if (!straniPredmeti[fakId]) return;

    straniPredmeti[fakId].forEach(name => {
        straniContainer.appendChild(createRightCard(name));
    });
}

/* -------------------------------
   CREATE RIGHT CARD
--------------------------------*/
function createRightCard(name) {
    const div = document.createElement('div');
    div.className =
        'available-subject border border-gray-200 px-4 py-2 rounded-md ' +
        'bg-gray-50 hover:bg-gray-100 cursor-pointer transition';

    div.dataset.name = name;
    div.textContent = name;

    div.addEventListener('click', () => toggleLink(div));
    return div;
}

/* -------------------------------
   LEFT CARD CLICK
--------------------------------*/
leftCards.forEach(card => {
    card.addEventListener('click', () => setActiveLeft(card));
});

/* -------------------------------
   SET ACTIVE LEFT
--------------------------------*/
function setActiveLeft(card) {
    // reset visuals
    leftCards.forEach(c =>
        c.classList.remove('ring-2','ring-blue-500','bg-blue-50','border-blue-500')
    );

    document.querySelectorAll('.available-subject')
        .forEach(c => c.classList.remove('border-blue-400','bg-blue-50'));

    activeLeft = card;
    if (!card) return;

    card.classList.add('ring-2','ring-blue-500','bg-blue-50','border-blue-500');
    renderPillsForLeft(card);
}

/* -------------------------------
   TOGGLE LINK
--------------------------------*/
function toggleLink(rightCard) {
    if (!activeLeft) return;

    const left = activeLeft.dataset.name;
    const right = rightCard.dataset.name;

    if (!links[left]) links[left] = new Set();

    const set = links[left];

    if (set.has(right)) {
        set.delete(right);
        rightCard.classList.remove('border-blue-400','bg-blue-50');
    } else {
        if (set.size >= MAX_LINKS) return;
        set.add(right);
        rightCard.classList.add('border-blue-400','bg-blue-50');
    }

    renderPillsForLeft(activeLeft);
}

/* -------------------------------
   RENDER PILLS (NO DUPLICATES)
--------------------------------*/
function renderPillsForLeft(leftCard) {
    const wrap = leftCard.querySelector('.linked-pills');
    wrap.innerHTML = '';

    const leftName = leftCard.dataset.name;
    if (!links[leftName]) return;

    links[leftName].forEach(rightName => {
        const pill = document.createElement('span');
        pill.className =
            'px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs ' +
            'flex items-center gap-1';

        pill.innerHTML = `
            ${rightName}
            <button class="text-red-500 hover:text-red-700 font-bold">&times;</button>
        `;

        pill.querySelector('button').addEventListener('click', e => {
            e.stopPropagation();
            links[leftName].delete(rightName);
            renderPillsForLeft(leftCard);
        });

        wrap.appendChild(pill);
    });
}

/* -------------------------------
   FAKULTET CHANGE
--------------------------------*/
fakultetSelect.addEventListener('change', () => {
    // reset links & UI
    Object.keys(links).forEach(k => delete links[k]);
    document.querySelectorAll('.linked-pills').forEach(p => p.innerHTML = '');
    setActiveLeft(null);

    renderStrani(fakultetSelect.value);
});

/* -------------------------------
   START
--------------------------------*/
renderFIT();
if (fakultetSelect.value) {
    renderStrani(fakultetSelect.value);
}
</script>


</x-app-layout>

