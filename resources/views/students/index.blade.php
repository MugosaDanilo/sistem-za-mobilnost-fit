<x-app-layout>

  @if(session('success'))
    <div class="mb-4 bg-green-100 text-green-800 p-3 rounded-md">
      {{ session('success') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
      <strong class="font-bold">Whoops!</strong>
      <span class="block">There were some problems with your input:</span>
      <ul class="mt-2 list-disc list-inside">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="py-10 max-w-7xl mx-auto px-6">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Studenti</h1>
      <a href="{{ route('students.create') }}"
        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105">
        Dodaj studenta
      </a>
    </div>

    <div class="mb-4">
      <input type="text" id="searchStudent" placeholder="Pretrazi.."
        class="w-full max-w-md border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2">
    </div>

    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
      <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Lista Studenata</h2>
        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ count($students) }}
          Total</span>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="studentTable">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ime i Prezime
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Indeks</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Godina</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200" id="studentTableBody">
            @foreach ($students as $student)
              <tr class="student-row hover:bg-gray-50 transition-colors duration-150 ease-in-out"
                data-search="{{ strtolower($student->ime . ' ' . $student->prezime . ' ' . $student->br_indexa . ' ' . $student->email) }}">

                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div
                      class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                      {{ substr($student->ime, 0, 1) }}{{ substr($student->prezime, 0, 1) }}
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ $student->ime }} {{ $student->prezime }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->br_indexa }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->email }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->godina_studija }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->nivoStudija->naziv ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <a href="{{ route('students.edit', $student->id) }}"
                      class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors">
                      Edit
                    </a>

                    <form action="{{ route('students.destroy', $student->id) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to delete this student?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                        class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors">
                        Delete
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

      </div>
    </div>
  </div>

</x-app-layout>