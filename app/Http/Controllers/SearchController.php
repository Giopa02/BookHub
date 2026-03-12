<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search($params)
    {
        return "search" . $params;
    }  
}
