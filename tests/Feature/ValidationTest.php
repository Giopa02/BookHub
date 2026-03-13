<?php

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

    public function test_inscription_refuse_mot_de_passe_trop_court(): void
    {
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Ab1!', 'password_confirmation' => 'Ab1!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_inscription_refuse_mot_de_passe_sans_majuscule(): void
    {
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'password1!', 'password_confirmation' => 'password1!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_inscription_refuse_mot_de_passe_sans_chiffre(): void
    {
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password!!', 'password_confirmation' => 'Password!!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_inscription_refuse_mot_de_passe_sans_caractere_special(): void
    {
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password12', 'password_confirmation' => 'Password12',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_inscription_accepte_mot_de_passe_valide(): void
    {
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password1!', 'password_confirmation' => 'Password1!',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['email' => 'test@test.com']);
    }

    public function test_inscription_refuse_mot_de_passe_non_confirme(): void
    {
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password1!', 'password_confirmation' => 'Different1!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_inscription_refuse_email_deja_utilise(): void
    {
        User::create([
            'name' => 'Existant', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => bcrypt('Password1!'), 'role_id' => 2,
        ]);

        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password1!', 'password_confirmation' => 'Password1!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_inscription_refuse_champs_vides(): void
    {
        $response = $this->post('/subscription', []);

        $response->assertSessionHasErrors(['name', 'prenom', 'email', 'password']);
    }

    public function test_connexion_refuse_identifiants_incorrects(): void
    {
        User::create([
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => bcrypt('Password1!'), 'role_id' => 2,
        ]);

        $response = $this->post('/connect', [
            'email' => 'test@test.com',
            'password' => 'MauvaisMdp1!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_changement_mdp_refuse_ancien_mot_de_passe_incorrect(): void
    {
        $user = User::create([
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => bcrypt('Password1!'), 'role_id' => 2,
        ]);

        $response = $this->actingAs($user)->post('/change-password', [
            'current_password' => 'MauvaisMdp1!',
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_changement_mdp_refuse_reutilisation_des_5_derniers(): void
    {
        $user = User::create([
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => Hash::make('Password1!'), 'role_id' => 2,
        ]);

        PasswordHistory::create(['user_id' => $user->id, 'password' => Hash::make('Password1!')]);
        PasswordHistory::create(['user_id' => $user->id, 'password' => Hash::make('OldPass2!')]);
        PasswordHistory::create(['user_id' => $user->id, 'password' => Hash::make('OldPass3!')]);

        $response = $this->actingAs($user)->post('/change-password', [
            'current_password' => 'Password1!',
            'password' => 'OldPass2!',
            'password_confirmation' => 'OldPass2!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_changement_mdp_accepte_nouveau_mot_de_passe_valide(): void
    {
        $user = User::create([
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => Hash::make('Password1!'), 'role_id' => 2,
        ]);

        PasswordHistory::create(['user_id' => $user->id, 'password' => Hash::make('Password1!')]);

        $response = $this->actingAs($user)->post('/change-password', [
            'current_password' => 'Password1!',
            'password' => 'ToutNouveau9!',
            'password_confirmation' => 'ToutNouveau9!',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profil');
    }

    public function test_inscription_cree_un_usager_par_defaut(): void
    {
        $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password1!', 'password_confirmation' => 'Password1!',
        ]);

        $user = User::where('email', 'test@test.com')->first();
        $this->assertEquals(2, $user->role_id);
    }

    public function test_inscription_sauvegarde_le_mot_de_passe_dans_lhistorique(): void
    {
        $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password1!', 'password_confirmation' => 'Password1!',
        ]);

        $user = User::where('email', 'test@test.com')->first();
        $this->assertEquals(1, $user->passwordHistories()->count());
    }
}