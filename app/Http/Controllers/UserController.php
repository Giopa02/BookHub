<?php

// Ce Controller gère tout ce qui concerne les utilisateurs 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  // connexion/déconnexion de l'utilisateur
use Illuminate\Support\Facades\Hash;  // chiffrer (hacher) les mots de passe
use Illuminate\Support\Facades\Mail; // envoyer des emails
use App\Models\User;                  //  modèle représentant un utilisateur
use App\Models\PasswordHistory;       //  modèle qui stocke l'historique des mots de passe

class UserController extends Controller
{
    // -----------------------------------------------------------------------
    // Affiche la page d'inscription
    // URL : GET /subscription
    // -----------------------------------------------------------------------
    public function subscription()
    {
        // affiche formulaire d'inscription
        return view('auth.subscription');
    }

    // -----------------------------------------------------------------------
    // Traite le formulaire d'inscription. URL : POST /subscription
    // -----------------------------------------------------------------------
    public function register(Request $request)
    {
        // On valide les données envoyées par le formulaire. Si une règle échoue, Laravel renvoie automatiquement l'utilisateur en arrière avec les erreurs
        $request->validate([
            'name'   => 'required|string|max:255',  // Nom obligatoire, texte, max 255 caractères
            'prenom' => 'required|string|max:255',  // Prénom obligatoire
            'email'  => 'required|email|unique:users,email', // Email valide et non encore utilisé
            'password' => [
                'required',
                'string',
                'min:12',       // au moins 12 caractères
                'confirmed',   // Le champ "confirmation" doit correspondre
                'regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*?&#]).+$/', // au moins une majuscule, un chiffre et caractère spécial
            ],
        ], [
            // messages d'erreur personnalisés en français
            'password.min'       => 'Le mot de passe doit contenir au moins 12 caractères.',
            'password.regex'     => 'Le mot de passe doit contenir au moins une majuscule, un chiffre et un caractère spécial (@$!%*?&#).',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        // On crée le nouvel utilisateur en base de données
        // Hash::make() chiffre le mot de passe
        $user = User::create([
            'name'               => $request->name,
            'prenom'             => $request->prenom,
            'email'              => $request->email,
            'password'           => Hash::make($request->password), // mdp chiffré
            'role_id'            => 2,    // 2 = rôle "usager" (par défaut, pas bibliothécaire)
            'password_changed_at'=> now(), // date du premier mot de passe
        ]);

        // auvegarde le premier mot de passe dans l'historique
        // permet plus tard de vérifier qu'on ne réutilise pas un ancien mot de passe
        PasswordHistory::create([
            'user_id'  => $user->id,
            'password' => Hash::make($request->password),
        ]);

        // connecte automatiquement l'utilisateur après l'inscription
        Auth::login($user);

        // redirige vers la page d'accueil
        return redirect('/');
    }

    // -----------------------------------------------------------------------
    // la page de connexion
    // URL : GET /connect
    // -----------------------------------------------------------------------
    public function connect()
    {
        return view('auth.connect');
    }

    // -----------------------------------------------------------------------
    // Traite le formulaire de connexion (étape 1 sur 2)
    // Vérifie email + mot de passe, puis envoie un code 2FA par email
    // URL : POST /connect
    // -----------------------------------------------------------------------
    public function login(Request $request)
    {
        // validation basique : email et mot de passe obligatoires
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Auth::attempt() vérifie si l'email et le mot de passe correspondent
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            // générer et envoyer le code 2FA
            // un code aléatoires est créé et stocké en base
            $code = $user->generateTwoFactorCode();

            // envoyer le code par email à l'utilisateur
            Mail::raw("Votre code de vérification BookHub : {$code}\n\nCe code expire dans 10 minutes.", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('BookHub - Code de vérification');
            });

            // déconnecte temporairement l'utilisateur si n'a pas encore validé le code 2FA
            Auth::logout();

            // mémorise l'identifiant de l'utilisateur en session
            $request->session()->put('2fa_user_id', $user->id);

            // redirige vers la page de saisie du code 2FA
            return redirect('/verify-2fa');
        }

        // Si identifiants sont incorrects, retour en arrière avec un message d'erreur
        return back()->withErrors([
            'email' => 'Identifiants incorrects.',
        ]);
    }

    // -----------------------------------------------------------------------
    // Affiche le formulaire de saisie du code 2FA (étape 2 de la connexion)
    // URL : GET /verify-2fa
    // -----------------------------------------------------------------------
    public function showTwoFactorForm()
    {
        // Si la session ne contient pas d'identifiant utilisateur en attente,
        // l'utilisateur n'a pas passé l'étape 1 → on le redirige vers la connexion
        if (!session('2fa_user_id')) {
            return redirect('/connect');
        }

        return view('auth.two_factor');
    }

    // -----------------------------------------------------------------------
    // Vérifie le code 2FA saisi par l'utilisateur (étape 2 de la connexion)
    // URL : POST /verify-2fa
    // -----------------------------------------------------------------------
    public function verifyTwoFactor(Request $request)
    {
        // Le code doit faire exactement 6 caractères
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        // récupère l'identifiant mémorisé en session (étape 1)
        $userId = session('2fa_user_id');
        if (!$userId) {
            return redirect('/connect');
        }

        $user = User::findOrFail($userId);

        // Vérifier le code et l'expiration
        // compare le code saisi avec celui stocké en base de données
        if ($user->two_factor_code !== $request->code) {
            return back()->withErrors(['code' => 'Code incorrect.']);
        }

        // vérifie que le code n'a pas expiré (il expire après 10 minutes)
        if ($user->two_factor_expires_at && $user->two_factor_expires_at->isPast()) {
            $user->resetTwoFactorCode(); // efface le code périmé
            return back()->withErrors(['code' => 'Le code a expiré. Veuillez vous reconnecter.']);
        }

        // ode valide : on efface le code 2FA (il ne peut servir qu'une fois)
        $user->resetTwoFactorCode();

        // connecte officiellement l'utilisateur
        Auth::login($user);

        // nettoie la session et on régénère le token pour la sécurité (prévention CSRF)
        $request->session()->forget('2fa_user_id');
        $request->session()->regenerate();

        return redirect('/');
    }

