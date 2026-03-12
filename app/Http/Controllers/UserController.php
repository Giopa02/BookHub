<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function subscription()
    {
        return "subscription";
        // ('user.profile’); // le controleur retourne une vue, nous verrons
        // par la suite comment créer une vue
    }
    
    public function connect()
    {
        return "connect";
    }  

    public function profils()
    {
        return "profils";
    }  

    public function profil($id)
    {
        return "profil id: " . $id;
    }  

    public function personnalProfil()
    {
        return "PersonnalProfil";
    }  
}
