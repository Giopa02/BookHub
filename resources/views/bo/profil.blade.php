@extends('template')

@section('title', 'BookHub - Profil de ' . ($user->prenom ?? ''))

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<a href="/bo/profils" class="btn btn-outline-dark mb-3">&larr; Retour à la liste</a>

		<div class="row">
			<div class="col-md-4">
				<div class="card p-4">
					<div class="card-body text-center">
						<i class="icon icon-user" style="font-size: 4rem;"></i>
						<h3 class="mt-3">{{ $user->prenom }} {{ $user->name }}</h3>
						<p class="text-muted">{{ $user->email }}</p>
						<p><span class="badge bg-dark">{{ $user->role->role ?? 'Usager' }}</span></p>
						<p>Inscrit le {{ $user->created_at->format('d/m/Y') }}</p>
					</div>
				</div>
			</div>

			<div class="col-md-8">
				{{-- Emprunt en cours --}}
				<div class="card p-4 mb-4">
					<h4>Emprunt en cours</h4>
					@php
						$currentBorrow = $user->borrows->whereNull('return_date')->first();
					@endphp

					@if($currentBorrow)
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

						<form method="POST" action="/return/{{ $currentBorrow->id }}">
							@csrf
							@method('PATCH')
							<button type="submit" class="btn btn-dark">Enregistrer le retour</button>
						</form>
					@else
						<p class="text-muted">Aucun emprunt en cours.</p>
					@endif
				</div>

				{{-- Historique --}}
				<div class="card p-4">
					<h4>Historique ({{ $user->borrows->whereNotNull('return_date')->count() }} emprunts)</h4>
					@forelse($user->borrows->whereNotNull('return_date') as $borrow)
						<div class="border-bottom pb-2 mb-2">
							<strong>{{ \Carbon\Carbon::parse($borrow->borrowing_date)->format('d/m/Y') }}</strong>
							&rarr; {{ \Carbon\Carbon::parse($borrow->return_date)->format('d/m/Y') }}
							<br>
							<small class="text-muted">
								@foreach($borrow->copies as $copy)
									{{ $copy->book->title ?? 'N/A' }}@if(!$loop->last), @endif
								@endforeach
							</small>
						</div>
					@empty
						<p class="text-muted">Aucun historique.</p>
					@endforelse
				</div>
			</div>
		</div>
	</div>
</section>

@endsection