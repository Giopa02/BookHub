<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Copy;
use App\Models\Status;
use App\Models\Borrow;
use App\Models\PasswordHistory;

class ModelRelationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['role' => 'bibliothécaire']);
        Role::create(['role' => 'usager']);
        Status::create(['status' => 'disponible']);
        Status::create(['status' => 'emprunté']);
    }

    public function test_un_utilisateur_appartient_a_un_role(): void
    {
        $user = User::create([
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => bcrypt('Password1!'), 'role_id' => 2,
        ]);

        $this->assertNotNull($user->role);
        $this->assertEquals('usager', $user->role->role);
    }

    public function test_un_role_a_plusieurs_utilisateurs(): void
    {
        $role = Role::find(2);

        User::create(['name' => 'Test1', 'prenom' => 'A', 'email' => 'a@test.com', 'password' => bcrypt('Password1!'), 'role_id' => 2]);
        User::create(['name' => 'Test2', 'prenom' => 'B', 'email' => 'b@test.com', 'password' => bcrypt('Password1!'), 'role_id' => 2]);

        $this->assertEquals(2, $role->users()->count());
    }

    public function test_un_livre_appartient_a_un_auteur(): void
    {
        $author = Author::create(['name' => 'Victor Hugo']);
        $book = Book::create(['title' => 'Les Misérables', 'author_id' => $author->id]);

        $this->assertNotNull($book->author);
        $this->assertEquals('Victor Hugo', $book->author->name);
    }

    public function test_un_auteur_a_plusieurs_livres(): void
    {
        $author = Author::create(['name' => 'Jules Verne']);

        Book::create(['title' => 'Vingt Mille Lieues sous les mers', 'author_id' => $author->id]);
        Book::create(['title' => 'Le Tour du monde en 80 jours', 'author_id' => $author->id]);

        $this->assertEquals(2, $author->books()->count());
    }

    public function test_un_livre_a_plusieurs_categories(): void
    {
        $author = Author::create(['name' => 'Test']);
        $book = Book::create(['title' => 'Test Book', 'author_id' => $author->id]);

        $cat1 = Category::create(['libelle' => 'Roman']);
        $cat2 = Category::create(['libelle' => 'Science-Fiction']);

        $book->categories()->attach([$cat1->id, $cat2->id]);

        $this->assertEquals(2, $book->categories()->count());
    }

    public function test_une_categorie_a_plusieurs_livres(): void
    {
        $author = Author::create(['name' => 'Test']);
        $cat = Category::create(['libelle' => 'Policier']);

        $book1 = Book::create(['title' => 'Livre 1', 'author_id' => $author->id]);
        $book2 = Book::create(['title' => 'Livre 2', 'author_id' => $author->id]);

        $cat->books()->attach([$book1->id, $book2->id]);

        $this->assertEquals(2, $cat->books()->count());
    }

    public function test_un_livre_a_plusieurs_exemplaires(): void
    {
        $author = Author::create(['name' => 'Test']);
        $book = Book::create(['title' => 'Test', 'author_id' => $author->id]);

        Copy::create(['commission_date' => '2024-01-01', 'book_id' => $book->id, 'status_id' => 1]);
        Copy::create(['commission_date' => '2024-06-01', 'book_id' => $book->id, 'status_id' => 1]);

        $this->assertEquals(2, $book->copies()->count());
    }

    public function test_un_exemplaire_appartient_a_un_livre_et_un_statut(): void
    {
        $author = Author::create(['name' => 'Test']);
        $book = Book::create(['title' => 'Test', 'author_id' => $author->id]);

        $copy = Copy::create(['commission_date' => '2024-01-01', 'book_id' => $book->id, 'status_id' => 1]);

        $this->assertNotNull($copy->book);
        $this->assertNotNull($copy->status);
        $this->assertEquals('Test', $copy->book->title);
        $this->assertEquals('disponible', $copy->status->status);
    }

    public function test_un_emprunt_appartient_a_un_utilisateur(): void
    {
        $user = User::create([
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => bcrypt('Password1!'), 'role_id' => 2,
        ]);

        $borrow = Borrow::create(['borrowing_date' => '2024-01-01', 'user_id' => $user->id]);

        $this->assertNotNull($borrow->user);
        $this->assertEquals('Test', $borrow->user->name);
    }

    public function test_un_emprunt_a_plusieurs_exemplaires(): void
    {
        $user = User::create([
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => bcrypt('Password1!'), 'role_id' => 2,
        ]);
        $author = Author::create(['name' => 'Test']);
        $book = Book::create(['title' => 'Test', 'author_id' => $author->id]);

        $copy1 = Copy::create(['commission_date' => '2024-01-01', 'book_id' => $book->id, 'status_id' => 1]);
        $copy2 = Copy::create(['commission_date' => '2024-01-01', 'book_id' => $book->id, 'status_id' => 1]);

        $borrow = Borrow::create(['borrowing_date' => '2024-01-01', 'user_id' => $user->id]);
        $borrow->copies()->attach([$copy1->id, $copy2->id]);

        $this->assertEquals(2, $borrow->copies()->count());
    }

    public function test_un_utilisateur_a_un_historique_de_mots_de_passe(): void
    {
        $user = User::create([
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => bcrypt('Password1!'), 'role_id' => 2,
        ]);

        PasswordHistory::create(['user_id' => $user->id, 'password' => bcrypt('Password1!')]);
        PasswordHistory::create(['user_id' => $user->id, 'password' => bcrypt('Password2!')]);

        $this->assertEquals(2, $user->passwordHistories()->count());
    }
}