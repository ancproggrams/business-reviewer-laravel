@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('register') }}" class="form">
    <img src="{{ asset('/images/eyay-logo-transparent.png') }}" class="mx-auto mb-8 auth-logo" alt="EyAy">
    <h1 class="text-2xl font-bold mb-2">Account aanmaken</h1>
    <p class="text-gray-600 mb-6">Registreer als AI-leverancier of reviewer om mee te doen aan het EyAy trust platform.</p>
    @csrf


    <div class="flex mb-3">
        <div class="mr-2">
            <label for="name" class="font-medium">Voornaam</label>
            <input id="name" type="text" name="name" class="@error('name') border-red-400 @enderror"
                value="{{ old('name') }}" required autofocus>
        </div>

        <div class="ml-2">
            <label for="name" class="font-medium">Achternaam</label>
            <input id="surname" type="text" name="surname" class="@error('surname') border-red-400 @enderror"
                value="{{ old('surname') }}" required autofocus>
        </div>
        @error('name', 'surname')
        <div class="text-sm text-red-400 mb8" role="alert">
            <span>{{ $message }}</span>
        </div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="font-medium">E-mailadres</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" value="{{ old('email') }}" required>

        @error('email')
        <div class="text-sm text-red-400 mb8" role="alert">
            <span>{{ $message }}</span>
        </div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="country" class="font-medium">Land</label>
        @include('helpers._country-dropdown')

        @error('country')
        <div class="text-sm text-red-400 mb8" role="alert">
            <span>{{ $message }}</span>
        </div>
        @enderror
    </div>


    <div class="mb-3">
        <label for="city" class="font-medium">Stad</label>
        <input id="city" type="text" name="city" value="{{ old('city') }}" required>

        @error('city')
        <div class="text-sm text-red-400 mb8" role="alert">
            <span>{{ $message }}</span>
        </div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="font-medium">Wachtwoord</label>
        <input id="password" type="password" required name="password">
        @error('password')
        <div class="text-sm text-red-400 mb8" role="alert">
            <span>{{ $message }}</span>
        </div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password-confirm" class="font-medium">Bevestig wachtwoord</label>
        <input id="password-confirm" type="password" name="password_confirmation" required>
    </div>

    <div class="mb-3">
        <div class="block">
            <input type="radio" name="type" value="business" autofocus required>
            <label for="type">Ik ben AI-leverancier</label>
        </div>
        <div class="block">
            <input type="radio" name="type" value="reviewer" autofocus required>
            <label for="type">Ik wil leveranciers beoordelen</label>
        </div>
    </div>


    <button type="submit" class="nav-button mt-4">
        Account aanmaken
    </button>

</form>

@endsection
