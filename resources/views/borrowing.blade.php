@extends('template')

@section('title', 'BookHub - Mes emprunts')

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<div class="section-header align-center">
			<h2 class="section-title">Mes emprunts</h2>
		</div>

		@if(session('success'))
			<div class="alert alert-success">{{ session('success') }}</div>
		@endif

		@if(session('error'))
			<div class="alert alert-danger">{{ session('error') }}</div>
		@endif

		{{-- Emprunt en cours --}}
		<div class="card p-4 mb-4">
			<h4>Emprunt en cours</h4>
			@if($currentBorrow ?? false)
				<p><strong>Date d'emprunt :</strong> {{ \Carbon\Carbon::parse($currentBorrow->borrowing_date)->format('d/m/Y') }}</p>
				<p><strong>Retour prévu avant le :</strong> {{ \Carbon\Carbon::parse($currentBorrow->borrowing_date)->addDays(30)->format('d/m/Y') }}</p>

				<table class="table">
					<thead>
						<tr>
							<th>Exemplaire</th>
							<th>Livre</th>
							<th>Auteur</th>
						</tr>
					</thead>
					<tbody>
						@foreach($currentBorrow->copies as $copy)
						<tr>
							<td>#{{ $copy->id }}</td>
							<td>{{ $copy->book->title ?? 'N/A' }}</td>
							<td>{{ $copy->book->author->name ?? 'N/A' }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>

				<form method="POST" action="/return/{{ $currentBorrow->id }}">
					@csrf
					@method('PATCH')
					<button type="submit" class="btn btn-dark">Déposer le retour</button>
				</form>
			@else
				<p class="text-muted">Vous n'avez aucun emprunt en cours. <a href="/search/all">Parcourir le catalogue</a></p>
			@endif
		</div>

		{{-- Historique --}}
		<div class="card p-4">
			<h4>Historique</h4>
			@forelse($borrowHistory ?? [] as $borrow)
				<div class="border-bottom pb-2 mb-2">
					<strong>Emprunt du {{ \Carbon\Carbon::parse($borrow->borrowing_date)->format('d/m/Y') }}</strong>
					— Retourné le {{ \Carbon\Carbon::parse($borrow->return_date)->format('d/m/Y') }}
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
</section>

@endsection