<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 2, // usager par défaut
        ]);

        Auth::login($user);

        return redirect('/');
    }

    // Afficher le formulaire de connexion
    public function connect()
    {
        return view('auth.connect');
    }

    // Traiter la connexion
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'Identifiants incorrects.',
        ]);
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

    // BO : liste de tous les usagers
    public function profils()
    {

        if (Auth::user()->role_id !== 1) {
            abort(403);
        }

        $users = User::with('role', 'borrows')->get();

        return view('bo.profils', compact('users'));
    }

    // BO : profil détaillé d'un usager
    public function profil($id)
    {

        if (Auth::user()->role_id !== 1) {
        abort(403);
        }
        
        $user = User::with('role', 'borrows.copies.book')->findOrFail($id);

        return view('bo.profil', compact('user'));
    }
}