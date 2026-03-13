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

				<p class="text-center text-muted mb-4">
					Un code de vérification a été envoyé à votre adresse email. Il expire dans 10 minutes.
				</p>

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
						<form method="POST" action="/verify-2fa">
							@csrf

							<div class="mb-3">
								<label for="code" class="form-label">Code à 6 chiffres</label>
								<input type="text" class="form-control text-center" id="code" name="code"
									maxlength="6" placeholder="000000" required autofocus
									style="font-size: 1.5rem; letter-spacing: 0.5rem;">
							</div>

							<div class="d-grid mb-3">
								<button type="submit" class="btn btn-dark">Vérifier</button>
							</div>
						</form>

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