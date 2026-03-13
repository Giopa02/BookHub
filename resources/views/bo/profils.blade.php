@extends('template')

@section('title', 'BookHub - BO Usagers')

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<h2 class="mb-4">Gestion des usagers</h2>
		<div class="row mb-4">
			<div class="col-md-6">
				<input type="text" id="searchUser" class="form-control" placeholder="Rechercher un usager (nom, prénom, email)..." onkeyup="filterUsers()">
			</div>
		</div>

		<table class="table table-striped">
			<thead>
				<tr>
					<th>ID</th>
					<th>Nom</th>
					<th>Prénom</th>
					<th>Email</th>
					<th>Rôle</th>
					<th>Emprunts en cours</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				
				@forelse($users ?? [] as $user)
				@php /** @var \App\Models\User $user */ @endphp <!-- evite avertissements de l'IDE "Trying to get property of non-object of type void" -->
				<tr>
					<td>{{ $user->id }}</td>
					<td>{{ $user->name }}</td>
					<td>{{ $user->prenom }}</td>
					<td>{{ $user->email }}</td>
					<td>{{ $user->role->role ?? 'N/A' }}</td>
					<td>{{ $user->borrows->whereNull('return_date')->count() }}</td>
					<td>
						<a href="/bo/profil/{{ $user->id }}" class="btn btn-sm btn-outline-dark">Voir profil</a>
					</td>
				</tr>
				@empty
				<tr>
					<td colspan="7">Aucun usager enregistré.</td>
				</tr>
				@endforelse
			</tbody>
		</table>

		<div class="d-flex justify-content-center mt-4">
			{{ $users->links() }}
		</div>
	</div>
</section>
<script>
function filterUsers() {
    let input = document.getElementById('searchUser').value.toLowerCase();
    let rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? '' : 'none';
    });
}
</script>
@endsection