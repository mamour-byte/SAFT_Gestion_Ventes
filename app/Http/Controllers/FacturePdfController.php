<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ventes;  
use PDF;
use App\Models\Clients;

class FacturePdfController extends Controller
{
    public function generate($id)
    {
        $vente = Ventes::with(['client', 'details.product', 'facture'])->findOrFail($id);

        $produitsArray = $vente->details->map(function ($item) {
            return [
                'nom' => $item->product->nom ?? 'Produit inconnu',
                'quantity' => $item->quantity,
                'prix_unitaire' => $item->product->price,
                'total_ligne' => $item->quantity * $item->product->price
            ];
        });

        $subtotal = $vente->details->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        $taxRate = 20;
        $taxAmount = $subtotal * ($taxRate / 100);
        $totalAmount = $subtotal + $taxAmount;

        $pdfData = [
            'numero_facture' => $vente->facture->numero_facture ?? 'NON-RENSEIGNÉ',
            'date_facture' => optional($vente->facture->date_facture)->format('d/m/Y') ?? 'NON-RENSEIGNÉ',
            'date_echeance' => optional($vente->facture->date_echeance)->format('d/m/Y') ?? 'NON-RENSEIGNÉ',

            'client_nom' => $vente->client->nom ?? '',
            'client_prenom' => $vente->client->prenom ?? '',
            'client_adresse' => $vente->client->adresse ?? '',
            'client_telephone' => $vente->client->telephone ?? '',
            'client_email' => $vente->client->email ?? '',
            'client_siret' => $vente->client->siret ?? '',

            'produits' => $produitsArray,
            'subtotal' => $subtotal,
            'taxRate' => $taxRate,
            'taxAmount' => $taxAmount,
            'totalAmount' => $totalAmount,
        ];

        $pdf = PDF::loadView('pdf.facturepdf', $pdfData);
        return $pdf->stream('facturepdf.pdf'); 
    }
}
