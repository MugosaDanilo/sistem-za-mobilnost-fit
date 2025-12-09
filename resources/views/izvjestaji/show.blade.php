<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $data['naslov'] ?? 'Izvještaj' }}</h2>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('izvjestaji.index') }}" class="text-blue-500 hover:text-blue-700 mb-4 inline-block">
            ← Nazad na Izvještaje
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Statistika -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded">
            <p class="text-lg text-gray-700">
                <span class="font-semibold">Ukupno zapisa:</span> 
                <span class="text-2xl font-bold text-blue-600">{{ $data['ukupno'] ?? 0 }}</span>
            </p>
        </div>

        <!-- Tabela sa podacima -->
        @if($data['podaci'] && count($data['podaci']) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach($data['kolone'] as $kolona)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $kolona }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data['podaci'] as $red)
                            <tr class="hover:bg-gray-50">
                                @foreach($red as $vrednost)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $vrednost }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Akcije -->
            <div class="mt-6 flex gap-4">
                <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    🖨️ Štampaj
                </button>
                <a href="{{ route('izvjestaji.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Nazad
                </a>
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 text-lg">Nema dostupnih podataka za ovaj izvještaj</p>
                <a href="{{ route('izvjestaji.index') }}" class="mt-4 inline-block bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Nazad
                </a>
            </div>
        @endif
    </div>
</div>

<style>
    @media print {
        .no-print {
            display: none;
        }
        body {
            background-color: white;
        }
    }
</style>
</x-app-layout>
