<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dodaj Univerzitet') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('univerzitet.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block mb-1">Naziv univerziteta</label>
                        <input type="text" name="naziv" class="border rounded px-3 py-2 w-full" required>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Država</label>
                        <input type="text" name="drzava" class="border rounded px-3 py-2 w-full" required>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Grad</label>
                        <input type="text" name="grad" class="border rounded px-3 py-2 w-full" required>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Email</label>
                        <input type="email" name="email" class="border rounded px-3 py-2 w-full" required>
                    </div>
                    @error('email')
    <div class="text-red-600 mt-1">{{ $message }}</div>
@enderror

                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
    Sačuvaj
</button>

 </form>

            </div>
        </div>
    </div>
</x-app-layout>