<?php

namespace App\Orchid\Screens;

use App\Http\Controllers\VenteController;
use App\Orchid\Layouts\TabsNav\NouvVentesRow;
use App\Orchid\Layouts\TabsNav\HistVentesRow;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use App\Models\Ventes;

class VentesScreen extends Screen
{
    public function query()
        {
            $ventes = \App\Models\Ventes::with(['client', 'details.product', 'facture'])->latest()->get();

            $formatted = $ventes->map(function ($vente) {
                return [
                    'id_client' => $vente->id_client,
                    'produits' => $vente->details->map(function ($detail) {
                        return [
                            'nom' => $detail->product->nom ?? 'Produit inconnu',
                            'quantite' => $detail->quantite_vendue,
                            'prix_unitaire' => $detail->product->prix_unitaire ?? 0,
                        ];
                    }),
                    'type_document' => $vente->facture->type_document ?? 'facture',
                    'numero_facture' => $vente->facture->numero_facture ?? null,
                    'date_livraison' => $vente->date_livraison ?? null,
                ];
            });

            return [
                'ventes' => $formatted,
            ];
        }


    public function name(): string
    {
        return 'Gestion des Ventes';
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
            ]),
        ];
    }


    public function addToVentesTable(Request $request)
    {
        // dd('Orchid reçoit bien la requête !', $request->all());2
        return (new VenteController)->addToVentesTable($request);
    }

    public function removeFromVentesTable(Request $request)
    {
        return (new VenteController)->removeFromVentesTable($request);
    }

    public function saveVentes(Request $request)
    {
        return (new VenteController)->saveVentes($request);
    }
}