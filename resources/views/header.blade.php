{{-- =========================================================================
	header.blade.php — En-tête présent sur toutes les pages du site
========================================================================= --}}

<div id="header-wrap">

    {{-- Barre supérieure --}}
    <div class="top-content">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">

                <div class="social-links">
                    <ul class="list-unstyled d-flex mb-0">
                        <li><a href="#"><i class="icon icon-facebook"></i></a></li>
                        <li><a href="#"><i class="icon icon-twitter"></i></a></li>
                        <li><a href="#"><i class="icon icon-youtube-play"></i></a></li>
                    </ul>
                </div>

                <div class="d-flex align-items-center gap-3">
                    @auth
                        <a href="/profil" class="top-link"><i class="icon icon-user me-1"></i>{{ Auth::user()->name }}</a>
                        <a href="/logout" class="top-link">Déconnexion</a>
                    @else
                        <a href="/connect" class="top-link"><i class="icon icon-user me-1"></i>Connexion</a>
                        <a href="/subscription" class="top-link">Inscription</a>
                    @endauth
                </div>

            </div>
        </div>
    </div>

    {{-- Navigation principale --}}
    <header id="header">
        <div class="container">
            <nav class="navbar navbar-expand-lg p-0">

                <a class="navbar-brand" href="/">
                    <img src="{{ asset('images/main-logo.png') }}" alt="BookHub" class="header-logo" style="height: 100px; width: auto;">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav ms-auto align-items-center">

                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">Accueil</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('search*') ? 'active' : '' }}" href="/search/all">Catalogue</a>
                        </li>

                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('borrowing*') ? 'active' : '' }}" href="/borrowing">Mes emprunts</a>
                            </li>

                            @if(Auth::user()->role_id === 1)
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {{ request()->is('bo/*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
                                        Back Office
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="/bo/copies">Exemplaires</a></li>
                                        <li><a class="dropdown-item" href="/bo/profils">Usagers</a></li>
                                    </ul>
                                </li>
                            @endif
                        @endauth

                    </ul>

                    <form role="search" method="get" action="/search" class="d-flex align-items-center ms-3 header-search">
                        <input type="search" name="params" placeholder="Rechercher un livre..." class="form-control form-control-sm">
                        <button type="submit" class="btn btn-sm btn-dark ms-1">&#128269;</button>
                    </form>
                </div>

            </nav>
        </div>
    </header>

</div>
