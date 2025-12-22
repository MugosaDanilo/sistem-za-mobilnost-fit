<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 leading-tight">
            Edit University
        </h2>
    </x-slot>

    <div class="py-10 max-w-6xl mx-auto px-6">
        <div class="bg-white shadow rounded-lg p-6">
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('univerzitet.update', $univerzitet->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block mb-1 font-medium">University Name</label>
                    <input type="text" name="naziv" value="{{ old('naziv', $univerzitet->naziv) }}" class="border rounded px-3 py-2 w-full" required>
                    @error('naziv')<div class="text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium">Country</label>
                    <select name="drzava" class="border rounded px-3 py-2 w-full" required>
                        <option value="">Select a country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country }}" {{ old('drzava', $univerzitet->drzava) === $country ? 'selected' : '' }}>
                                {{ $country }}
                            </option>
                        @endforeach
                    </select>
                    @error('drzava')<div class="text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium">City</label>
                    <input type="text" name="grad" value="{{ old('grad', $univerzitet->grad) }}" class="border rounded px-3 py-2 w-full" required>
                    @error('grad')<div class="text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email', $univerzitet->email) }}" class="border rounded px-3 py-2 w-full" required>
                    @error('email')<div class="text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded transition">
                    Save Changes
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
