@extends('layouts.app')

@section('content')

<div class="flex justify-center">

    <form action="/businesses" method="POST" class="form" enctype="multipart/form-data">
        @csrf

        <h1 class="text-2xl font-bold mb-3">Bedrijf aanmelden</h1>
        <p class="text-gray-600 mb-6">Maak een publiek leveranciersprofiel zodat organisaties je AI-oplossingen kunnen vinden, vergelijken en beoordelen.</p>

        <div class="py-3 flex flex-col">
            <label for="country">Land</label>
            @include('helpers._country-dropdown')
            @error('country')
            <p class="text-red-400 text-xs">{{ $message }}</p>
            @enderror
        </div>

        <div class="py-3 flex flex-col">
            <label for="name">Bedrijfsnaam</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
            @error('name')
            <p class="text-red-400 text-xs">{{ $message }}</p>
            @enderror
        </div>

        <div class="py-3 flex flex-col">
            <label for="address">Adres</label>
            <input type="text" name="address" value="{{ old('address') }}" required>
            @error('address')
            <p class="text-red-400 text-xs">{{ $message }}</p>
            @enderror
        </div>

        <div class="py-3 flex flex-col">
            <label for="city">Stad</label>
            <input type="text" name="city" value="{{ old('city') }}" required>
            @error('city')
            <p class="text-red-400 text-xs">{{ $message }}</p>
            @enderror
        </div>

        <div class="py-3 flex flex-col">
            <label for="phone_number">Telefoonnummer</label>
            <input type="text" name="phone_number" value="{{ old('phone_number') }}" required>
            @error('phone_number')
            <p class="text-red-400 text-xs">{{ $message }}</p>
            @enderror
        </div>




        <div class="py-3 flex flex-col">
            <label for="location">Locatie</label>
            <input type="hidden" name="geo_location" value="" id="geo-location">
            @section('head')
            <script src="https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.js"></script>
            <link type="text/css" rel="stylesheet" href="https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.css" />

            <script type="text/javascript">
                navigator.geolocation.getCurrentPosition(pos => console.log(pos));


                window.onload = function () {
                    L.mapquest.key = 'lYrP4vF3Uk5zgTiGGuEzQGwGIVDGuy24';

                    var map = L.mapquest.map('map', {
                        center: [37.7749, -122.4194],
                        layers: L.mapquest.tileLayer('map'),
                        zoom: 1
                    });

                    let marker;

                    map.on('click', (e) => {
                        if (marker) {
                            marker.remove();
                        }

                        const markerCoords = [e.latlng.lat, e.latlng.lng];

                        marker = L.marker(markerCoords, {
                            draggable: true
                        }).addTo(map);

                        document.querySelector('#geo-location').value = JSON.stringify(markerCoords);
                    });

                }

            </script>
            @endsection
            <div id="map" style="width: 100%; height: 250px;" class="mb-5"></div>
        </div>


        <div class="py-3 flex flex-col">
            <label for="email">Zakelijk e-mailadres</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
            @error('email')
            <p class="text-red-400 text-xs">{{ $message }}</p>
            @enderror
        </div>


        <div class="py-3 flex flex-col">
            <label for="website_url">Website</label>
            <input type="url" name="website_url" value="{{ old('website_url') }}" placeholder="https://example.com" required>
            @error('website_url')
            <p class="text-red-400 text-xs">{{ $message }}</p>
            @enderror
        </div>

        <div class="py-3 flex flex-col">
            <label for="categories">AI-oplossingen</label>
            <select name="categories[]" multiple required>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('categories')
            <p class="text-red-400 text-xs">{{ $message }}</p>
            @enderror
            @error('categories.*')
            <p class="text-red-400 text-xs">{{ $message }}</p>
            @enderror
        </div>

        <div class="py-3 flex flex-col">
            <label for="description">Beschrijving</label>
            <textarea type="text" name="description" cols="30" rows="10">{{ old('description') }}</textarea>
            @error('description')
            <p class="text-red-400 text-xs">{{ $message }}</p>
            @enderror
        </div>

        <div class="py-3 flex flex-col">
            <label for="front_image">Afbeelding</label>
            <input type="file" name="front_image" class="py-2  rounded" accept="image/*" required>
            @error('front_image')
            <p class="text-red-400 text-xs">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="py-2 px-5 bg-red-500 text-white rounded block w-full">Leveranciersprofiel aanmaken</button>
        <small class="mt-2 block">Door te klikken ga je akkoord met de voorwaarden van EyAy.</small>
    </form>

</div>

@endsection
