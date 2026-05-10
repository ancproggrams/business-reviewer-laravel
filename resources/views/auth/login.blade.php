@extends('layouts.app')

@section('content')


<form method="POST" action="{{ route('login') }}" class="form">
    <img src="{{ asset('/images/eyay-logo-transparent.png') }}" class="mx-auto mb-8 auth-logo" alt="EyAy">
    <h1 class="text-2xl font-bold mb-2">Inloggen</h1>
    <p class="text-gray-600 mb-6">Log in om je leveranciersprofiel te beheren of reviews te plaatsen.</p>
    @csrf

    <div class="mb-3">
        <label for="email" class="font-medium">E-mailadres</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
    </div>

    <div class="mb-3">
        <label for="password" class="font-medium col-md-4 col-form-label text-md-right">Wachtwoord</label>
        <input id="password" type="password" name="password" required autocomplete="current-password">
    </div>

    <div class="flex justify-between mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                {{ old('remember') ? 'checked' : '' }}>

            <label class="form-check-label" for="remember">
                Onthoud mij
            </label>
        </div>
        @if (Route::has('password.request'))
        <a class="hover:underline" href="{{ route('password.request') }}">
            Wachtwoord vergeten?
        </a>
        @endif
    </div>

    @error('email')
    <div class="text-sm text-red-400 mb-2" role="alert">
        <span>{{ $message }}</span>
    </div>
    @enderror

    <button type="submit" class="nav-button mt-4">
        Inloggen
    </button>
</form>

@endsection
