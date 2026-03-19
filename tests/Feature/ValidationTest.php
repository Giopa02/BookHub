<?php

// Ce fichier teste toutes les validations (règles de saisie) de l'application.

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use App\Models\Status;
use App\Models\PasswordHistory;
use Illuminate\Support\Facades\Hash;

class ValidationTest extends TestCase
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

    // -----------------------------------------------------------------------
    // Tests de validation du formulaire d'INSCRIPTION
    // -----------------------------------------------------------------------

    public function test_inscription_refuse_mot_de_passe_trop_court(): void
    {
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Ab1!short', 'password_confirmation' => 'Ab1!short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_inscription_refuse_mot_de_passe_sans_majuscule(): void
    {
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'password1234!', 'password_confirmation' => 'password1234!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_inscription_refuse_mot_de_passe_sans_chiffre(): void
    {
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Passwordabcd!', 'password_confirmation' => 'Passwordabcd!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_inscription_refuse_mot_de_passe_sans_caractere_special(): void
    {
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password12345', 'password_confirmation' => 'Password12345',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_inscription_accepte_mot_de_passe_valide(): void
    {
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password1234!', 'password_confirmation' => 'Password1234!',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['email' => 'test@test.com']);
    }

    public function test_inscription_refuse_mot_de_passe_non_confirme(): void
    {
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password1234!', 'password_confirmation' => 'Different1234!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_inscription_refuse_email_deja_utilise(): void
    {
        User::create([
            'name' => 'Existant', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => bcrypt('Password1234!'), 'role_id' => 2,
        ]);

        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password1234!', 'password_confirmation' => 'Password1234!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_inscription_refuse_champs_vides(): void
    {
        $response = $this->post('/subscription', []);

        $response->assertSessionHasErrors(['name', 'prenom', 'email', 'password']);
    }

    // -----------------------------------------------------------------------
    // Tests de validation de la CONNEXION
    // -----------------------------------------------------------------------

    public function test_connexion_refuse_identifiants_incorrects(): void
    {
        User::create([
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => bcrypt('Password1234!'), 'role_id' => 2,
        ]);

        $response = $this->post('/connect', [
            'email' => 'test@test.com',
            'password' => 'MauvaisMdp1234!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // -----------------------------------------------------------------------
    // Tests de validation du CHANGEMENT DE MOT DE PASSE
    // -----------------------------------------------------------------------

    public function test_changement_mdp_refuse_ancien_mot_de_passe_incorrect(): void
    {
        $user = User::create([
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => bcrypt('Password1234!'), 'role_id' => 2,
        ]);

        $response = $this->actingAs($user)->post('/change-password', [
            'current_password' => 'MauvaisMdp1234!',
            'password' => 'NewPassword1234!',
            'password_confirmation' => 'NewPassword1234!',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_changement_mdp_refuse_reutilisation_des_5_derniers(): void
    {
        $user = User::create([
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => Hash::make('Password1234!'), 'role_id' => 2,
        ]);

        PasswordHistory::create(['user_id' => $user->id, 'password' => Hash::make('Password1234!')]);
        PasswordHistory::create(['user_id' => $user->id, 'password' => Hash::make('OldPassword2!')]);
        PasswordHistory::create(['user_id' => $user->id, 'password' => Hash::make('OldPassword3!')]);

        $response = $this->actingAs($user)->post('/change-password', [
            'current_password' => 'Password1234!',
            'password' => 'OldPassword2!',
            'password_confirmation' => 'OldPassword2!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_changement_mdp_accepte_nouveau_mot_de_passe_valide(): void
    {
        $user = User::create([
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => Hash::make('Password1234!'), 'role_id' => 2,
        ]);

        PasswordHistory::create(['user_id' => $user->id, 'password' => Hash::make('Password1234!')]);

        $response = $this->actingAs($user)->post('/change-password', [
            'current_password' => 'Password1234!',
            'password' => 'ToutNouveau99!',
            'password_confirmation' => 'ToutNouveau99!',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profil');
    }

    // -----------------------------------------------------------------------
    // Tests sur les règles métier de l'inscription
    // -----------------------------------------------------------------------

    public function test_inscription_cree_un_usager_par_defaut(): void
    {
        $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password1234!', 'password_confirmation' => 'Password1234!',
        ]);

        $user = User::where('email', 'test@test.com')->first();
        $this->assertEquals(2, $user->role_id);
    }

    public function test_inscription_sauvegarde_le_mot_de_passe_dans_lhistorique(): void
    {
        $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password1234!', 'password_confirmation' => 'Password1234!',
        ]);

        $user = User::where('email', 'test@test.com')->first();
        $this->assertEquals(1, $user->passwordHistories()->count());
    }
}