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
            $quantity = $item->quantite_vendue ?? 0; 
            $price = $item->product->prix_unitaire ?? 0;

            return [
                'nom' => $item->product->nom ?? 'Produit inconnu',
                'quantity' => $quantity,
                'prix_unitaire' => $price,
                'total_ligne' => $quantity * $price
            ];
        });

        $subtotal = $vente->details->sum(function ($item) {
            return $item->quantite_vendue * ($item->product->prix_unitaire ?? 0);
        });

        $taxRate = 18;
        $factureTvaIncluse = $vente->facture->tva ?? false; 

        $taxAmount = $factureTvaIncluse ? $subtotal * ($taxRate / 100) : 0;
        $totalAmount = $subtotal + $taxAmount;

        $tva_status = $factureTvaIncluse ? 'TVA incluse' : 'TVA non incluse';

        $pdfData = [
            'numero_facture' => $vente->facture->numero_facture ?? '-',
            'date_facture' => $vente->facture->date_facture ?? now()->format('Y-m-d'),
            'date_echeance' => $vente->facture->date_echeance ?? now()->addDays(30)->format('Y-m-d'),

            'client_nom' => $vente->client->nom ?? '',
            'client_prenom' => $vente->client->prenom ?? '',
            'client_adresse' => $vente->client->adresse ?? '',
            'client_telephone' => $vente->client->telephone ?? '',
            'client_email' => $vente->client->email ?? '',
            'client_NumeroNinea' => $vente->client->NumeroNinea ?? '',
            'Client_NumeroRC' => $vente->client->NumeroRegistreCommerce ?? '',

            'produits' => $produitsArray,
            'subtotal' => $subtotal,
            'taxRate' => $taxRate,
            'taxAmount' => $taxAmount,
            'totalAmount' => $totalAmount,
            'tva_status' => $tva_status,

            'type_document' => ucfirst($vente->facture->type_document ?? '-'), // ← ICI ajouté
        ];

        $pdf = PDF::loadView('pdf.facturepdf', $pdfData);
        return $pdf->stream('Facture ' . $vente->client->nom . ' ' . now()->translatedFormat('F Y') . '.pdf');

    }
}
