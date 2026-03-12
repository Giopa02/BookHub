<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BorrowController extends Controller
{
    public function borrowing()
    {
        return "borrowing";
    }  

    public function borrow($id)
    {
        return "borrow, exemplarid" . $id;
    } 

    public function return($id)
    {
        return "return Borrow id: " . $id;
    } 
}
