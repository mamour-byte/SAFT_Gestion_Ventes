<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class pdfController extends Controller
{
    public function downloadPDF(Request $request)
        {
            $index = $request->get('index');

            $venteData = session('ventes')[$index] ?? null;

            if (!$venteData) {
                Toast::error('Vente introuvable.');
                return redirect()->back();
            }

            $pdf = PDF::loadView('pdf.vente', ['vente' => $venteData]);

            return response()->streamDownload(
                fn () => print($pdf->output()),
                'document_vente_' . now()->format('Ymd_His') . '.pdf'
            );
        }

        
}
