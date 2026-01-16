<x-app-layout>
  <div class="py-10 max-w-4xl mx-auto px-6">
    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200 p-6">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Add Student</h1>
        <a href="{{ route('students.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
          &larr; Back to Student Management
        </a>
      </div>

      <form action="{{ route('students.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="mb-4">
            <label for="ime" class="block text-gray-700 font-medium mb-1">First Name</label>
            <input type="text" id="ime" name="ime" value="{{ old('ime') }}"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('ime') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
          </div>

          <div class="mb-4">
            <label for="prezime" class="block text-gray-700 font-medium mb-1">Last Name</label>
            <input type="text" id="prezime" name="prezime" value="{{ old('prezime') }}"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('prezime') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
          </div>

          <div class="mb-4">
            <label for="br_indexa" class="block text-gray-700 font-medium mb-1">Index Number</label>
            <input type="text" id="br_indexa" name="br_indexa" value="{{ old('br_indexa') }}"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('br_indexa') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
          </div>

          <div class="mb-4">
            <label for="datum_rodjenja" class="block text-gray-700 font-medium mb-1">Date of Birth</label>
            <input type="date" id="datum_rodjenja" name="datum_rodjenja" value="{{ old('datum_rodjenja') }}"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('datum_rodjenja') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
          </div>

          <div class="mb-4">
            <label for="telefon" class="block text-gray-700 font-medium mb-1">Phone Number</label>
            <input type="text" id="telefon" name="telefon" value="{{ old('telefon') }}"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('telefon') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
          </div>

          <div class="mb-4">
            <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
          </div>

          <div class="mb-4">
            <label for="godina_studija" class="block text-gray-700 font-medium mb-1">Year of Study</label>
            <input type="number" id="godina_studija" name="godina_studija" value="{{ old('godina_studija') }}"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('godina_studija') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
          </div>

          <div class="mb-4">
            <label for="jmbg" class="block text-gray-700 font-medium mb-1">JMBG</label>
            <input type="text" id="jmbg" name="jmbg" value="{{ old('jmbg') }}"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('jmbg') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
          </div>


          <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Pol</label>
              <label class="inline-flex items-center">
                <input type="radio" name="pol" id="pol_muski" value="musko" checked
                  class="form-radio text-blue-600" />
                <span class="ml-2 text-gray-700">Muški</span>
              </label>

              <label class="inline-flex items-center">
                <input type="radio" name="pol" id="pol_zenski" value="zensko"
                  class="form-radio text-blue-600" />
                <span class="ml-2 text-gray-700">Ženski</span>
              </label>
          </div>




          <div class="mb-4 md:col-span-2" x-data="{
              selectedFaculty: '{{ old('fakultet_id') }}',
              subjects: [],
              async fetchSubjects() {
                  if (!this.selectedFaculty) {
                      this.subjects = [];
                      return;
                  }
                  try {
                      let url = '{{ route('admin.mobility.faculty-subjects') }}' + '?fakultet_id=' + this.selectedFaculty;
                      let response = await fetch(url);
                      this.subjects = await response.json();
                      window.dispatchEvent(new CustomEvent('subjects-updated', { detail: this.subjects }));
                  } catch (e) {
                      console.error('Error fetching subjects:', e);
                  }
              },
              init() {
                  if (this.selectedFaculty) {
                      this.fetchSubjects();
                  }
              }
          }">
            <div class="mb-4">
              <label class="block text-gray-700 font-medium mb-2">Faculty</label>
              <select name="fakultet_id" required x-model="selectedFaculty" @change="fetchSubjects()"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select Faculty</option>
                @foreach($fakulteti as $f)
                  <option value="{{ $f->id }}" {{ old('fakultet_id') == $f->id ? 'selected' : '' }}>
                    {{ $f->naziv }}
                  </option>
                @endforeach
              </select>
              @error('fakultet_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 font-medium mb-2">Study Level</label>
              <select name="nivo_studija_id" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                onchange="window.dispatchEvent(new CustomEvent('study-level-changed', { detail: this.value }))">
                <option value="">Select Study Level</option>
                @foreach($nivoStudija as $nivo)
                  <option value="{{ $nivo->id }}" {{ old('nivo_studija_id') == $nivo->id ? 'selected' : '' }}>
                    {{ $nivo->naziv }}
                  </option>
                @endforeach
              </select>
              @error('nivo_studija_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <x-subject-selector :subjects="$predmeti" />
            @error('predmeti') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="flex justify-end mt-6">
          <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105">
            Create Student
          </button>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>