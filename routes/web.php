<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\pdfController;


Route::get('/', function () {
    return view('welcome');
});


Route::post('/ventes/addToVentesTable', [VenteController::class, 'addToVentesTable'])
    ->name('ventes.addToVentesTable');


Route::get('/documents/{type}/{id}', [pdfController::class, 'show'])
    ->name('documents.show');