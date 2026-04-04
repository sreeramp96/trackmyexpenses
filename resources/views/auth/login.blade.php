<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-ink tracking-tight">Welcome back</h2>
        <p class="text-sm text-ink-3 mt-1">Please enter your details to sign in.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <x-input-label for="password" :value="__('Password')" class="mb-0" />
                @if (Route::has('password.request'))
                    <a class="text-[10px] font-bold uppercase tracking-wider text-ink-3 hover:text-ink transition-colors" href="{{ route('password.request') }}">
                        {{ __('Forgot?') }}
                    </a>
                @endif
            </div>
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-edge-2 text-ink focus:ring-0 bg-surface-2 transition-all" name="remember">
            <label for="remember_me" class="ms-2 text-[11px] font-medium text-ink-2 uppercase tracking-wide cursor-pointer">{{ __('Keep me signed in') }}</label>
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center">
                {{ __('Sign In') }}
            </x-primary-button>
        </div>

        <div class="text-center pt-4 border-t border-edge/50">
            <p class="text-xs text-ink-3">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-bold text-ink hover:underline">Create one</a>
            </p>
        </div>
    </form>
</x-guest-layout>
