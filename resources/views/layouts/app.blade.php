<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BeMo Kanban Board') }}</title>

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <nav class="navigation">
        <div class="navigation__container">
            <svg class="navigation__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                 fill="none" stroke="#FFF"
                 stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
            </svg>

            <a href="{{ url('/') }}" class="navigation__title">
                BeMo <span class="navigation__title--no-mobile">Kanban Board</span>
            </a>

            <div class="navigation__buttons">
                @guest
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="navigation__button">
                            Login
                        </a>
                    @endif

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="navigation__button">
                            Register
                        </a>
                    @endif
                @else
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf

                        <button type="submit" class="navigation__button">
                            Logout
                        </button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    @yield('content')
</div>
</body>
</html>
