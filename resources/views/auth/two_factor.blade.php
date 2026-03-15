{{-- =========================================================================
	auth/two_factor.blade.php — Page de vérification 2FA (double authentification)
	=========================================================================
	Cette vue est la 2e étape de la connexion.
	Après avoir saisi email + mot de passe corrects (étape 1), l'utilisateur reçoit un code à 6 chiffres par email.
	Il doit le saisir ici pour finaliser sa connexion.

	Le code expire après 10 minutes.
	Un lien permet de demander un nouveau code si l'ancien a expiré.
--}}
@extends('template')

@section('title', 'BookHub - Vérification 2FA')

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-5">

				<div class="section-header align-center">
					<h2 class="section-title">Vérification</h2>
				</div>

                {{-- Message d'information : explique ce que l'utilisateur doit faire --}}
				<p class="text-center text-muted mb-4">
					Un code de vérification a été envoyé à votre adresse email. Il expire dans 10 minutes.
				</p>

                {{-- Message de succès (affiché après un renvoi de code réussi) --}}
				@if(session('success'))
					<div class="alert alert-success">{{ session('success') }}</div>
				@endif

                {{-- Affichage des erreurs (code incorrect ou expiré) --}}
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
                        {{-- Formulaire POST vers /verify-2fa → UserController@verifyTwoFactor --}}
						<form method="POST" action="/verify-2fa">
							@csrf

							<div class="mb-3">
								<label for="code" class="form-label">Code à 6 chiffres</label>
                                {{-- Champ de saisie du code :
									- maxlength="6" : maximum 6 caractères
									- autofocus : le curseur se place automatiquement dans ce champ
									- style avec letter-spacing : espace entre les chiffres pour mieux lire --}}
								<input type="text" class="form-control text-center" id="code" name="code"
									maxlength="6" placeholder="000000" required autofocus
									style="font-size: 1.5rem; letter-spacing: 0.5rem;">
							</div>

							<div class="d-grid mb-3">
								<button type="submit" class="btn btn-dark">Vérifier</button>
							</div>
						</form>

                        {{-- Lien pour recevoir un nouveau code si l'ancien a expiré --}}
                        {{-- Ce lien appelle UserController@resendTwoFactorCode --}}
						<div class="text-center">
							<a href="/resend-2fa" class="text-muted">Renvoyer le code</a>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</section>

@endsection
