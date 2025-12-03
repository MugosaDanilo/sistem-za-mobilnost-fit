@php($editing = isset($student))

<form method="POST" action="{{ $editing ? route('studenti.update', $student->id) : route('studenti.store') }}">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="ime" class="block text-gray-700 font-medium mb-1">Ime <span class="text-red-500">*</span></label>
            <input type="text" 
                   id="ime" 
                   name="ime" 
                   value="{{ old('ime', $student->ime ?? '') }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2"
                   required>
            @error('ime')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="prezime" class="block text-gray-700 font-medium mb-1">Prezime <span class="text-red-500">*</span></label>
            <input type="text" 
                   id="prezime" 
                   name="prezime" 
                   value="{{ old('prezime', $student->prezime ?? '') }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2"
                   required>
            @error('prezime')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="br_indexa" class="block text-gray-700 font-medium mb-1">Broj indeksa <span class="text-red-500">*</span></label>
            <input type="text" 
                   id="br_indexa" 
                   name="br_indexa" 
                   value="{{ old('br_indexa', $student->br_indexa ?? '') }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2"
                   required>
            @error('br_indexa')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-gray-700 font-medium mb-1">Email <span class="text-red-500">*</span></label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="{{ old('email', $student->email ?? '') }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2"
                   required>
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="telefon" class="block text-gray-700 font-medium mb-1">Telefon <span class="text-red-500">*</span></label>
            <input type="text" 
                   id="telefon" 
                   name="telefon" 
                   value="{{ old('telefon', $student->telefon ?? '') }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2"
                   required>
            @error('telefon')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="jmbg" class="block text-gray-700 font-medium mb-1">JMBG <span class="text-red-500">*</span></label>
            <input type="text" 
                   id="jmbg" 
                   name="jmbg" 
                   value="{{ old('jmbg', $student->jmbg ?? '') }}"
                   maxlength="13"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2"
                   required>
            @error('jmbg')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="datum_rodjenja" class="block text-gray-700 font-medium mb-1">Datum rođenja <span class="text-red-500">*</span></label>
            <input type="date" 
                   id="datum_rodjenja" 
                   name="datum_rodjenja"
                   value="{{ old('datum_rodjenja', isset($student) && $student->datum_rodjenja ? $student->datum_rodjenja->format('Y-m-d') : '') }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2"
                   required>
            @error('datum_rodjenja')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="godina_studija" class="block text-gray-700 font-medium mb-1">Godina studija <span class="text-red-500">*</span></label>
            <input type="number" 
                   id="godina_studija" 
                   name="godina_studija" 
                   min="1" 
                   max="8"
                   value="{{ old('godina_studija', $student->godina_studija ?? '') }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2"
                   required>
            @error('godina_studija')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="nivo_studija_id" class="block text-gray-700 font-medium mb-1">Nivo studija <span class="text-red-500">*</span></label>
            <select id="nivo_studija_id" 
                    name="nivo_studija_id"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 bg-white"
                    required
                    style="appearance: auto; -webkit-appearance: menulist; -moz-appearance: menulist;">
                <option value="">Izaberi nivo studija</option>
                @if(isset($nivoiStudija) && $nivoiStudija->count() > 0)
                    @foreach($nivoiStudija as $nivo)
                        <option value="{{ $nivo->id }}" 
                                {{ old('nivo_studija_id', isset($student) ? $student->nivo_studija_id : '') == $nivo->id ? 'selected' : '' }}>
                            {{ $nivo->naziv }}
                        </option>
                    @endforeach
                @else
                    {{-- Fallback opcije ako nema podataka u bazi --}}
                    <option value="1">Osnovne</option>
                    <option value="2">Master</option>
                @endif
            </select>
            @error('nivo_studija_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
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
