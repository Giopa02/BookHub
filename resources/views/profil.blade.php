<!--pour le profil perso ! -->

@extends('template')

@section('title', 'BookHub - Mon profil')

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<div class="row">
			<div class="col-md-4">
				<div class="card p-4">
					<div class="card-body text-center">
						<i class="icon icon-user" style="font-size: 4rem;"></i>
						<h3 class="mt-3">{{ $user->prenom }} {{ $user->name }}</h3>
						<p class="text-muted">{{ $user->email }}</p>
						<p><span class="badge bg-dark">{{ $user->role->role ?? 'Usager' }}</span></p>
						<a href="/change-password" class="btn btn-outline-dark btn-sm mt-2">Changer le mot de passe</a>
					</div>
				</div>
			</div>

			<div class="col-md-8">
				<div class="card p-4 mb-4">
					<h4>Emprunt en cours</h4>
					@if($currentBorrow ?? false)
						<p><strong>Date d'emprunt :</strong> {{ \Carbon\Carbon::parse($currentBorrow->borrowing_date)->format('d/m/Y') }}</p>
						<p><strong>Retour prévu :</strong> {{ \Carbon\Carbon::parse($currentBorrow->borrowing_date)->addDays(30)->format('d/m/Y') }}</p>

						<table class="table">
							<thead>
								<tr>
									<th>Exemplaire</th>
									<th>Livre</th>
								</tr>
							</thead>
							<tbody>
								@foreach($currentBorrow->copies as $copy)
								<tr>
									<td>#{{ $copy->id }}</td>
									<td>{{ $copy->book->title ?? 'N/A' }}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					@else
						<p class="text-muted">Aucun emprunt en cours.</p>
					@endif
				</div>

				<div class="card p-4">
					<h4>Historique des emprunts</h4>
					@forelse($borrowHistory ?? [] as $borrow)
						<div class="border-bottom pb-2 mb-2">
							<p class="mb-1">
								<strong>Emprunt du {{ \Carbon\Carbon::parse($borrow->borrowing_date)->format('d/m/Y') }}</strong>
								— Retourné le {{ \Carbon\Carbon::parse($borrow->return_date)->format('d/m/Y') }}
							</p>
							<small class="text-muted">
								{{ $borrow->copies->count() }} exemplaire(s) :
								@foreach($borrow->copies as $copy)
									{{ $copy->book->title ?? 'N/A' }}@if(!$loop->last), @endif
								@endforeach
							</small>
						</div>
					@empty
						<p class="text-muted">Aucun historique d'emprunt.</p>
					@endforelse
				</div>
			</div>
		</div>
	</div>
</section>

@endsection