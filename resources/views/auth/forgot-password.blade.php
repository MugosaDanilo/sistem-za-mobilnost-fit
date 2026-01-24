<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Zaturili ste šifru, no problem, probaj sa datumom rođenja, ili paščetom ili drugim kućnim ljubimcem, ako nije to onda je 12345, ako nije ni to ukucaj email i poslaće ti se da resetuješ šifru') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email za resetovanje šifre') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
