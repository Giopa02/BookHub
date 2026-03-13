@extends('template')

@section('title', 'BookHub - BO Exemplaires')

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h2>Gestion des exemplaires</h2>
			<a href="/bo/exemplar/add" class="btn btn-dark">+ Ajouter un exemplaire</a>
		</div>

		@if(session('success'))
			<div class="alert alert-success">{{ session('success') }}</div>
		@endif


		<div class="row mb-4">
			<div class="col-md-6">
				<form method="GET" action="/bo/copies" class="d-flex gap-2">
					<input type="text" name="search" class="form-control" placeholder="Rechercher (livre, auteur, statut, état)..." value="{{ request('search') }}">
					<button type="submit" class="btn btn-dark">Rechercher</button>
					@if(request('search'))
						<a href="/bo/copies" class="btn btn-outline-secondary">Effacer</a>
					@endif
				</form>
			</div>
		</div>

		<table class="table table-striped">
			<thead>
				<tr>
					<th>ID</th>
					<th>Livre</th>
					<th>Auteur</th>
					<th>Mise en service</th>
					<th>Statut</th>
					<th>État</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				@php /** @var \App\Models\Copy $copy */ @endphp <!-- evite avertissements de l'IDE "Trying to get property of non-object of type void" -->
				@forelse($copies ?? [] as $copy)
				<tr>
					<td>{{ $copy->id }}</td>
					<td>{{ $copy->book->title ?? 'N/A' }}</td>
					<td>{{ $copy->book->author->name ?? 'N/A' }}</td>
					<td>{{ \Carbon\Carbon::parse($copy->commission_date)->format('d/m/Y') }}</td>
					<td>
						@if($copy->status->status === 'disponible')
							<span class="badge bg-success">{{ $copy->status->status }}</span>
						@else
							<span class="badge bg-warning">{{ $copy->status->status }}</span>
						@endif
					</td>
					<td>
						@if($copy->etat === 'excellent')
							<span class="badge bg-success">{{ $copy->etat }}</span>
						@elseif($copy->etat === 'bon')
							<span class="badge bg-info">{{ $copy->etat }}</span>
						@else
							<span class="badge bg-secondary">{{ $copy->etat }}</span>
						@endif
					</td>
					<td>
						<a href="/bo/exemplar/{{ $copy->id }}" class="btn btn-sm btn-outline-dark">Voir</a>
						<a href="/bo/exemplar/update/{{ $copy->id }}" class="btn btn-sm btn-outline-primary">Modifier</a>
						<form method="POST" action="/bo/exemplar/delete/{{ $copy->id }}" class="d-inline" onsubmit="return confirm('Confirmer la suppression ?')">
							@csrf
							@method('DELETE')
							<button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
						</form>
					</td>
				</tr>
				@empty
				<tr>
					<td colspan="7">Aucun exemplaire enregistré.</td>
				</tr>
				@endforelse
			</tbody>
		</table>

		<div class="d-flex justify-content-center mt-4">
			{{ $copies->links() }}
		</div>
	</div>
</section>



@endsection