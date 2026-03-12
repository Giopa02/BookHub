<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExamplarController extends Controller
{
    public function copies()
    {
        return "bo/copies";
    }  

    public function exemplar($id)
    {
        return "exemplar id:" . $id;
    } 
    public function add()
    {
        return "add";
    } 

    public function update($id)
    {
        return "update id" . $id;
    } 

    public function delete($id)
    {
        return "delete id" . $id;
    } 
}
