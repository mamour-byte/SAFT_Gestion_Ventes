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
            'date_facture' => optional($vente->facture->date_facture)->format('d/m/Y') ?? '-',
            'date_echeance' => optional($vente->facture->date_echeance)->format('d/m/Y') ?? '-',

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
            'tva_status' => $tva_status,

            'type_document' => ucfirst($vente->facture->type_document ?? '-'), // ← ICI ajouté
        ];

        $pdf = PDF::loadView('pdf.facturepdf', $pdfData);
        return $pdf->stream('Facture ' . $vente->client->nom . ' ' . now()->translatedFormat('F Y') . '.pdf');

    }
}
