<x-app-layout>
    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-800 p-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Greška!</strong>
            <span class="block">Došlo je do problema sa unosom:</span>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="py-10 max-w-7xl mx-auto px-6">
        <div class="mb-6">
            <a href="{{ route('fakulteti.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                &larr; Nazad na fakultete
            </a>
        </div>

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Predmeti - {{ $fakultet->naziv }}</h1>
            <div class="flex items-center space-x-2">
                <button id="addSubjectBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105">
                    Dodaj predmet
                </button>
                <button id="importSubjectBtn" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transform transition hover:scale-105">
                    Import predmeta
                </button>
            </div>
        </div>

        <div class="mb-4">
            <input type="text" id="searchSubject" placeholder="Pretraži..." class="w-full max-w-md border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2">
        </div>

        <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Lista predmeta</h2>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ count($predmeti) }} ukupno</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Naziv</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ECTS</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semestar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivo studija</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($predmeti as $p)
                            <tr class="subject-row hover:bg-gray-50 transition-colors duration-150 ease-in-out" data-search="{{ strtolower($p->naziv) }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $p->naziv }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $p->ects }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $p->semestar }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $p->nivoStudija->naziv ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors openEditModal"
                                            data-id="{{ $p->id }}" data-naziv="{{ $p->naziv }}" data-ects="{{ $p->ects }}"
                                            data-semestar="{{ $p->semestar }}" data-nivo="{{ $p->nivo_studija_id }}">
                                            Izmeni
                                        </button>
                                        <form action="{{ route('predmeti.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Da li ste sigurni da želite obrisati ovaj predmet?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors">Obriši</button>  
                                        </form>
                                        <button class="text-purple-600 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 px-3 py-1 rounded-md transition-colors openNlModal"
                                            data-id="{{ $p->id }}" data-naziv="{{ $p->naziv }}" data-fakultet="{{ $fakultet->id }}" data-nl="{{ $p->nastavnaLista->link ?? '' }}">
                                            Nastavna lista
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<!-- NL Modal -->
<div id="nlModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-xl font-semibold mb-4">Nastavna lista - <span id="nlSubjectName"></span></h2>
        <form id="nlForm" method="POST" action="{{ route('nastavne-liste.store') }}">
            @csrf
            <input type="hidden" name="predmet_id" id="nlSubjectId">
            <input type="hidden" name="fakultet_id" id="nlFakultetId">
            <div class="mb-4">
                <label for="nlLink" class="block text-gray-700 font-medium mb-1">Link nastavne liste</label>
                <input type="text" id="nlLink" name="nl_link" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="https://..." required>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelNlModal" class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-100">Otkaži</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">Sačuvaj</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const nlModal = document.getElementById('nlModal');
    const nlForm = document.getElementById('nlForm');
    const cancelNl = document.getElementById('cancelNlModal');

    document.querySelectorAll('.openNlModal').forEach(button => {
        button.addEventListener('click', () => {
            nlForm.predmet_id.value = button.dataset.id;
            nlForm.fakultet_id.value = button.dataset.fakultet;
            nlForm.nl_link.value = button.dataset.nl;
            document.getElementById('nlSubjectName').textContent = button.dataset.naziv;
            nlModal.classList.remove('hidden');
            nlModal.classList.add('flex');
        });
    });

    cancelNl.addEventListener('click', () => {
        nlModal.classList.add('hidden');
        nlModal.classList.remove('flex');
    });
});
</script>
</x-app-layout>
