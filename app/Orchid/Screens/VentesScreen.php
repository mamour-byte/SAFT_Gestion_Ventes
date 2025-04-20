<?php

namespace App\Orchid\Screens;

use App\Http\Controllers\VenteController;
use App\Orchid\Layouts\TabsNav\NouvVentesRow;
use App\Orchid\Layouts\TabsNav\HistVentesRow;
use App\Orchid\Layouts\TabsNav\DevisTable;
use App\Orchid\Layouts\TabsNav\FactureTable;
use App\Orchid\Layouts\TabsNav\AvoirTable;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use App\Models\Ventes;

use Orchid\Support\Facades\Toast;
use PDF;
use Orchid\Screen\Actions\Button;

class VentesScreen extends Screen
{
    private const DOCUMENT_TYPES = [
        'devis' => 'devis',
        'facture' => 'facture',
        'avoir' => 'avoir'
    ];

    public function name(): string
        {
            return 'Gestion des Ventes';
        }

    

    public function query(): array
        {
            $ventes = Ventes::with(['client', 'details.product', 'facture'])
                ->latest()
                ->get();

            $formatted = $ventes->map(function ($vente) {
                return [
                    'id' => $vente->id, 
                    'document_id' => $vente->facture->id ?? null, 
                    'id_client' => $vente->id_client,
                    'client_nom' => $vente->client->nom ?? 'Client inconnu',
                    'produits' => $vente->details->map(function ($detail) {
                        return [
                            'nom' => $detail->product->nom ?? 'Produit inconnu',
                            'quantite' => $detail->quantite_vendue ?? 0,
                            'prix_unitaire' => $detail->product->prix_unitaire ?? 0,
                        ];
                    })->values()->all(),
                    'type_document' => $vente->facture->type_document ?? self::DOCUMENT_TYPES['facture'],
                    'numero_facture' => $vente->facture->numero_facture ?? null,
                    'date_livraison' => $vente->date_livraison ?? null,
                ];
            });

            return [
                'ventes' => $formatted,
                'devis' => $formatted->filter(fn($v) => $v['type_document'] === self::DOCUMENT_TYPES['devis']),
                'factures' => $formatted->filter(fn($v) => $v['type_document'] === self::DOCUMENT_TYPES['facture']),
                'avoirs' => $formatted->filter(fn($v) => $v['type_document'] === self::DOCUMENT_TYPES['avoir']),
            ];
        }


    public function layout(): array
    {
        return [
            Layout::tabs([
                'Nouvelle Vente' => [
                    NouvVentesRow::class,
                ],
                'Historique' => [
                    HistVentesRow::class,
                ],
                'Devis' => [
                    DevisTable::class,
                ],
                'Factures' => [
                    FactureTable::class,
                ],
                'Avoirs' => [
                    AvoirTable::class,
                ],
            ]),
        ];
    }

    public function addToVentesTable(Request $request)
    {
        return (new VenteController)->addToVentesTable($request);
    }

    public function removeFromVentesTable(Request $request)
    {
        return (new VenteController)->removeFromVentesTable($request);
    }
}