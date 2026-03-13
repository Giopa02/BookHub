@extends('template')

@section('title', 'BookHub - Accueil')

@section('content')

<section id="billboard">
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				<button class="prev slick-arrow">
					<i class="icon icon-arrow-left"></i>
				</button>

				<div class="main-slider pattern-overlay">
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

				<button class="next slick-arrow">
					<i class="icon icon-arrow-right"></i>
				</button>

			</div>
		</div>
	</div>
</section>

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
						@forelse($books ?? [] as $book)
						<div class="col-md-3">
							<div class="product-item">
								<figure class="product-style">
									<img src="{{ $book->cover_image ? asset('images/' . $book->cover_image) : asset('images/product-item1.jpg') }}" alt="{{ $book->title }}" class="product-item">
									<a href="/exemplar/{{ $book->id }}" class="add-to-cart">Voir détails</a>
								</figure>
								<figcaption>
									<h3>{{ $book->title }}</h3>
									<span>{{ $book->author->name ?? 'Auteur inconnu' }}</span>
								</figcaption>
							</div>
						</div>
						@empty
						<div class="col-md-12">
							<p class="text-center">Aucun livre disponible pour le moment.</p>
						</div>
						@endforelse
					</div>
				</div>

			</div>
		</div>

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