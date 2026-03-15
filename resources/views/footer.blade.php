{{-- =========================================================================
	footer.blade.php — Pied de page présent sur toutes les pages
	=========================================================================
	Ce fichier est inclus dans template.blade.php via @include('footer').
	Il contient :
	- Le logo et la description de BookHub
	- Les liens de navigation rapide
	- Les liens "Mon compte"
	- Les liens d'aide
	- La barre de copyright avec les réseaux sociaux
--}}

{{-- =========================================================================
	FOOTER PRINCIPAL : colonnes avec les liens et infos
========================================================================== --}}
<footer id="footer">
	<div class="container">
		<div class="row">

            {{-- Colonne 1 : Logo et description --}}
			<div class="col-md-4">
				<div class="footer-item">
					<div class="company-brand">
						<img src="{{ asset('images/main-logo.png') }}" alt="logo" class="footer-logo" style="max-height: 150px; width: auto;">
						<!--<a href="/"><img src="{{ asset('images/main-logo.png') }}" alt="BookHub" style="max-height: 60px; width: auto;"></a>-->
						<p>BookHub - Votre bibliothèque en ligne. Empruntez, découvrez et explorez des milliers de livres.</p>
					</div>
				</div>
			</div>

            {{-- Colonne 2 : Liens de découverte (Accueil, Catalogue) --}}
			<div class="col-md-2">
				<div class="footer-menu">
					<h5>Découvrir</h5>
					<ul class="menu-list">
						<li class="menu-item"><a href="/">Accueil</a></li>
						<li class="menu-item"><a href="/search/all">Catalogue</a></li>
					</ul>
				</div>
			</div>

            {{-- Colonne 3 : Liens vers l'espace compte utilisateur --}}
			<div class="col-md-2">
				<div class="footer-menu">
					<h5>Mon compte</h5>
					<ul class="menu-list">
						<li class="menu-item"><a href="/connect">Connexion</a></li>
						<li class="menu-item"><a href="/subscription">Inscription</a></li>
						<li class="menu-item"><a href="/profil">Mon profil</a></li>
					</ul>
				</div>
			</div>

            {{-- Colonne 4 : Liens d'aide (pour l'instant des liens vides "#") --}}
			<div class="col-md-2">
				<div class="footer-menu">
					<h5>Aide</h5>
					<ul class="menu-list">
						<li class="menu-item"><a href="#">Centre d'aide</a></li>
						<li class="menu-item"><a href="#">Nous contacter</a></li>
					</ul>
				</div>
			</div>

		</div>
	</div>
</footer>

{{-- =========================================================================
	BARRE DE COPYRIGHT (en dessous du footer)
========================================================================== --}}
<div id="footer-bottom">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="copyright">
					<div class="row">

                        {{-- Texte de copyright avec l'année générée dynamiquement --}}
						<div class="col-md-6">
							<p>&copy; {{ date('Y') }} BookHub. Tous droits réservés.</p>
                            {{-- date('Y') affiche l'année actuelle automatiquement (ex: 2026) --}}
						</div>

                        {{-- Icônes réseaux sociaux en bas à droite --}}
						<div class="col-md-6">
							<div class="social-links align-right">
								<ul>
									<li><a href="#"><i class="icon icon-facebook"></i></a></li>
									<li><a href="#"><i class="icon icon-twitter"></i></a></li>
									<li><a href="#"><i class="icon icon-youtube-play"></i></a></li>
								</ul>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
