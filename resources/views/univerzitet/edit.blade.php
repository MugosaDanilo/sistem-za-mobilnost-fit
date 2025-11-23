<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Uredi Univerzitet') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Forma za uređivanje univerziteta --}}
                <form action="{{ route('univerzitet.update', $univerzitet->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Naziv univerziteta --}}
                    <div class="mb-4">
                        <label class="block mb-1">Naziv univerziteta</label>
                        <input type="text" name="naziv" value="{{ old('naziv', $univerzitet->naziv) }}" class="border rounded px-3 py-2 w-full" required>
                        @error('naziv')
                            <div class="text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Država --}}
                    <div class="mb-4">
                        <label class="block mb-1">Država</label>
                        <input type="text" name="drzava" value="{{ old('drzava', $univerzitet->drzava) }}" class="border rounded px-3 py-2 w-full" required>
                        @error('drzava')
                            <div class="text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Grad --}}
                    <div class="mb-4">
                        <label class="block mb-1">Grad</label>
                        <input type="text" name="grad" value="{{ old('grad', $univerzitet->grad) }}" class="border rounded px-3 py-2 w-full" required>
                        @error('grad')
                            <div class="text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <label class="block mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $univerzitet->email) }}" class="border rounded px-3 py-2 w-full" required>
                        @error('email')
                            <div class="text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Dugme za čuvanje --}}
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                        Sačuvaj promjene
                    </button>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
