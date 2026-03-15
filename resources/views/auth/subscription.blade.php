{{-- =========================================================================
	auth/subscription.blade.php — Page d'inscription
	=========================================================================
	Cette vue affiche le formulaire de création de compte.
	L'utilisateur doit saisir : nom, prénom, email, mot de passe (x2).

	Après une inscription réussie, l'utilisateur est connecté automatiquement et redirigé vers la page d'accueil.
--}}
@extends('template')

@section('title', 'BookHub - Inscription')

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-6">

				<div class="section-header align-center">
					<h2 class="section-title">Inscription</h2>
				</div>

                {{-- Affichage des erreurs de validation --}}
				@if($errors->any())
					<div class="alert alert-danger">
						<ul class="mb-0">
							@foreach($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				<div class="card p-4">
					<div class="card-body">
                        {{-- Formulaire POST vers /subscription → UserController@register --}}
						<form method="POST" action="/subscription">
							@csrf {{-- Token de sécurité CSRF --}}

                            {{-- Champ Nom --}}
							<div class="mb-3">
								<label for="name" class="form-label">Nom</label>
								<input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                {{-- old('name') : conserve la valeur saisie en cas d'erreur --}}
							</div>

                            {{-- Champ Prénom --}}
							<div class="mb-3">
								<label for="prenom" class="form-label">Prénom</label>
								<input type="text" class="form-control" id="prenom" name="prenom" value="{{ old('prenom') }}" required>
							</div>

                            {{-- Champ Email --}}
							<div class="mb-3">
								<label for="email" class="form-label">Adresse email</label>
								<input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
							</div>

                            {{-- Champ Mot de passe --}}
							<div class="mb-3">
								<label for="password" class="form-label">Mot de passe</label>
								<input type="password" class="form-control" id="password" name="password" required>
                                {{-- On ne met pas old() sur les mots de passe : ils ne doivent jamais être pré-remplis --}}
							</div>

                            {{-- Champ Confirmation du mot de passe --}}
                            {{-- Laravel vérifie que "password" et "password_confirmation" sont identiques --}}
							<div class="mb-3">
								<label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
								<input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
							</div>

							<div class="d-grid">
								<button type="submit" class="btn btn-dark">S'inscrire</button>
							</div>
						</form>

                        {{-- Lien vers la connexion pour ceux qui ont déjà un compte --}}
						<p class="text-center mt-3">
							Déjà un compte ? <a href="/connect">Se connecter</a>
						</p>
					</div>
				</div>

			</div>
		</div>
	</div>
</section>

@endsection
