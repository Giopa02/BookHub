@extends('template')

@section('title', 'BookHub - ' . ($book->title ?? 'Détail'))

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<div class="row">

			<div class="col-md-4">
				<figure>
					<img src="{{ $book->cover_image ? asset('images/' . $book->cover_image) : asset('images/single-image.jpg') }}" alt="{{ $book->title }}" class="single-image img-fluid">
				</figure>
			</div>

			<div class="col-md-8">
				<h2>{{ $book->title }}</h2>
				<p class="text-muted">Par {{ $book->author->name ?? 'Auteur inconnu' }}</p>

				@if($book->publication_date)
					<p><strong>Date de publication :</strong> {{ \Carbon\Carbon::parse($book->publication_date)->format('d/m/Y') }}</p>
				@endif

				<div class="mb-3">
					@foreach($book->categories as $cat)
						<span class="badge bg-secondary">{{ $cat->libelle }}</span>
					@endforeach
				</div>

				@if($book->description)
					<p>{{ $book->description }}</p>
				@endif

				<hr>

				<h4>Exemplaires</h4>
				<table class="table">
					<thead>
						<tr>
							<th>#</th>
							<th>Mise en service</th>
							<th>Statut</th>
							<th>État</th>
						</tr>
					</thead>
					<tbody>
						@forelse($book->copies as $copy)
						<tr>
							<td>{{ $copy->id }}</td>
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
						</tr>
						@empty
						<tr>
							<td colspan="4">Aucun exemplaire enregistré.</td>
						</tr>
						@endforelse
					</tbody>
				</table>

				@auth
					@if($book->copies->where('status_id', 1)->count() > 0)
						<form method="POST" action="/borrowing/{{ $book->copies->where('status_id', 1)->first()->id }}">
							@csrf <!--contre les attaques CRSF -->
							<button type="submit" class="btn btn-dark">Emprunter un exemplaire</button>
						</form>
					@else
						<button class="btn btn-secondary" disabled>Aucun exemplaire disponible</button>
					@endif
				@else
					<a href="/connect" class="btn btn-outline-dark">Connectez-vous pour emprunter</a>
				@endauth
			</div>

		</div>
	</div>
</section>

@endsection