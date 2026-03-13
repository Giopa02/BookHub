@extends('template')

@section('title', 'BookHub - Changer le mot de passe')

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-6">

				<div class="section-header align-center">
					<h2 class="section-title">Changer le mot de passe</h2>
				</div>

				@if($user->password_changed_at)
					<p class="text-center text-muted mb-4">
						Dernier changement : {{ $user->password_changed_at->format('d/m/Y à H:i') }}
					</p>
				@endif

				@if(session('success'))
					<div class="alert alert-success">{{ session('success') }}</div>
				@endif

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
						<form method="POST" action="/change-password">
							@csrf

							<div class="mb-3">
								<label for="current_password" class="form-label">Mot de passe actuel</label>
								<input type="password" class="form-control" id="current_password" name="current_password" required>
							</div>

							<div class="mb-3">
								<label for="password" class="form-label">Nouveau mot de passe</label>
								<input type="password" class="form-control" id="password" name="password" required>
								<small class="text-muted">
									Minimum 8 caractères, une majuscule, un chiffre et un caractère spécial (@$!%*?&#).
								</small>
							</div>

							<div class="mb-3">
								<label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
								<input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
							</div>

							<p class="text-muted small">
								<i class="icon icon-info"></i> Vous ne pouvez pas réutiliser un de vos 5 derniers mots de passe.
							</p>

							<div class="d-flex gap-2">
								<button type="submit" class="btn btn-dark">Modifier le mot de passe</button>
								<a href="/profil" class="btn btn-outline-secondary">Annuler</a>
							</div>
						</form>
					</div>
				</div>

			</div>
		</div>
	</div>
</section>

@endsection