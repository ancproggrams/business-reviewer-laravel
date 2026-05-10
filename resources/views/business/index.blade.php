@extends('layouts.app')
@section('content')

<section class="lg:flex index items-start">

    <aside class="index__aside mr-5 w-full md:w-1/4 hidden md:block">
        <h5 class="text-xl font-bold mb-2">AI-oplossingen</h5>
        <ul>
            <li><a href="/businesses?category=Klantenservice AI">Klantenservice AI</a></li>
            <li><a href="/businesses?category=AI-agents">AI-agents</a></li>
            <li><a href="/businesses?category=Documentverwerking">Documentverwerking</a></li>
            <li><a href="/businesses?category=Integratiepartners">Integratiepartners</a></li>
        </ul>
    </aside>
    <main class="index__main flex-1 flex flex-col">
        <a href='/businesses/create' class="add__button">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="inline mr-2">
                <g fill="#ffff">
                    <path
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z">
                    </path>
                </g>
            </svg>Bedrijf aanmelden</a>
            <hr>
        <businesses></businesses>
    </main>
</section>
@endsection
