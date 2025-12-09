<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Uredi Izvještaj</h2>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('izvjestaji.update', $id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="naziv" class="block text-gray-700 font-bold mb-2">Naziv Izvještaja</label>
                    <input type="text" name="naziv" id="naziv" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Unesite naziv izvještaja" required>
                </div>

                <div class="mb-4">
                    <label for="opis" class="block text-gray-700 font-bold mb-2">Opis</label>
                    <textarea name="opis" id="opis" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Unesite opis izvještaja"></textarea>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Ažuriraj
                    </button>
                    <a href="{{ route('izvjestaji.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Nazad
                    </a>
                </div>
            </form>
        </div>
    </div>
    </div>
</x-app-layout>
