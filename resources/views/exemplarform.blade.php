@extends('template')

@section('title', isset($copy) ? 'BookHub - Modifier exemplaire' : 'BookHub - Ajouter exemplaire')

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-8">

				<h2>{{ isset($copy) ? 'Modifier un exemplaire' : 'Ajouter un exemplaire' }}</h2>

				@if($errors->any())
					<div class="alert alert-danger">
						<ul class="mb-0">
							@foreach($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				<div class="card p-4 mt-3">
					<form method="POST" action="{{ isset($copy) ? '/bo/exemplar/update/' . $copy->id : '/bo/exemplar/add' }}">
						@csrf
						@if(isset($copy))
							@method('PUT')
						@endif

						<div class="mb-3">
							<label for="book_id" class="form-label">Livre</label>
							<select class="form-select" id="book_id" name="book_id" required>
								<option value="">-- Sélectionner un livre --</option>
								@foreach($books ?? [] as $book)
									<option value="{{ $book->id }}" {{ (old('book_id', $copy->book_id ?? '') == $book->id) ? 'selected' : '' }}>
										{{ $book->title }} — {{ $book->author->name ?? '' }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="mb-3">
							<label for="commission_date" class="form-label">Date de mise en service</label>
							<input type="date" class="form-control" id="commission_date" name="commission_date"
								value="{{ old('commission_date', $copy->commission_date ?? date('Y-m-d')) }}" required>
						</div>

						<div class="mb-3">
							<label for="status_id" class="form-label">Statut</label>
							<select class="form-select" id="status_id" name="status_id" required>
								@foreach($statuses ?? [] as $status)
									<option value="{{ $status->id }}" {{ (old('status_id', $copy->status_id ?? '') == $status->id) ? 'selected' : '' }}>
										{{ $status->status }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="d-flex gap-2">
							<button type="submit" class="btn btn-dark">{{ isset($copy) ? 'Mettre à jour' : 'Ajouter' }}</button>
							<a href="/bo/copies" class="btn btn-outline-secondary">Annuler</a>
						</div>
					</form>
				</div>

			</div>
		</div>
	</div>
</section>

@endsection