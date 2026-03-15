<x-app-layout>

    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-800 p-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block">Došlo je do greške pri unosu:</span>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="py-10 max-w-6xl mx-auto px-6">
        <div class="mb-6">
            <a href="{{ route('students.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                &larr; Nazad na studente
            </a>
        </div>

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900">
                Dokumenta - {{ $student->ime }} {{ $student->prezime }}
            </h1>
        </div>

        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Upload novog dokumenta</h2>

            <form action="{{ route('students.documents.store', $student->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                @csrf

                <div>
                    <label for="naziv" class="block text-gray-700 font-medium mb-1">Naziv dokumenta</label>
                    <input type="text" id="naziv" name="naziv"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="npr. Motivaciono pismo" required>
                </div>

                <div>
                    <label for="file" class="block text-gray-700 font-medium mb-1">Odaberi fajl</label>
                    <input type="file" id="file" name="file"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>

                <div>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-lg transform transition hover:scale-105">
                        Upload
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Lista dokumenata</h2>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    {{ $student->documents->count() }} ukupno
                </span>
            </div>

            @if($student->documents->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Naziv</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datum dodavanja</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Radnje</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($student->documents as $document)
                                <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $document->naziv }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $document->created_at->format('d.m.Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('students.documents.download', $document->id) }}"
                                                class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md transition-colors">
                                                Preuzmi
                                            </a>

                                            <form action="{{ route('students.documents.destroy', $document->id) }}" method="POST"
                                                onsubmit="return confirm('Jeste li sigurni da želite da obrišete dokument?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors">
                                                    Obriši
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6 text-center text-gray-500">
                    Ovaj student još nema uploadovana dokumenta.
                </div>
            @endif
        </div>
    </div>

</x-app-layout>