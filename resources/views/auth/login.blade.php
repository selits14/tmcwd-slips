<x-guest-layout>
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email address')" class="font-medium text-slate-700" />
            <x-text-input id="email" class="mt-2 block w-full rounded-lg border-slate-300 px-4 py-3 text-sm shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@tmcwd.gov.ph" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="font-medium text-slate-700" />
            <x-text-input id="password" class="mt-2 block w-full rounded-lg border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-3 pt-1">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="rounded text-sm font-medium text-sky-700 underline-offset-4 hover:text-sky-900 hover:underline focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="mt-2 w-full justify-center rounded-lg border-0 bg-sky-600 px-4 py-3 text-sm font-semibold tracking-wide text-white shadow-sm hover:bg-sky-700 focus:bg-sky-700 focus:ring-sky-500 active:bg-sky-800">
            {{ __('Sign in') }}
        </x-primary-button>
    </form>
</x-guest-layout>
