<x-app-layout>
  <div class="py-10 max-w-4xl mx-auto px-6">
    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200 p-6">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Student</h1>
        <a href="{{ route('students.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
          &larr; Back to Student Management
        </a>
      </div>

      <form x-data action="{{ route('students.update', $student->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="mb-4">
            <label for="ime" class="block text-gray-700 font-medium mb-1">First Name</label>
            <input type="text" id="ime" name="ime" value="{{ old('ime', $student->ime) }}"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('ime') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
          </div>

          <div class="mb-4">
            <label for="prezime" class="block text-gray-700 font-medium mb-1">Last Name</label>
            <input type="text" id="prezime" name="prezime" value="{{ old('prezime', $student->prezime) }}"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('prezime') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
          </div>

          <div class="mb-4">
            <label for="br_indexa" class="block text-gray-700 font-medium mb-1">Index Number</label>
            <input type="text" id="br_indexa" name="br_indexa" value="{{ old('br_indexa', $student->br_indexa) }}"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('br_indexa') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
          </div>

          <div class="mb-4">
            <label for="datum_rodjenja" class="block text-gray-700 font-medium mb-1">Date of Birth</label>
            <input type="date" id="datum_rodjenja" name="datum_rodjenja"
              value="{{ old('datum_rodjenja', $student->datum_rodjenja) }}"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('datum_rodjenja') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
          </div>

          <div class="mb-4">
            <label for="telefon" class="block text-gray-700 font-medium mb-1">Phone Number</label>
            <input type="text" id="telefon" name="telefon" value="{{ old('telefon', $student->telefon) }}"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('telefon') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
          </div>

          <div class="mb-4">
            <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $student->email) }}"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
          </div>

          <div class="mb-4">
            <label for="godina_studija" class="block text-gray-700 font-medium mb-1">Year of Study</label>
            <input type="number" id="godina_studija" name="godina_studija"
              value="{{ old('godina_studija', $student->godina_studija) }}"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('godina_studija') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
          </div>

          <div class="mb-4">
            <label for="jmbg" class="block text-gray-700 font-medium mb-1">JMBG</label>
            <input type="text" id="jmbg" name="jmbg" value="{{ old('jmbg', $student->jmbg) }}"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('jmbg') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
          </div>

          <div class="mb-4 md:col-span-2">
            <div class="mb-4">
              <label class="block text-gray-700 font-medium mb-2">Study Level</label>
              <select name="nivo_studija_id" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                @change="window.dispatchEvent(new CustomEvent('study-level-changed', { detail: $event.target.value }))"
                x-init="$nextTick(() => window.dispatchEvent(new CustomEvent('study-level-changed', { detail: $el.value })))">
                <option value="">Select Study Level</option>
                @foreach($nivoStudija as $nivo)
                  <option value="{{ $nivo->id }}" {{ old('nivo_studija_id', $student->nivo_studija_id) == $nivo->id ? 'selected' : '' }}>
                    {{ $nivo->naziv }}
                  </option>
                @endforeach
              </select>
              @error('nivo_studija_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4 md:col-span-2">
              {{-- Store full object for edit to prefill grades. fallback to old input if validation fails --}}
              <x-subject-selector :subjects="$predmeti" :selected="$errors->any() ? old('predmeti') : $student->predmeti" />
              @error('predmeti') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
          </div>
        </div>

        <div class="flex justify-end mt-6">
          <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105">
            Update Student
          </button>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>