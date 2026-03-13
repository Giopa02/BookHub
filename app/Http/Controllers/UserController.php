<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\PasswordHistory;

class UserController extends Controller
{
    // Afficher le formulaire d'inscription
    public function subscription()
    {
        return view('auth.subscription');
    }

    // Traiter l'inscription
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*?&#]).+$/', // au moins une majuscule, un chiffre et caractère spécial
            ],
        ], [
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.regex' => 'Le mot de passe doit contenir au moins une majuscule, un chiffre et un caractère spécial (@$!%*?&#).',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 2,
            'password_changed_at' => now(),
        ]);

        // Sauvegarder le premier mot de passe dans l'historique
        PasswordHistory::create([
            'user_id' => $user->id,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect('/');
    }

    // Afficher le formulaire de connexion
    public function connect()
    {
        return view('auth.connect');
    }

    // Traiter la connexion (étape 1 : vérifier email/mdp puis envoyer code 2FA)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            // Générer et envoyer le code 2FA
            $code = $user->generateTwoFactorCode();

            // Envoyer le code par email
            Mail::raw("Votre code de vérification BookHub : {$code}\n\nCe code expire dans 10 minutes.", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('BookHub - Code de vérification');
            });

            // Déconnecter temporairement l'utilisateur
            Auth::logout();

            // Stocker l'ID en session pour l'étape 2
            $request->session()->put('2fa_user_id', $user->id);

            return redirect('/verify-2fa');
        }

        return back()->withErrors([
            'email' => 'Identifiants incorrects.',
        ]);
    }

    // Afficher le formulaire 2FA
    public function showTwoFactorForm()
    {
        if (!session('2fa_user_id')) {
            return redirect('/connect');
        }

        return view('auth.two_factor');
    }

    // Vérifier le code 2FA (étape 2)
    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = session('2fa_user_id');
        if (!$userId) {
            return redirect('/connect');
        }

        $user = User::findOrFail($userId);

        // Vérifier le code et l'expiration
        if ($user->two_factor_code !== $request->code) {
            return back()->withErrors(['code' => 'Code incorrect.']);
        }

        if ($user->two_factor_expires_at && $user->two_factor_expires_at->isPast()) {
            $user->resetTwoFactorCode();
            return back()->withErrors(['code' => 'Le code a expiré. Veuillez vous reconnecter.']);
        }

        // Code valide : connecter l'utilisateur
        $user->resetTwoFactorCode();
        Auth::login($user);

        $request->session()->forget('2fa_user_id');
        $request->session()->regenerate();

        return redirect('/');
    }

    // Renvoyer le code 2FA
    public function resendTwoFactorCode()
    {
        $userId = session('2fa_user_id');
        if (!$userId) {
            return redirect('/connect');
        }

        $user = User::findOrFail($userId);
        $code = $user->generateTwoFactorCode();

        Mail::raw("Votre nouveau code de vérification BookHub : {$code}\n\nCe code expire dans 10 minutes.", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('BookHub - Nouveau code de vérification');
        });

        return back()->with('success', 'Un nouveau code a été envoyé à votre adresse email.');
    }

    // Déconnexion
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // Profil personnel de l'usager connecté
    public function personnalProfil()
    {
        $user = Auth::user();
        $user->load('role', 'borrows.copies.book');

        $currentBorrow = $user->borrows->whereNull('return_date')->first();
        $borrowHistory = $user->borrows->whereNotNull('return_date');

        return view('profil', compact('user', 'currentBorrow', 'borrowHistory'));
    }

    // Afficher le formulaire de changement de mot de passe
    public function showChangePassword()
    {
        $user = Auth::user();

        return view('auth.change_password', compact('user'));
    }

    // Traiter le changement de mot de passe
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*?&#]).+$/',
            ],
        ], [
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.regex' => 'Le mot de passe doit contenir au moins une majuscule, un chiffre et un caractère spécial (@$!%*?&#).',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $user = Auth::user();

        // Vérifier le mot de passe actuel
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        // Vérifier que le nouveau mdp n'est pas dans les 5 derniers
        $lastPasswords = PasswordHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        foreach ($lastPasswords as $old) {
            if (Hash::check($request->password, $old->password)) {
                return back()->withErrors(['password' => 'Vous ne pouvez pas réutiliser un de vos 5 derniers mots de passe.']);
            }
        }

        // Mettre à jour le mot de passe
        $user->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
        ]);

        // Sauvegarder dans l'historique
        PasswordHistory::create([
            'user_id' => $user->id,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/profil')->with('success', 'Mot de passe modifié avec succès.');
    }

    // BO : liste de tous les usagers
    public function profils()
    {
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            abort(403);
        }

        $users = User::with('role', 'borrows')->paginate(15);

        return view('bo.profils', compact('users'));
    }

    // BO : profil détaillé d'un usager
    public function profil($id)
    {
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            abort(403);
        }

        $user = User::with('role', 'borrows.copies.book')->findOrFail($id);

        return view('bo.profil', compact('user'));
    }
}