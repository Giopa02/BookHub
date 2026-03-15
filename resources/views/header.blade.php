{{-- =========================================================================
	header.blade.php — En-tête présent sur toutes les pages du site
	=========================================================================
	Ce fichier est inclus dans template.blade.php via @include('header').
	Il contient deux parties :
	1. La barre supérieure (réseaux sociaux, connexion/déconnexion, recherche)
	2. La barre de navigation principale (logo + menu)

	Le contenu du menu s'adapte selon si l'utilisateur est connecté ou non,
	et selon son rôle (usager ou bibliothécaire).
--}}

<div id="header-wrap">

    {{-- =====================================================================
		BARRE SUPÉRIEURE (top bar)
    ====================================================================== --}}
	<div class="top-content">
		<div class="container-fluid">
			<div class="row">

                {{-- Colonne gauche : icônes des réseaux sociaux --}}
				<div class="col-md-6">
					<div class="social-links">
						<ul>
							<li><a href="#"><i class="icon icon-facebook"></i></a></li>
							<li><a href="#"><i class="icon icon-twitter"></i></a></li>
							<li><a href="#"><i class="icon icon-youtube-play"></i></a></li>
							<li><a href="#"><i class="icon icon-behance-square"></i></a></li>
						</ul>
					</div>
				</div>

                {{-- Colonne droite : connexion/déconnexion + barre de recherche --}}
				<div class="col-md-6">
					<div class="right-element">

                        {{-- @auth / @else / @endauth : bloc conditionnel de Blade
							@auth    = si l'utilisateur EST connecté
							@else    = si l'utilisateur N'EST PAS connecté
							@endauth = fin du bloc conditionnel --}}
						@auth
                            {{-- L'utilisateur est connecté : on affiche son nom et un lien de déconnexion --}}
							<a href="/profil" class="user-account for-buy">
								<i class="icon icon-user"></i>
                                {{-- Auth::user()->name affiche le nom de l'utilisateur connecté --}}
								<span>{{ Auth::user()->name }}</span>
							</a>
							<a href="/logout" class="for-buy">
								<span>Déconnexion</span>
							</a>
						@else
                            {{-- L'utilisateur n'est pas connecté : on affiche les liens de connexion/inscription --}}
							<a href="/connect" class="user-account for-buy">
								<i class="icon icon-user"></i>
								<span>Connexion</span>
							</a>
							<a href="/subscription" class="for-buy">
								<span>Inscription</span>
							</a>
						@endauth

                        {{-- Barre de recherche : formulaire qui envoie vers /search avec le paramètre "params" --}}
						<div class="action-menu">
							<div class="search-bar">
								<a href="#" class="search-button search-toggle" data-selector="#header-wrap">
									<i class="icon icon-search"></i>
								</a>
								<form role="search" method="get" class="search-box" action="/search">
									<input class="search-field text search-input" placeholder="Rechercher un livre..."
										type="search" name="params">
                                    {{-- name="params" : le mot saisi sera envoyé comme ?params=... dans l'URL --}}
								</form>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

    {{-- =====================================================================
		EN-TÊTE PRINCIPAL : logo + menu de navigation
    ====================================================================== --}}
	<header id="header">
		<div class="container-fluid">
			<div class="row">

                {{-- Logo BookHub : cliquable, redirige vers l'accueil --}}
				<div class="col-md-2">
					<div class="main-logo">
						<a href="/"><img src="{{ asset('images/main-logo.png') }}" alt="BookHub" style="max-height: 170px; width: auto;"></a>
                        {{-- asset() génère le chemin correct vers le dossier public/images/ --}}
					</div>
				</div>

                {{-- Menu de navigation principal --}}
				<div class="col-md-10">
					<nav id="navbar">
						<div class="main-menu stellarnav">
							<ul class="menu-list">

                                {{-- Lien Accueil : visible par tous --}}
								<li class="menu-item active"><a href="/">Accueil</a></li>

                                {{-- Lien Catalogue : visible par tous --}}
								<li class="menu-item"><a href="/search/all">Catalogue</a></li>

                                {{-- Les liens suivants ne s'affichent que si l'utilisateur est connecté --}}
								@auth
									{{-- Mes emprunts : visible par tous les utilisateurs connectés --}}
									<li class="menu-item"><a href="/borrowing">Mes emprunts</a></li>

                                    {{-- Menu Back-Office : visible UNIQUEMENT pour les bibliothécaires (role_id = 1) --}}
									@if(Auth::user()->role_id === 1)
										<li class="menu-item has-sub">
											<a href="#" class="nav-link">Back Office</a>
                                            {{-- Sous-menu déroulant (has-sub = a un sous-menu) --}}
											<ul>
												<li><a href="/bo/copies">Exemplaires</a></li>  {{-- Gestion des exemplaires --}}
												<li><a href="/bo/profils">Usagers</a></li>      {{-- Gestion des usagers --}}
											</ul>
										</li>
									@endif
								@endauth

							</ul>

                            {{-- Menu hamburger pour mobile (les 3 barres horizontales) --}}
							<div class="hamburger">
								<span class="bar"></span>
								<span class="bar"></span>
								<span class="bar"></span>
							</div>

						</div>
					</nav>
				</div>

			</div>
		</div>
	</header>

</div>
