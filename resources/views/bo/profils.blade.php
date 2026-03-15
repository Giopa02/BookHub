{{-- =========================================================================
	bo/profils.blade.php — Back-Office : liste des usagers
	=========================================================================
	Cette vue est réservée aux bibliothécaires (role_id = 1).
	Elle affiche la liste de tous les usagers inscrits avec :
	- Un champ de recherche en temps réel (filtrage côté navigateur via JavaScript)
	- Le tableau des usagers avec leurs informations
	- Le nombre d'emprunts en cours pour chaque usager

	Données reçues depuis le Controller (UserController@profils) :
	- $users : collection paginée d'utilisateurs avec leurs rôles et emprunts
--}}
@extends('template')

@section('title', 'BookHub - BO Usagers')

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<h2 class="mb-4">Gestion des usagers</h2>

        {{-- =====================================================================
			Barre de recherche en temps réel (JavaScript côté navigateur)
			Contrairement à la recherche des exemplaires qui passe par le serveur,
			celle-ci filtre les lignes du tableau instantanément sans recharger la page
        ====================================================================== --}}
		<div class="row mb-4">
			<div class="col-md-6">
                {{-- onkeyup="filterUsers()" : la fonction est appelée à chaque frappe au clavier --}}
				<input type="text" id="searchUser" class="form-control" placeholder="Rechercher un usager (nom, prénom, email)..." onkeyup="filterUsers()">
			</div>
		</div>

        {{-- =====================================================================
			Tableau des usagers
        ====================================================================== --}}
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

                    {{-- On compte les emprunts sans date de retour = emprunts en cours --}}
					<td>{{ $user->borrows->whereNull('return_date')->count() }}</td>

					<td>
                        {{-- Bouton pour voir le profil détaillé de cet usager --}}
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

        {{-- Pagination --}}
		<div class="d-flex justify-content-center mt-4">
			{{ $users->links() }}
		</div>
	</div>
</section>

{{-- =====================================================================
	Script JavaScript : filtrage des usagers en temps réel
	Il parcourt toutes les lignes du tableau et masque celles qui ne contiennent pas le texte saisi dans la barre de recherche.
====================================================================== --}}
<script>
function filterUsers() {
    // On récupère le texte saisi, converti en minuscules pour une recherche insensible à la casse
    let input = document.getElementById('searchUser').value.toLowerCase();

    // On récupère toutes les lignes <tr> du corps du tableau
    let rows = document.querySelectorAll('tbody tr');

    // Pour chaque ligne, on vérifie si son texte contient le mot cherché
    rows.forEach(row => {
        let text = row.textContent.toLowerCase(); // Tout le texte de la ligne en minuscules
        // Si le texte de la ligne contient l'input → on affiche la ligne, sinon on la masque
        row.style.display = text.includes(input) ? '' : 'none';
    });
}
</script>
@endsection
