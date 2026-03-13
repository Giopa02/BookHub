<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CopyController;

Route::get('/', function (){
    return view('index');
});


//User
Route::get('/subscription', [UserController::class, 'subscription']);
Route::post('/subscription', [UserController::class, 'register']);
Route::get('/connect', [UserController::class, 'connect']);
Route::post('/connect', [UserController::class, 'login']);
Route::get('/profil', [UserController::class, 'personnalProfil']);


//Recherche
Route::get('/search/{params}', [SearchController::class, 'search']);
Route::get('/exemplar/{id}', [CopyController::class, 'exemplar']);


//Emprunt
Route::get('/borrowing', [BorrowController::class, 'borrowing']);
Route::post('/borrowing/{id}', [BorrowController::class, 'borrow']);
Route::patch('/return/{id}', [BorrowController::class, 'return']);


//BO
Route::get('/bo/profils', [UserController::class, 'profils']);
Route::get('/bo/profil/{id}', [UserController::class, 'profil']);

Route::get('/bo/copies', [CopyController::class, 'copies']);
Route::get('/bo/exemplar/add', [CopyController::class, 'add']);
Route::post('/bo/exemplar/add', [CopyController::class, 'store']);
Route::get('/bo/exemplar/update/{id}', [CopyController::class, 'edit']);
Route::put('/bo/exemplar/update/{id}', [CopyController::class, 'update']);
Route::delete('/bo/exemplar/delete/{id}', [CopyController::class, 'delete']);
Route::get('/bo/exemplar/{id}', [CopyController::class, 'exemplar']);