    // -----------------------------------------------------------------------
    // Renvoie un nouveau code 2FA par email (si le premier a expiré)
    // URL : GET /resend-2fa
    // -----------------------------------------------------------------------
    public function resendTwoFactorCode()
    {
        // On vérifie qu'un utilisateur attend bien sa vérification 2FA
        $userId = session('2fa_user_id');
        if (!$userId) {
            return redirect('/connect');
        }

        $user = User::findOrFail($userId);

        // génère un nouveau code (l'ancien automatiquement remplacé)
        $code = $user->generateTwoFactorCode();

        // envoie le nouveau code par email
        Mail::raw("Votre nouveau code de vérification BookHub : {$code}\n\nCe code expire dans 10 minutes.", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('BookHub - Nouveau code de vérification');
        });

        return back()->with('success', 'Un nouveau code a été envoyé à votre adresse email.');
    }

    // -----------------------------------------------------------------------
    // Déconnecte l'utilisateur
    // URL : GET /logout
    // -----------------------------------------------------------------------
    public function logout(Request $request)
    {
        Auth::logout(); // déconnecte l'utilisateur

        // détruit la session (efface toutes les données temporaires du navigateur)
        $request->session()->invalidate();

        // régénère le token de sécurité CSRF pour éviter les attaques
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // -----------------------------------------------------------------------
    // Affiche la page de profil personnel de l'utilisateur connecté
    // URL : GET /profil
    // -----------------------------------------------------------------------
    public function personnalProfil()
    {
        $user = Auth::user();

        // charge les données liées : rôle, emprunts, exemplaires des emprunts, livres
        $user->load('role', 'borrows.copies.book');

        // sépare l'emprunt actuel (sans retour) de l'historique (avec retour)
        $currentBorrow  = $user->borrows->whereNull('return_date')->first();
        $borrowHistory  = $user->borrows->whereNotNull('return_date');

        return view('profil', compact('user', 'currentBorrow', 'borrowHistory'));
    }

    // -----------------------------------------------------------------------
    // Affiche le formulaire de changement de mot de passe
    // URL : GET /change-password
    // -----------------------------------------------------------------------
    public function showChangePassword()
    {
        $user = Auth::user();

        // passe l'utilisateur à la vue pour afficher la date du dernier changement
        return view('auth.change_password', compact('user'));
    }

    // -----------------------------------------------------------------------
    // Traite le changement de mot de passe avec vérifications de sécurité
    // URL : POST /change-password
    // -----------------------------------------------------------------------
    public function changePassword(Request $request)
    {
        // Validation : même règles de complexité que lors de l'inscription
        $request->validate([
            'current_password' => 'required|string',
            'password' => [
                'required',
                'string',
                'min:12',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*?&#]).+$/',
            ],
        ], [
            'password.min'       => 'Le mot de passe doit contenir au moins 12 caractères.',
            'password.regex'     => 'Le mot de passe doit contenir au moins une majuscule, un chiffre et un caractère spécial (@$!%*?&#).',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $user = Auth::user();

        // Hash::check() compare le mot de passe en clair avec celui chiffré en base
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        // Vérifier que le nouveau mot de passe n'est pas dans les 5 derniers : récupère les 5 derniers mots de passe stockés dans l'historique
        $lastPasswords = PasswordHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Pour chaque ancien mot de passe, on vérifie si le nouveau y correspond
        foreach ($lastPasswords as $old) {
            if (Hash::check($request->password, $old->password)) {
                return back()->withErrors(['password' => 'Vous ne pouvez pas réutiliser un de vos 5 derniers mots de passe.']);
            }
        }

        // Tout est valide : on met à jour le mot de passe en base (chiffré)
        $user->update([
            'password'            => Hash::make($request->password),
            'password_changed_at' => now(), // On note la date du changement
        ]);

        // sauvegarder dans l'historique pour les vérifications futures
        PasswordHistory::create([
            'user_id'  => $user->id,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/profil')->with('success', 'Mot de passe modifié avec succès.');
    }

    // -----------------------------------------------------------------------
    // [BACK-OFFICE] Affiche la liste de tous les usagers
    // URL : GET /bo/profils
    // Réservé aux bibliothécaires
    // -----------------------------------------------------------------------
    public function profils()
    {
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            abort(403); // Accès refusé
        }

        // récupère tous les utilisateurs avec leur rôle et leurs emprunts
        $users = User::with('role', 'borrows')->paginate(15);

        return view('bo.profils', compact('users'));
    }

    // -----------------------------------------------------------------------
    // [BACK-OFFICE] Affiche le profil détaillé d'un usager
    // URL : GET /bo/profil/{id}
    // -----------------------------------------------------------------------
    public function profil($id)
    {
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            abort(403);
        }

        // On récupère l'usager avec toutes ses données liées pour un profil complet
        $user = User::with('role', 'borrows.copies.book')->findOrFail($id);

        return view('bo.profil', compact('user'));
    }
}
