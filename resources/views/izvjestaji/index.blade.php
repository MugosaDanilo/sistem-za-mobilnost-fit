@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Izvještaji</h1>
    </div>

    @if ($message = Session::get('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ $message }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Izvještaj o Studentima -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="text-center">
                <div class="text-4xl font-bold text-blue-500 mb-3">👥</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Studenti</h3>
                <p class="text-gray-600 mb-4">Pregled svih registrovanih studenata</p>
                <a href="{{ route('izvjestaji.show', 'studenti') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-block">
                    Otvori Izvještaj
                </a>
            </div>
        </div>

        <!-- Izvještaj o Mobilnostima -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="text-center">
                <div class="text-4xl font-bold text-green-500 mb-3">✈️</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Mobilnosti</h3>
                <p class="text-gray-600 mb-4">Pregled svih mobilnosti studenata</p>
                <a href="{{ route('izvjestaji.show', 'mobilnosti') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-block">
                    Otvori Izvještaj
                </a>
            </div>
        </div>

        <!-- Izvještaj o Fakultetima -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="text-center">
                <div class="text-4xl font-bold text-purple-500 mb-3">🏛️</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Fakulteti</h3>
                <p class="text-gray-600 mb-4">Pregled svih registrovanih fakulteta</p>
                <a href="{{ route('izvjestaji.show', 'fakulteti') }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded inline-block">
                    Otvori Izvještaj
                </a>
            </div>
        </div>

        <!-- Izvještaj o Univerzitetima -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="text-center">
                <div class="text-4xl font-bold text-orange-500 mb-3">🎓</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Univerziteti</h3>
                <p class="text-gray-600 mb-4">Pregled svih registrovanih univerziteta</p>
                <a href="{{ route('izvjestaji.show', 'univerziteti') }}" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded inline-block">
                    Otvori Izvještaj
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
