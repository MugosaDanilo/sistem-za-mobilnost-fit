<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Odaberite Izvještaj</h2>
    </x-slot>

    <div class="container mx-auto px-4 py-6">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Izvještaj o Studentima -->
        <a href="{{ route('izvjestaji.show', 'studenti') }}" class="block">
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition cursor-pointer h-full">
                <div class="text-center">
                    <div class="text-4xl font-bold text-blue-500 mb-3"></div>
                    <h3 class="text-xl font-semibold text-gray-800">Studenti</h3>
                    <p class="text-gray-600 mt-2">Pregled svih registrovanih studenata</p>
                </div>
            </div>
        </a>

        <!-- Izvještaj o Mobilnostima -->
        <a href="{{ route('izvjestaji.show', 'mobilnosti') }}" class="block">
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition cursor-pointer h-full">
                <div class="text-center">
                    <div class="text-4xl font-bold text-green-500 mb-3"></div>
                    <h3 class="text-xl font-semibold text-gray-800">Mobilnosti</h3>
                    <p class="text-gray-600 mt-2">Pregled svih mobilnosti studenata</p>
                </div>
            </div>
        </a>

        <!-- Izvještaj o Fakultetima -->
        <a href="{{ route('izvjestaji.show', 'fakulteti') }}" class="block">
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition cursor-pointer h-full">
                <div class="text-center">
                    <div class="text-4xl font-bold text-purple-500 mb-3"></div>
                    <h3 class="text-xl font-semibold text-gray-800">Fakulteti</h3>
                    <p class="text-gray-600 mt-2">Pregled svih registrovanih fakulteta</p>
                </div>
            </div>
        </a>

        <!-- Izvještaj o Univerzitetima -->
        <a href="{{ route('izvjestaji.show', 'univerziteti') }}" class="block">
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition cursor-pointer h-full">
                <div class="text-center">
                    <div class="text-4xl font-bold text-orange-500 mb-3"></div>
                    <h3 class="text-xl font-semibold text-gray-800">Univerziteti</h3>
                    <p class="text-gray-600 mt-2">Pregleeeed svih registrovanih univerziteta</p>
                </div>
            </div>
        </a>
    </div>

    <div class="mt-6">
        <a href="{{ route('izvjestaji.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Nazad
        </a>
    </div>
    </div>
</x-app-layout>
