<?php

// Ce fichier contient les tests des Controllers.

// Ces tests utilisent PHPUnit (via Pest) et le système de test intégré à Laravel.
// Pour lancer les tests : php artisan test

// Chaque méthode qui commence par "test_" est un test distinct.
// Un test réussit si toutes ses "assertions" (vérifications) sont vraies.

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase; // Réinitialise la base de données entre chaque test
use App\Models\User;
use App\Models\Role;
use App\Models\Status;
use App\Models\Author;
use App\Models\Book;
use App\Models\Copy;

class ControllerTest extends TestCase
{
    use RefreshDatabase;

    // Ces propriétés stockent les utilisateurs créés pour les tests
    protected User $librarian; // Un bibliothécaire (accès BO)
    protected User $user;      // Un usager normal

    // setUp() est appelée automatiquement AVANT chaque test de cette classe
    // Elle prépare les données nécessaires à tous les tests
    protected function setUp(): void
    {
        parent::setUp(); // On appelle d'abord le setUp de la classe parente (Laravel)

        // On crée les rôles et les statuts nécessaires
        Role::create(['role' => 'bibliothécaire']);
        Role::create(['role' => 'usager']);
        Status::create(['status' => 'disponible']);
        Status::create(['status' => 'emprunté']);

        // On crée un bibliothécaire de test
        $this->librarian = User::create([
            'name' => 'Dupont', 'prenom' => 'Marie', 'email' => 'biblio@test.com',
            'password' => bcrypt('Password1!'), 'role_id' => 1, // 1 = bibliothécaire
        ]);

        // On crée un usager de test
        $this->user = User::create([
            'name' => 'Martin', 'prenom' => 'Lucas', 'email' => 'user@test.com',
            'password' => bcrypt('Password1!'), 'role_id' => 2, // 2 = usager
        ]);
    }

    // -----------------------------------------------------------------------
    // Tests d'accessibilité des pages publiques
    // assertStatus(200) = vérifie que la page se charge correctement (code HTTP 200 = OK)
    // -----------------------------------------------------------------------

    public function test_la_page_accueil_est_accessible(): void
    {
        // $this->get('/') = simuler une visite de la page d'accueil
        $response = $this->get('/');
        $response->assertStatus(200); // La page doit répondre 200 (OK)
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
        // On crée un auteur et un livre dans la base de test
        $author = Author::create(['name' => 'Victor Hugo']);
        Book::create(['title' => 'Les Misérables', 'author_id' => $author->id]);

        // On effectue une recherche sur ce titre
        $response = $this->get('/search/Misérables');
        $response->assertStatus(200);
        $response->assertSee('Les Misérables'); // Le titre doit apparaître dans la réponse HTML
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
    // Ces tests vérifient que les pages BO sont bien protégées
    // assertStatus(403) = accès refusé (code HTTP 403 = Forbidden)
    // -----------------------------------------------------------------------

    public function test_un_visiteur_ne_peut_pas_acceder_au_bo_exemplaires(): void
    {
        // Sans être connecté, on tente d'accéder au Back-Office
        $response = $this->get('/bo/copies');
        $response->assertStatus(403); // Doit être refusé
    }

    public function test_un_usager_ne_peut_pas_acceder_au_bo_exemplaires(): void
    {
        // actingAs($this->user) = simuler une connexion en tant qu'usager
        $response = $this->actingAs($this->user)->get('/bo/copies');
        $response->assertStatus(403); // L'usager ne peut pas accéder au BO
    }

    public function test_un_bibliothecaire_peut_acceder_au_bo_exemplaires(): void
    {
        // actingAs($this->librarian) = simuler une connexion en tant que bibliothécaire
        $response = $this->actingAs($this->librarian)->get('/bo/copies');
        $response->assertStatus(200); // Le bibliothécaire PEUT accéder au BO
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
        $book   = Book::create(['title' => 'Test', 'author_id' => $author->id]);

        // On simule l'envoi du formulaire d'ajout d'exemplaire en tant que bibliothécaire
        $response = $this->actingAs($this->librarian)->post('/bo/exemplar/add', [
            'book_id'         => $book->id,
            'commission_date' => '2024-01-01',
            'status_id'       => 1,
            'etat'            => 'bon',
        ]);

        // Après l'ajout, on doit être redirigé vers la liste des exemplaires
        $response->assertRedirect('/bo/copies');

        // On vérifie que l'exemplaire existe bien en base de données
        $this->assertDatabaseHas('copies', ['book_id' => $book->id, 'etat' => 'bon']);
    }

    public function test_un_bibliothecaire_peut_supprimer_un_exemplaire(): void
    {
        $author = Author::create(['name' => 'Test']);
        $book   = Book::create(['title' => 'Test', 'author_id' => $author->id]);
        $copy   = Copy::create(['commission_date' => '2024-01-01', 'book_id' => $book->id, 'status_id' => 1]);

        // On simule la suppression
        $response = $this->actingAs($this->librarian)->delete('/bo/exemplar/delete/' . $copy->id);

        $response->assertRedirect('/bo/copies');

        // On vérifie que l'exemplaire N'existe PLUS en base de données
        // assertDatabaseMissing = l'inverse de assertDatabaseHas
        $this->assertDatabaseMissing('copies', ['id' => $copy->id]);
    }

    // -----------------------------------------------------------------------
    // Test de déconnexion
    // -----------------------------------------------------------------------

    public function test_la_deconnexion_fonctionne(): void
    {
        // On se déconnecte en visitant /logout
        $response = $this->actingAs($this->user)->get('/logout');

        // On doit être redirigé vers l'accueil
        $response->assertRedirect('/');

        // assertGuest() vérifie que l'utilisateur n'est plus connecté
        $this->assertGuest();
    }
}
