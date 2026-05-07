{{-- =========================================================================
	footer.blade.php — Pied de page présent sur toutes les pages
========================================================================= --}}

<footer id="footer">

    <div class="footer-main">
        <div class="container">
            <div class="row py-5">

                <div class="col-md-4 mb-4">
                    <img src="{{ asset('images/main-logo.png') }}" alt="BookHub" class="footer-logo mb-3">
                    <p>Votre bibliothèque en ligne. Empruntez, découvrez et explorez des milliers de livres.</p>
                </div>

                <div class="col-md-2 offset-md-2 mb-4">
                    <h6 class="footer-heading">Découvrir</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="/">Accueil</a></li>
                        <li><a href="/search/all">Catalogue</a></li>
                    </ul>
                </div>

                <div class="col-md-2 mb-4">
                    <h6 class="footer-heading">Mon compte</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="/connect">Connexion</a></li>
                        <li><a href="/subscription">Inscription</a></li>
                        <li><a href="/profil">Mon profil</a></li>
                    </ul>
                </div>

                <div class="col-md-2 mb-4">
                    <h6 class="footer-heading">Aide</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Centre d'aide</a></li>
                        <li><a href="#">Nous contacter</a></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <div id="footer-bottom">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-3">
                <p class="mb-0">&copy; {{ date('Y') }} BookHub. Tous droits réservés.</p>
                <div class="social-links">
                    <ul class="list-unstyled d-flex mb-0">
                        <li><a href="#"><i class="icon icon-facebook"></i></a></li>
                        <li><a href="#"><i class="icon icon-twitter"></i></a></li>
                        <li><a href="#"><i class="icon icon-youtube-play"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</footer>
