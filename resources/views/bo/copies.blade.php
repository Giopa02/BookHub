@extends('template')

@section('title', 'BookHub - BO Exemplaires')

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h2>Gestion des exemplaires</h2>
			<a href="/bo/exemplar/add" class="btn btn-dark">+ Ajouter un exemplaire</a>
		</div>

		<div class="row mb-4">
			<div class="col-md-6">
				<input type="text" id="searchCopy" class="form-control" placeholder="Rechercher un exemplaire (titre, auteur, mise en service, statut)..." onkeyup="filterUsers()">
			</div>
		</div>

		@if(session('success'))
			<div class="alert alert-success">{{ session('success') }}</div>
		@endif

		<table class="table table-striped">
			<thead>
				<tr>
					<th>ID</th>
					<th>Livre</th>
					<th>Auteur</th>
					<th>Mise en service</th>
					<th>Statut</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				
				@forelse($copies ?? [] as $copy)
				@php /** @var \App\Models\Copy $copy */ @endphp <!-- evite avertissements de l'IDE "Trying to get property of non-object of type void" -->
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
					<td colspan="6">Aucun exemplaire enregistré.</td>
				</tr>
				@endforelse
			</tbody>
		</table>
	</div>
</section>

<!-- filtre qui permet de chercher grace au colonnes-->
<script>
function filterUsers() {
    let input = document.getElementById('searchCopy').value.toLowerCase();
    let rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? '' : 'none';
    });
}
</script>

@endsection