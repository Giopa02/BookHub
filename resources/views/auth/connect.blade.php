{{-- =========================================================================
	auth/connect.blade.php — Page de connexion
	=========================================================================
	Cette vue affiche le formulaire de connexion.
	L'utilisateur saisit son email et son mot de passe.
	Si les identifiants sont corrects, un code 2FA est envoyé par email et l'utilisateur est redirigé vers la page de vérification du code.

	Données reçues depuis le Controller (UserController@connect) :
	- $errors : collection des erreurs de validation (si mauvais identifiants)
--}}
@extends('template')

@section('title', 'BookHub - Connexion')

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-6">

				<div class="section-header align-center">
					<h2 class="section-title">Connexion</h2>
				</div>

                {{-- Affichage des erreurs de validation (email ou mot de passe incorrect) --}}
                {{-- $errors->any() = true si au moins une erreur existe --}}
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
                        {{-- Formulaire POST vers /connect → UserController@login --}}
						<form method="POST" action="/connect">
                            {{-- @csrf génère un champ caché avec un token de sécurité anti-piratage --}}
							@csrf

							<div class="mb-3">
								<label for="email" class="form-label">Adresse email</label>
                                {{-- old('email') : si le formulaire est soumis avec erreur,on pré-remplit le champ avec la valeur saisie précédemment --}}
								<input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
							</div>

							<div class="mb-3">
								<label for="password" class="form-label">Mot de passe</label>
								<input type="password" class="form-control" id="password" name="password" required>
                                {{-- type="password" masque les caractères saisis --}}
							</div>

							<div class="d-grid">
								<button type="submit" class="btn btn-dark">Se connecter</button>
							</div>
						</form>

                        {{-- Lien vers la page d'inscription pour les nouveaux utilisateurs --}}
						<p class="text-center mt-3">
							Pas encore de compte ? <a href="/subscription">S'inscrire</a>
						</p>
					</div>
				</div>

			</div>
		</div>
	</div>
</section>

@endsection
