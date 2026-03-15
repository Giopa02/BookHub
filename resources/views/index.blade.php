{{-- =========================================================================
	index.blade.php — Page d'accueil du site BookHub
	=========================================================================
	Elle affiche :
	1. Un slider (carrousel) avec deux diapositives de présentation
	2. Une section "Livres à la une" avec 8 livres affichés aléatoirement
--}}

{{-- On dit à Laravel que cette vue utilise le gabarit "template.blade.php" --}}
@extends('template')

{{-- On définit le titre de l'onglet du navigateur pour cette page --}}
@section('title', 'BookHub - Accueil')

{{-- Tout ce qui est entre @section('content') et @endsection
	remplace le @yield('content') dans template.blade.php --}}
@section('content')

{{-- =========================================================================
	SECTION 1 : Slider / Carrousel de la page d'accueil
	Affiche 2 diapositives avec image de fond, texte et bouton d'action
========================================================================== --}}
<section id="billboard">
	<div class="container">
		<div class="row">
			<div class="col-md-12">

                {{-- Bouton "précédent" du slider --}}
				<button class="prev slick-arrow">
					<i class="icon icon-arrow-left"></i>
				</button>

				<div class="main-slider pattern-overlay">

                    {{-- Diapositive 1 : message de bienvenue --}}
					<div class="slider-item">
						<div class="banner-content">
							<h2 class="banner-title">Bienvenue sur BookHub</h2>
							<p>Découvrez notre catalogue de livres et empruntez vos ouvrages préférés en quelques clics.</p>
							<div class="btn-wrap">
								<a href="/search/all" class="btn btn-outline-accent btn-accent-arrow">Voir le catalogue<i class="icon icon-ns-arrow-right"></i></a>
							</div>
						</div>
						<img src="{{ asset('images/main-banner1.jpg') }}" alt="banner" class="banner-image">
					</div>

                    {{-- Diapositive 2 : invitation à s'inscrire --}}
					<div class="slider-item">
						<div class="banner-content">
							<h2 class="banner-title">Des milliers de livres</h2>
							<p>Romans, sciences, histoire, technologie... Explorez toutes nos catégories.</p>
							<div class="btn-wrap">
								<a href="/subscription" class="btn btn-outline-accent btn-accent-arrow">S'inscrire<i class="icon icon-ns-arrow-right"></i></a>
							</div>
						</div>
						<img src="{{ asset('images/main-banner2.jpg') }}" alt="banner" class="banner-image">
					</div>

				</div>

                {{-- Bouton "suivant" du slider --}}
				<button class="next slick-arrow">
					<i class="icon icon-arrow-right"></i>
				</button>

			</div>
		</div>
	</div>
</section>

{{-- =========================================================================
	SECTION 2 : Grille de livres "à la une"
	Affiche 8 livres choisis aléatoirement par le Controller (routes/web.php)
========================================================================== --}}
<section id="featured-books" class="py-5 my-5">
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				<div class="section-header align-center">
					<div class="title">
						<span>Notre sélection</span>
					</div>
					<h2 class="section-title">Livres à la une</h2>
				</div>

				<div class="product-list" data-aos="fade-up">
					<div class="row">

                        {{-- Boucle dynamique sur les livres --}}
                        {{-- @forelse est comme @foreach mais gère le cas "liste vide" avec @empty --}}
						@forelse($books ?? [] as $book)
						<div class="col-md-3">
							<div class="product-item">
								<figure class="product-style">
                                    {{-- Si le livre a une image de couverture, on l'affiche,
										sinon on utilise une image par défaut --}}
									<img src="{{ $book->cover_image ? asset('images/' . $book->cover_image) : asset('images/product-item1.jpg') }}" alt="{{ $book->title }}" class="product-item">

                                    {{-- Bouton pour aller à la page de détail du livre --}}
									<a href="/exemplar/{{ $book->id }}" class="add-to-cart">Voir détails</a>
								</figure>
								<figcaption>
									<h3>{{ $book->title }}</h3> {{-- Titre du livre --}}
									<span>{{ $book->author->name ?? 'Auteur inconnu' }}</span>
                                    {{-- ?? 'Auteur inconnu' = si l'auteur n'existe pas, on affiche ce texte par défaut --}}
								</figcaption>
							</div>
						</div>
						@empty
                        {{-- Si aucun livre n'est disponible (liste vide), on affiche ce message --}}
						<div class="col-md-12">
							<p class="text-center">Aucun livre disponible pour le moment.</p>
						</div>
						@endforelse

					</div>
				</div>

			</div>
		</div>

        {{-- Lien pour voir tout le catalogue --}}
		<div class="row">
			<div class="col-md-12">
				<div class="btn-wrap align-right">
					<a href="/search/all" class="btn-accent-arrow">Voir tout le catalogue <i class="icon icon-ns-arrow-right"></i></a>
				</div>
			</div>
		</div>
	</div>
</section>

@endsection
