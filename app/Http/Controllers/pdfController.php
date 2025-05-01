<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Devis;
use App\Models\Avoir;
use PDF;

class pdfController extends Controller
{
    public function downloadPDF($type, $id)
        {
            $document = null;

            if ($type === 'facture') {
                $document = Facture::find($id);
            } elseif ($type === 'devis') {
                $document = Devis::find($id);
            } elseif ($type === 'avoir') {
                $document = Avoir::find($id);
            }

            if (!$document) {
                Toast::error('Document non trouvé.');
                return back();
            }

            $pdf = PDF::loadView('pdf.document', compact('document'));
            return $pdf->download("{$type}_{$id}.pdf");
}
}
