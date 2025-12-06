@php

$editing = isset($student);
@endphp

<form method="POST" action="{{ $editing ? route('studenti.update', $student->id) : route('studenti.store') }}" id="student-form">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="grid grid-cols-2 gap-6">
        <!-- Lijevo polje - Informacije o stdentu -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informacije o studentu</h3>
            
            <div class="flex items-center gap-3">
                <label for="ime" class="w-32 text-sm font-medium text-gray-700">Ime <span class="text-red-500">*</span></label>
                <input type="text" 
                       id="ime" 
                       name="ime" 
                       value="{{ old('ime', $student->ime ?? '') }}"
                       class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 text-sm"
                       required>
            </div>
            @error('ime')
                <p class="text-red-500 text-sm ml-36">{{ $message }}</p>
            @enderror

            <div class="flex items-center gap-3">
                <label for="prezime" class="w-32 text-sm font-medium text-gray-700">Prezime <span class="text-red-500">*</span></label>
                <input type="text" 
                       id="prezime" 
                       name="prezime" 
                       value="{{ old('prezime', $student->prezime ?? '') }}"
                       class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 text-sm"
                       required>
            </div>
            @error('prezime')
                <p class="text-red-500 text-sm ml-36">{{ $message }}</p>
            @enderror

            <div class="flex items-center gap-3">
                <label for="br_indexa" class="w-32 text-sm font-medium text-gray-700">Broj indeksa <span class="text-red-500">*</span></label>
                <input type="text" 
                       id="br_indexa" 
                       name="br_indexa" 
                       value="{{ old('br_indexa', $student->br_indexa ?? '') }}"
                       class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 text-sm"
                       required>
            </div>
            @error('br_indexa')
                <p class="text-red-500 text-sm ml-36">{{ $message }}</p>
            @enderror

            <div class="flex items-center gap-3">
                <label for="email" class="w-32 text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email', $student->email ?? '') }}"
                       class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 text-sm"
                       required>
            </div>
            @error('email')
                <p class="text-red-500 text-sm ml-36">{{ $message }}</p>
            @enderror

            <div class="flex items-center gap-3">
                <label for="telefon" class="w-32 text-sm font-medium text-gray-700">Telefon <span class="text-red-500">*</span></label>
                <input type="text" 
                       id="telefon" 
                       name="telefon" 
                       value="{{ old('telefon', $student->telefon ?? '') }}"
                       class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 text-sm"
                       required>
            </div>
            @error('telefon')
                <p class="text-red-500 text-sm ml-36">{{ $message }}</p>
            @enderror

            <div class="flex items-center gap-3">
                <label for="jmbg" class="w-32 text-sm font-medium text-gray-700">JMBG <span class="text-red-500">*</span></label>
                <input type="text" 
                       id="jmbg" 
                       name="jmbg" 
                       value="{{ old('jmbg', $student->jmbg ?? '') }}"
                       maxlength="13"
                       class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 text-sm"
                       required>
            </div>
            @error('jmbg')
                <p class="text-red-500 text-sm ml-36">{{ $message }}</p>
            @enderror

            <div class="flex items-center gap-3">
                <label for="datum_rodjenja" class="w-32 text-sm font-medium text-gray-700">Datum rođenja <span class="text-red-500">*</span></label>
                <input type="date" 
                       id="datum_rodjenja" 
                       name="datum_rodjenja"
                       value="{{ old('datum_rodjenja', isset($student) && $student->datum_rodjenja ? $student->datum_rodjenja->format('Y-m-d') : '') }}"
                       class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 text-sm"
                       required>
            </div>
            @error('datum_rodjenja')
                <p class="text-red-500 text-sm ml-36">{{ $message }}</p>
            @enderror

            <div class="flex items-center gap-3">
                <label for="godina_studija" class="w-32 text-sm font-medium text-gray-700">Godina studija <span class="text-red-500">*</span></label>
                <input type="number" 
                       id="godina_studija" 
                       name="godina_studija" 
                       min="1" 
                       max="8"
                       value="{{ old('godina_studija', $student->godina_studija ?? '') }}"
                       class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 text-sm"
                       required>
            </div>
            @error('godina_studija')
                <p class="text-red-500 text-sm ml-36">{{ $message }}</p>
            @enderror

            <div class="flex items-center gap-3">
                <label for="nivo_studija_id" class="w-32 text-sm font-medium text-gray-700">Nivo studija <span class="text-red-500">*</span></label>
                <select id="nivo_studija_id" 
                        name="nivo_studija_id"
                        class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 text-sm bg-white"
                        required>
                    <option value="">Izaberi nivo studija</option>
                    @if(isset($nivoiStudija) && $nivoiStudija->count() > 0)
                        @foreach($nivoiStudija as $nivo)
                            <option value="{{ $nivo->id }}" 
                                    {{ old('nivo_studija_id', isset($student) ? $student->nivo_studija_id : '') == $nivo->id ? 'selected' : '' }}>
                                {{ $nivo->naziv }}
                            </option>
                        @endforeach
                    @else
                        <option value="1">Osnovne</option>
                        <option value="2">Master</option>
                    @endif
                </select>
            </div>
            @error('nivo_studija_id')
                <p class="text-red-500 text-sm ml-36">{{ $message }}</p>
            @enderror
        </div>

        <!-- Desno polje - Predmeti -->
        <div class="space-y-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Predmeti</h3>
                <select id="fakultet-filter" 
                        class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 text-sm bg-white">
                    <option value="">Svi fakulteti</option>
                    @if(isset($fakulteti) && $fakulteti->count() > 0)
                        @foreach($fakulteti as $fakultet)
                            <option value="{{ $fakultet->id }}" 
                                    {{ (isset($fitFakultet) && $fitFakultet && $fitFakultet->id == $fakultet->id) ? 'selected' : '' }}>
                                {{ $fakultet->naziv }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            
            <div class="border border-gray-300 rounded-lg p-4 max-h-[600px] overflow-y-auto bg-gray-50">
                @if(isset($predmeti) && $predmeti->count() > 0)
                    <div class="space-y-2" id="predmeti-container">
                        @foreach($predmeti as $predmet)
                            @php
                                $studentPredmet = isset($student) ? $student->predmeti->where('id', $predmet->id)->first() : null;
                                $polozen = old("predmeti.{$predmet->id}.polozen", $studentPredmet ? ($studentPredmet->pivot->polozen ?? false) : false);
                                $ocjena = old("predmeti.{$predmet->id}.ocjena", $studentPredmet ? ($studentPredmet->pivot->ocjena ?? '') : '');
                            @endphp
                            <div class="bg-white border border-gray-200 rounded-lg p-2 hover:bg-gray-50 predmet-item" 
                                 data-fakultet-id="{{ $predmet->fakultet_id ?? '' }}">
                                <div class="grid grid-cols-[auto_1fr_auto_auto_auto] items-center gap-3 text-sm">
                                    <input type="checkbox" 
                                           name="predmeti[{{ $predmet->id }}][polozen]" 
                                           id="predmet_{{ $predmet->id }}"
                                           value="1"
                                           class="predmet-checkbox"
                                           data-predmet-id="{{ $predmet->id }}"
                                           {{ $polozen ? 'checked' : '' }}>
                                    
                                    <span class="font-medium text-gray-800 truncate" title="{{ $predmet->naziv }}">{{ $predmet->naziv }}</span>
                                    
                                    <span class="text-gray-600 text-xs whitespace-nowrap">Sem: {{ $predmet->semestar ?? 'N/A' }}</span>
                                    
                                    <span class="text-gray-600 text-xs whitespace-nowrap">ECTS: {{ $predmet->ects }}</span>
                                    
                                    <input type="number" 
                                           name="predmeti[{{ $predmet->id }}][ocjena]" 
                                           id="ocjena_{{ $predmet->id }}"
                                           min="6" 
                                           max="10"
                                           step="1"
                                           placeholder="Ocjena"
                                           value="{{ $ocjena }}"
                                           class="ocjena-input w-20 border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-blue-500 px-2 py-1 text-sm"
                                           {{ $polozen ? '' : 'disabled' }}>
                                    
                                    <input type="hidden" name="predmeti[{{ $predmet->id }}][predmet_id]" value="{{ $predmet->id }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm text-center py-4">Nema dostupnih predmeta</p>
                @endif
            </div>
        </div>
    </div>

    <div class="flex justify-end space-x-2 mt-6">
        <a href="{{ route('studenti.index') }}" 
           class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-100 text-gray-700">
            Otkaži
        </a>
        <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
            Sačuvaj
        </button>
    </div>
</form>

<script>
    // Enable/disable ocjena input based on checkbox
    document.querySelectorAll('.predmet-checkbox').forEach(checkbox => {
        const predmetId = checkbox.dataset.predmetId;
        const ocjenaInput = document.getElementById(`ocjena_${predmetId}`);
        
        // Set initial state
        if (checkbox.checked) {
            ocjenaInput.disabled = false;
        }
        
        checkbox.addEventListener('change', function() {
            ocjenaInput.disabled = !this.checked;
            if (!this.checked) {
                ocjenaInput.value = '';
            }
        });
    });

    // Filter predmeti by fakultet
    const fakultetFilter = document.getElementById('fakultet-filter');
    const predmetiContainer = document.getElementById('predmeti-container');
    
    if (fakultetFilter && predmetiContainer) {
        // Initial filter on page load (if FIT is selected by default)
        filterPredmeti();
        
        fakultetFilter.addEventListener('change', filterPredmeti);
        
        function filterPredmeti() {
            const selectedFakultetId = fakultetFilter.value;
            const predmetItems = predmetiContainer.querySelectorAll('.predmet-item');
            
            predmetItems.forEach(item => {
                const fakultetId = item.dataset.fakultetId;
                
                if (!selectedFakultetId || fakultetId === selectedFakultetId) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    }
</script>
