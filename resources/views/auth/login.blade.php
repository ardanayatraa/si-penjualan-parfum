<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">

        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="username" value="{{ __('Username') }}" />
                <x-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required
                    autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="current-password" />
            </div>

            <div class="mt-4">
                <x-label for="level" value="{{ __('Login sebagai') }}" />
                <select id="level" name="level" required
                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-800 dark:text-white">
                    <option value="admin">Admin</option>
                    <option value="kasir">Kasir</option>
                    <option value="pemilik">Pemilik Usaha</option>
                </select>
            </div>

            <div class="flex items-center justify-end mt-4">

                <x-button class="ms-4">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
