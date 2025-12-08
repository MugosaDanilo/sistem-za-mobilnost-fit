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
            $fit = $fakulteti->firstWhere('naziv', 'FIT');
        @endphp

        <div class="w-[45%] bg-white border border-gray-200 rounded-xl shadow p-6 transition-all duration-300">
            <h2 class="text-xl font-semibold mb-4">Information</h2>

            <!-- Student Pretraga -->
            <div class="flex flex-col gap-4 mb-6">
                <label for="student_id" class="font-semibold">Student</label>
                <div class="relative">
                    <input type="text" 
                        id="student_search" 
                        placeholder="Pretraži studenta..." 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        autocomplete="off">
                    
                    <div id="student_search_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden"></div>

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

            <!-- Fakultet Pretraga -->
            <div class="flex flex-col gap-4 mb-6">
                <label for="fakultet_id" class="font-semibold">Dolazi sa</label>
                <div class="relative">
                    <input type="text" 
                        id="fakultet_search" 
                        placeholder="Pretraži fakultet..." 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        autocomplete="off">
                    
                    <div id="fakultet_search_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden"></div>

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

            <!-- Predmeti -->
            <h3 class="text-lg font-semibold mb-3">Domaci univerzitet (FIT)</h3>
            <div id="fitSubjects" class="subjects-container mb-3">
                @if($fit)
                    @foreach($fit->predmeti as $predmet)
                        <div class="uploaded-subject border border-gray-200 rounded-md bg-gray-50 px-4 py-2 hover:bg-gray-100 transition cursor-pointer" data-name="{{ $predmet->naziv }}">
                            <div class="flex items-start justify-between gap-3">
                                <span class="subject-title">{{ $predmet->naziv }}</span>
                            </div>
                            <div class="linked-pills mt-2 flex flex-wrap gap-2 text-sm"></div>
                        </div>
                    @endforeach
                @endif
            </div>

            <h3 class="text-lg font-semibold mb-3">Strani univerzitet</h3>
            <div id="availableSubjects" class="flex flex-col gap-3"></div>

            <form id="uploadForm" action="{{ route(auth()->user()->type === 0 ? 'admin.mobility.upload' : 'profesor.mobility.upload') }}" method="POST" enctype="multipart/form-data" class="add-subject flex items-center gap-2 mt-4">
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
        </div>

        <div class="w-[55%] bg-white border border-gray-200 rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Strani univerzitet - predmeti</h2>
            <div id="availableSubjectsRight" class="flex flex-col gap-3"></div>
        </div>
    </div>
</div>

<script>
    const fitSubjects = Array.from(document.querySelectorAll('#fitSubjects .uploaded-subject'));
    const links = {};
    let activeLeft = null;
    const MAX_LINKS = 4;

    const rightContainer = document.getElementById('availableSubjects');

    const fakultetSelect = document.getElementById('fakultet_id');
    const fakultetPredmeti = @json($fakulteti->mapWithKeys(function($fak) {
        if($fak->naziv !== 'FIT') return [$fak->id => $fak->predmeti->pluck('naziv')];
    }));

    fakultetSelect.addEventListener('change', () => {
        rightContainer.innerHTML = '';
        for (const key in links) delete links[key];
        fitSubjects.forEach(f => f.querySelector('.linked-pills').innerHTML = '');
        activeLeft = null;

        if(!fakultetSelect.value || !fakultetPredmeti[fakultetSelect.value]) return;

        fakultetPredmeti[fakultetSelect.value].forEach(sub => {
            const div = document.createElement('div');
            div.className = 'available-subject border border-gray-200 px-4 py-2 rounded-md bg-gray-50 hover:bg-gray-100 cursor-pointer';
            div.textContent = sub;
            div.dataset.name = sub;
            div.addEventListener('click', () => toggleLink(div));
            rightContainer.appendChild(div);
        });
    });

    function setActiveLeft(card) {
        fitSubjects.forEach(c => c.classList.remove('ring-2','ring-blue-500','bg-blue-50','border-blue-500','shadow-md'));
        fitSubjects.forEach(c => c.querySelector('.linked-pills').innerHTML='');
        if(!card){activeLeft=null; return;}
        activeLeft=card;
        card.classList.add('ring-2','ring-blue-500','border-blue-500','bg-blue-50','shadow-md');
        renderPills(card);
    }

    function toggleLink(rightCard) {
        if(!activeLeft) return;
        const leftName = activeLeft.dataset.name;
        const rightName = rightCard.dataset.name;
        if(!links[leftName]) links[leftName]=new Set();
        const set = links[leftName];
        if(set.has(rightName)){set.delete(rightName); rightCard.classList.remove('border-blue-400','bg-blue-50');}
        else{if(set.size>=MAX_LINKS)return; set.add(rightName); rightCard.classList.add('border-blue-400','bg-blue-50');}
        renderPills(activeLeft);
    }

    function renderPills(leftCard){
        const pillsWrap = leftCard.querySelector('.linked-pills');
        pillsWrap.innerHTML='';
        const set = links[leftCard.dataset.name] || new Set();
        [...set].forEach(name=>{
            const pill=document.createElement('span');
            pill.className='inline-flex items-center gap-2 px-2 py-1 rounded-full bg-blue-100 text-blue-700 border border-blue-200';
            pill.textContent=name;
            const x=document.createElement('button');
            x.type='button'; x.textContent='×';
            x.className='leading-none'; x.onclick=e=>{e.stopPropagation(); set.delete(name); renderPills(leftCard);};
            pill.appendChild(x); pillsWrap.appendChild(pill);
        });
    }

    fitSubjects.forEach(card=>{card.addEventListener('click',()=>{setActiveLeft(card);});});
</script>

</x-app-layout>

