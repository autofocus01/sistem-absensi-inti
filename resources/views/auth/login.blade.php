<x-guest-layout>
    <div class="inti-auth-heading">
        <span class="inti-auth-eyebrow">Portal internal</span>
        <h1>Masuk ke Sistem</h1>
        <p>Gunakan akun perusahaan Anda untuk melanjutkan.</p>
    </div>

    @if (session('status'))
        <div class="inti-auth-alert inti-auth-alert-success" role="status">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="inti-auth-form">
        @csrf

        <div class="inti-auth-field">
            <label for="email">Email</label>
            <input id="email"
                   class="inti-auth-input @error('email') is-invalid @enderror"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autofocus
                   autocomplete="username"
                   inputmode="email">
            @error('email')
                <p class="inti-auth-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="inti-auth-field">
            <label for="password">Password</label>
            <input id="password"
                   class="inti-auth-input @error('password') is-invalid @enderror"
                   type="password"
                   name="password"
                   required
                   autocomplete="current-password">
            @error('password')
                <p class="inti-auth-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="inti-auth-options">
            <label for="remember_me" class="inti-auth-checkbox">
                <input id="remember_me" type="checkbox" name="remember">
                <span>Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="inti-auth-link" href="{{ route('password.request') }}">
                    Lupa Password?
                </a>
            @endif
        </div>

        <button type="submit" class="inti-auth-submit">
            Masuk
        </button>
    </form>
</x-guest-layout>
