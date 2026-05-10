<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>EyAy — AI-leveranciers beoordelen</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"
        integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog=="
        crossorigin="anonymous" />

    @yield('head')
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <script>
        const csrfToken = @JSON(csrf_token());
        const currentUser = @JSON(Auth::user());

    </script>
</head>

<body>
    <div id="app">
        <modal></modal>
        @include('layouts.navigation')
        <main>
            <section class="eyay-hero">
                <div>
                    <p class="eyay-eyebrow"><span></span> Onafhankelijk trust platform</p>
                    <h1>Vind en beoordeel AI-leveranciers die echt leveren.</h1>
                    <p class="eyay-lead">EyAy helpt organisaties AI-partners vergelijken op bewijs, governance, implementatiekwaliteit en klantreviews. Geen vendor showcase, maar aantoonbare werking.</p>
                    <form class="eyay-actions" method="GET" action="/businesses">
                        <input name="search" type="text" placeholder="Zoek leverancier, AI-oplossing of branche">
                        <button class="eyay-button primary" type="submit">Zoeken</button>
                        <a class="eyay-button" href="{{ route('business.create') }}">Bedrijf aanmelden</a>
                    </form>
                </div>
                <aside class="eyay-panel">
                    <img src="{{ asset('images/people-collaboration.jpg') }}" alt="AI-leveranciers review overleg">
                    <div class="eyay-score">
                        <strong>92%</strong>
                        <p>Matchscore op basis van categorie, bewijs, reviews en implementatieprofiel.</p>
                    </div>
                </aside>
            </section>
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>

</html>
