<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\pdfController;
use App\Http\Controllers\FacturePdfController;




Route::post('/ventes/addToVentesTable', [VenteController::class, 'addToVentesTable'])
    ->name('ventes.addToVentesTable');

Route::get('/facture/pdf/{id}', [FacturePdfController::class, 'generate'])->name('preview-pdf.pdf');

Route::post('/ventes/update', [VenteController::class, 'transformQuoteToInvoice'])
    ->name('ventes.transformQuoteToInvoice');

Route::post('/ventes/supprimer/{id}', [VenteController::class, 'destroy'])
    ->name('ventes.supprimer');

