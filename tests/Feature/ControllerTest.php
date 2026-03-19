<?php

// Ce fichier contient les tests des Controllers.

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use App\Models\Status;
use App\Models\Author;
use App\Models\Book;
use App\Models\Copy;

class ControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $librarian;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['role' => 'bibliothécaire']);
        Role::create(['role' => 'usager']);
        Status::create(['status' => 'disponible']);
        Status::create(['status' => 'emprunté']);

        $this->librarian = User::create([
            'name' => 'Dupont', 'prenom' => 'Marie', 'email' => 'biblio@test.com',
            'password' => bcrypt('Password1234!'), 'role_id' => 1,
        ]);

        $this->user = User::create([
            'name' => 'Martin', 'prenom' => 'Lucas', 'email' => 'user@test.com',
            'password' => bcrypt('Password1234!'), 'role_id' => 2,
        ]);
    }

    // -----------------------------------------------------------------------
    // Tests d'accessibilité des pages publiques
    // -----------------------------------------------------------------------

    public function test_la_page_accueil_est_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_la_page_inscription_est_accessible(): void
    {
        $response = $this->get('/subscription');
        $response->assertStatus(200);
    }

    public function test_la_page_connexion_est_accessible(): void
    {
        $response = $this->get('/connect');
        $response->assertStatus(200);
    }

    public function test_la_page_catalogue_est_accessible(): void
    {
        $response = $this->get('/search/all');
        $response->assertStatus(200);
    }

    // -----------------------------------------------------------------------
    // Tests de recherche
    // -----------------------------------------------------------------------

    public function test_la_recherche_par_titre_fonctionne(): void
    {
        $author = Author::create(['name' => 'Victor Hugo']);
        Book::create(['title' => 'Les Misérables', 'author_id' => $author->id]);

        $response = $this->get('/search/Misérables');
        $response->assertStatus(200);
        $response->assertSee('Les Misérables');
    }

    public function test_la_recherche_par_auteur_fonctionne(): void
    {
        $author = Author::create(['name' => 'Agatha Christie']);
        Book::create(['title' => 'Le Crime de l\'Orient-Express', 'author_id' => $author->id]);

        $response = $this->get('/search/Christie');
        $response->assertStatus(200);
        $response->assertSee('Christie');
    }

    // -----------------------------------------------------------------------
    // Tests d'accès au Back-Office
    // -----------------------------------------------------------------------

    public function test_un_visiteur_ne_peut_pas_acceder_au_bo_exemplaires(): void
    {
        $response = $this->get('/bo/copies');
        $response->assertStatus(403);
    }

    public function test_un_usager_ne_peut_pas_acceder_au_bo_exemplaires(): void
    {
        $response = $this->actingAs($this->user)->get('/bo/copies');
        $response->assertStatus(403);
    }

    public function test_un_bibliothecaire_peut_acceder_au_bo_exemplaires(): void
    {
        $response = $this->actingAs($this->librarian)->get('/bo/copies');
        $response->assertStatus(200);
    }

    public function test_un_usager_ne_peut_pas_acceder_au_bo_usagers(): void
    {
        $response = $this->actingAs($this->user)->get('/bo/profils');
        $response->assertStatus(403);
    }

    public function test_un_bibliothecaire_peut_acceder_au_bo_usagers(): void
    {
        $response = $this->actingAs($this->librarian)->get('/bo/profils');
        $response->assertStatus(200);
    }

    // -----------------------------------------------------------------------
    // Tests des actions CRUD sur les exemplaires
    // -----------------------------------------------------------------------

    public function test_un_bibliothecaire_peut_ajouter_un_exemplaire(): void
    {
        $author = Author::create(['name' => 'Test']);
        $book = Book::create(['title' => 'Test', 'author_id' => $author->id]);

        $response = $this->actingAs($this->librarian)->post('/bo/exemplar/add', [
            'book_id' => $book->id,
            'commission_date' => '2024-01-01',
            'status_id' => 1,
            'etat' => 'bon',
        ]);

        $response->assertRedirect('/bo/copies');
        $this->assertDatabaseHas('copies', ['book_id' => $book->id, 'etat' => 'bon']);
    }

    public function test_un_bibliothecaire_peut_supprimer_un_exemplaire(): void
    {
        $author = Author::create(['name' => 'Test']);
        $book = Book::create(['title' => 'Test', 'author_id' => $author->id]);
        $copy = Copy::create(['commission_date' => '2024-01-01', 'book_id' => $book->id, 'status_id' => 1]);

        $response = $this->actingAs($this->librarian)->delete('/bo/exemplar/delete/' . $copy->id);

        $response->assertRedirect('/bo/copies');
        $this->assertDatabaseMissing('copies', ['id' => $copy->id]);
    }

    // -----------------------------------------------------------------------
    // Test de déconnexion
    // -----------------------------------------------------------------------

    public function test_la_deconnexion_fonctionne(): void
    {
        $response = $this->actingAs($this->user)->get('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }
}