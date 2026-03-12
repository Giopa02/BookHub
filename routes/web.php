<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ExamplarController;


//User
Route::get('/subscription',[UserController::class, 'subscription']);
Route::get('/connect',[UserController::class, 'connect']);
Route::get('profil',[UserController::class, 'personnalProfil']);


//Recherche 
Route::get('/search/{params}',[SearchController::class, 'search']);
Route::get('/examplar{id}',[ExamplarController::class, 'examplar']);


//Emprunt
Route::get('/borrowing',[BorrowController::class, 'borrowing']); //list
Route::get('/borrowing/{id}',[BorrowController::class, 'borrow']); //adding an examplary
Route::get('/return/{id}',[BorrowController::class, 'return']); //return borrow


//BO
Route::get('/bo/profils',[UserController::class, 'profils']);
Route::get('/bo/profil/{id}',[UserController::class, 'profil']);

Route::get('/bo/copies',[ExamplarController::class, 'copies']);
Route::get('/bo/examplar/{id}',[ExamplarController::class, 'exemplar']);
Route::get('/bo/examplar/add',[ExamplarController::class, 'add']);
Route::get('/bo/examplar/update/{id}',[ExamplarController::class, 'update']);
Route::get('/bo/examplar/delete/{id}',[ExamplarController::class, 'delete']);


// Route::get('/connection', function () {
//     return 'test';
// });
