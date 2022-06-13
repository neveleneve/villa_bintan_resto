<nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-default shadow-sm" id="navbar-user">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <img src="{{ asset('argon/assets/img/brand/favicon.png') }}" height="30" alt="">
            Villa Bintan Resto
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto ">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ Request::is('/') ? 'active' : 'font-weight-bold' }}">
                        Home
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('menu') }}" class="nav-link {{ Request::is('menu') ? 'active' : null }} font-weight-bold">
                        Menu
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a href="{{ route('about') }}" class="nav-link {{ Request::is('about') ? 'active' : null }} font-weight-bold">
                        About Us
                    </a>
                </li> --}}
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link {{ Request::is('login') || Request::is('dashboard') || Request::is('reservations') ? 'active' : null }} font-weight-bold">
                        Administrator
                    </a>
                </li>
                @auth
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="post">
                            {{ csrf_field() }}
                            <button type="submit" class="nav-link font-weight-bold btn btn-secondary btn-block text-default" onclick="return confirm('Are you sure want to log out?')">
                                Log Out
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{ route('reservation') }}" class="nav-link font-weight-bold btn btn-secondary btn-block text-default">
                            Table Reservation
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
