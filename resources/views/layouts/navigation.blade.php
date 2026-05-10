<header class="site-header">
    <a class="brand" href="{{ url('/') }}">
        <span class="brand-mark">
            <img src="{{ asset('images/eyay-logo-transparent.png') }}" alt="EyAy">
        </span>
        <span>EyAy</span>
    </a>

    <nav aria-label="Primary navigation">
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ url('/businesses') }}">Leveranciers</a>
        <a href="{{ url('/businesses?category=AI-agents') }}">AI-agents</a>
        <a href="{{ url('/businesses?category=Integratiepartners') }}">Integraties</a>
        @guest
        <a class="nav-cta" href="{{ route('business.create') }}">Bedrijf aanmelden</a>
        <a href="{{ route('login') }}">{{ __('Login') }}</a>

        @if (Route::has('register'))
        <a class="nav-button" href="{{ route('register') }}">{{ __('Register') }}</a>
        @endif
        @else
        <a class="nav-cta" href="{{ Auth::user()->business()->exists() ? Auth::user()->business->path() : route('business.create') }}">
            {{ Auth::user()->business()->exists() ? 'Mijn bedrijf' : 'Bedrijf aanmelden' }}
        </a>
        <div class="relative user-menu">
            <button class="flex items-center focus:outline-none focus:text-gray-600" id="nav-dropdown"
                aria-haspopup="true" aria-expanded="true">
                <img src="{{ Auth::user()->displayAvatar() }}" class="rounded-full avatar"
                    alt="{{ Auth::user()->name }}">
                <p class="ml-1 font-normal">{{ Auth::user()->name }}</p>
            </button>

            <div class="origin-top-right absolute right-0 mt-2 w-56 dropdown-panel" id="dropdown-items">
                <div class="bg-white shadow-xs">
                    <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                        <a href="{{ route('profiles.show', Auth::user()->id) }}"
                            class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                            role="menuitem">Profiel</a>
                        <form method="POST" action="{{ route('logout') }}">
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm leading-5
                            text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100
                            focus:text-gray-900" role="menuitem">
                                @csrf
                                Uitloggen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endguest
    </nav>
</header>
