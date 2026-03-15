<?php

// Ce fichier teste toutes les validations (règles de saisie) de l'application.
// Il vérifie que :
//   - Les formulaires rejettent correctement les données invalides
//   - Les formulaires acceptent les données valides
//   - Les règles de sécurité du mot de passe sont bien appliquées
//   - La règle de non-réutilisation des mots de passe fonctionne
//
// Ces tests sont essentiels pour la sécurité de l'application.

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

    // Préparation des données de base
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
    // assertSessionHasErrors('password') = vérifie qu'une erreur sur "password" a été générée
    // -----------------------------------------------------------------------

    public function test_inscription_refuse_mot_de_passe_trop_court(): void
    {
        // On tente de s'inscrire avec un mot de passe de seulement 4 caractères
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Ab1!', 'password_confirmation' => 'Ab1!',
        ]);

        // On s'attend à une erreur sur le champ "password"
        $response->assertSessionHasErrors('password');
    }

    public function test_inscription_refuse_mot_de_passe_sans_majuscule(): void
    {
        // Mot de passe sans lettre majuscule
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'password1!', 'password_confirmation' => 'password1!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_inscription_refuse_mot_de_passe_sans_chiffre(): void
    {
        // Mot de passe sans chiffre
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password!!', 'password_confirmation' => 'Password!!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_inscription_refuse_mot_de_passe_sans_caractere_special(): void
    {
        // Mot de passe sans caractère spécial
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password12', 'password_confirmation' => 'Password12',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_inscription_accepte_mot_de_passe_valide(): void
    {
        // Mot de passe valide : 8+ caractères, majuscule, chiffre, caractère spécial
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password1!', 'password_confirmation' => 'Password1!',
        ]);

        // assertSessionHasNoErrors = aucune erreur ne doit être présente
        $response->assertSessionHasNoErrors();

        // assertDatabaseHas = vérifie qu'un enregistrement existe en base
        $this->assertDatabaseHas('users', ['email' => 'test@test.com']);
    }

    public function test_inscription_refuse_mot_de_passe_non_confirme(): void
    {
        // Les deux champs de mot de passe ne correspondent pas
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password1!', 'password_confirmation' => 'Different1!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_inscription_refuse_email_deja_utilise(): void
    {
        // On crée d'abord un utilisateur avec cet email
        User::create([
            'name' => 'Existant', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => bcrypt('Password1!'), 'role_id' => 2,
        ]);

        // On tente de s'inscrire avec le même email → doit être refusé
        $response = $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password1!', 'password_confirmation' => 'Password1!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_inscription_refuse_champs_vides(): void
    {
        // On soumet un formulaire vide
        $response = $this->post('/subscription', []);

        // On s'attend à des erreurs sur tous les champs obligatoires
        $response->assertSessionHasErrors(['name', 'prenom', 'email', 'password']);
    }

    // -----------------------------------------------------------------------
    // Tests de validation de la CONNEXION
    // -----------------------------------------------------------------------

    public function test_connexion_refuse_identifiants_incorrects(): void
    {
        // On crée un utilisateur avec un mot de passe connu
        User::create([
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => bcrypt('Password1!'), 'role_id' => 2,
        ]);

        // On tente de se connecter avec un mauvais mot de passe
        $response = $this->post('/connect', [
            'email'    => 'test@test.com',
            'password' => 'MauvaisMdp1!', // Mot de passe incorrect
        ]);

        // L'erreur doit être associée au champ "email"
        $response->assertSessionHasErrors('email');
    }

    // -----------------------------------------------------------------------
    // Tests de validation du CHANGEMENT DE MOT DE PASSE
    // -----------------------------------------------------------------------

    public function test_changement_mdp_refuse_ancien_mot_de_passe_incorrect(): void
    {
        $user = User::create([
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => bcrypt('Password1!'), 'role_id' => 2,
        ]);

        // actingAs($user) = simuler une connexion en tant que cet utilisateur
        $response = $this->actingAs($user)->post('/change-password', [
            'current_password'      => 'MauvaisMdp1!', // Mauvais mot de passe actuel
            'password'              => 'NewPassword1!',
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

        // On simule un historique de mots de passe
        PasswordHistory::create(['user_id' => $user->id, 'password' => Hash::make('Password1!')]);
        PasswordHistory::create(['user_id' => $user->id, 'password' => Hash::make('OldPass2!')]);
        PasswordHistory::create(['user_id' => $user->id, 'password' => Hash::make('OldPass3!')]);

        // On tente de réutiliser un mot de passe déjà présent dans l'historique
        $response = $this->actingAs($user)->post('/change-password', [
            'current_password'      => 'Password1!',
            'password'              => 'OldPass2!', // Ce mot de passe est dans l'historique
            'password_confirmation' => 'OldPass2!',
        ]);

        $response->assertSessionHasErrors('password'); // Doit être refusé
    }

    public function test_changement_mdp_accepte_nouveau_mot_de_passe_valide(): void
    {
        $user = User::create([
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => Hash::make('Password1!'), 'role_id' => 2,
        ]);

        PasswordHistory::create(['user_id' => $user->id, 'password' => Hash::make('Password1!')]);

        // On change pour un nouveau mot de passe valide qui n'est pas dans l'historique
        $response = $this->actingAs($user)->post('/change-password', [
            'current_password'      => 'Password1!',
            'password'              => 'ToutNouveau9!', // Nouveau mot de passe
            'password_confirmation' => 'ToutNouveau9!',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profil'); // Redirigé vers le profil après succès
    }

    // -----------------------------------------------------------------------
    // Tests sur les règles métier de l'inscription
    // -----------------------------------------------------------------------

    public function test_inscription_cree_un_usager_par_defaut(): void
    {
        // Quand quelqu'un s'inscrit, il doit avoir le rôle "usager" (role_id = 2)
        $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password1!', 'password_confirmation' => 'Password1!',
        ]);

        $user = User::where('email', 'test@test.com')->first();
        $this->assertEquals(2, $user->role_id); // Doit être 2 = usager
    }

    public function test_inscription_sauvegarde_le_mot_de_passe_dans_lhistorique(): void
    {
        // Après inscription, le mot de passe doit être sauvegardé dans l'historique
        // pour les vérifications futures de non-réutilisation
        $this->post('/subscription', [
            'name' => 'Test', 'prenom' => 'User', 'email' => 'test@test.com',
            'password' => 'Password1!', 'password_confirmation' => 'Password1!',
        ]);

        $user = User::where('email', 'test@test.com')->first();

        // L'historique doit contenir exactement 1 entrée (le premier mot de passe)
        $this->assertEquals(1, $user->passwordHistories()->count());
    }
}
