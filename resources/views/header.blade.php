<div id="header-wrap">

	<div class="top-content">
		<div class="container-fluid">
			<div class="row">
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
				<div class="col-md-6">
					<div class="right-element">
						@auth
							<a href="/profil" class="user-account for-buy">
								<i class="icon icon-user"></i>
								<span>{{ Auth::user()->name }}</span>
							</a>
							<a href="/logout" class="for-buy">
								<span>Déconnexion</span>
							</a>
						@else
							<a href="/connect" class="user-account for-buy">
								<i class="icon icon-user"></i>
								<span>Connexion</span>
							</a>
							<a href="/subscription" class="for-buy">
								<span>Inscription</span>
							</a>
						@endauth

						<div class="action-menu">
							<div class="search-bar">
								<a href="#" class="search-button search-toggle" data-selector="#header-wrap">
									<i class="icon icon-search"></i>
								</a>
								<form role="search" method="get" class="search-box" action="/search">
									<input class="search-field text search-input" placeholder="Rechercher un livre..."
										type="search" name="params">
								</form>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	<header id="header">
		<div class="container-fluid">
			<div class="row">

				<div class="col-md-2">
					<div class="main-logo">
						<a href="/"><img src="{{ asset('images/main-logo.png') }}" alt="BookHub"></a>
					</div>
				</div>

				<div class="col-md-10">
					<nav id="navbar">
						<div class="main-menu stellarnav">
							<ul class="menu-list">
								<li class="menu-item active"><a href="/">Accueil</a></li>
								<li class="menu-item"><a href="/search/all">Catalogue</a></li>
								@auth
									<li class="menu-item"><a href="/borrowing">Mes emprunts</a></li>
									@if(Auth::user()->role_id === 1)
										<li class="menu-item has-sub">
											<a href="#" class="nav-link">Back Office</a>
											<ul>
												<li><a href="/bo/copies">Exemplaires</a></li>
												<li><a href="/bo/profils">Usagers</a></li>
											</ul>
										</li>
									@endif
								@endauth
							</ul>

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