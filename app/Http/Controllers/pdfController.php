<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class pdfController extends Controller
{
    public function show(string $type, string $id)
    {
        abort_unless(in_array($type, ['facture', 'devis', 'avoir']), 404);
        
        $model = match($type) {
            'facture' => Facture::findOrFail($id),
            'devis' => Devis::findOrFail($id),
            'avoir' => Avoir::findOrFail($id),
        };

        $pdf = PDF::loadView("pdf.{$type}", ['document' => $model]);
        
        return $pdf->stream("{$type}-{$model->numero}.pdf");
    }

        
}
