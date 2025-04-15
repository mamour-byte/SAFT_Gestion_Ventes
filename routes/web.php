<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VenteController;

Route::get('/', function () {
    return view('welcome');
});


Route::post('/ventes/addToVentesTable', [VenteController::class, 'addToVentesTable'])
    ->name('ventes.addToVentesTable');
