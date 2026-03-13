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
						<form method="POST" action="/connect">
							@csrf

							<div class="mb-3">
								<label for="email" class="form-label">Adresse email</label>
								<input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
							</div>

							<div class="mb-3">
								<label for="password" class="form-label">Mot de passe</label>
								<input type="password" class="form-control" id="password" name="password" required>
							</div>

							<div class="d-grid">
								<button type="submit" class="btn btn-dark">Se connecter</button>
							</div>
						</form>

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