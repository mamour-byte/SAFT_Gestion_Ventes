<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use App\Http\Controllers\ChartController;
use App\Models\Ventes;
use App\Orchid\Layouts\Charts\ProductChart;
use App\Orchid\Layouts\Charts\VenteChart;
use App\Orchid\Layouts\Charts\ClientChart;
use App\Orchid\Layouts\TabsNav\DevisTable;
use App\Orchid\Layouts\TabsNav\FactureTable;
use App\Orchid\Layouts\TabsNav\AvoirTable;

class PlatformScreen extends Screen
{


    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
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
                    'type_document' => $vente->facture->type_document ?? 'facture',
                    'numero_facture' => $vente->facture->numero_facture ?? null,
                    'date_livraison' => $vente->date_livraison ?? null,
                ];
            });


            $ventesParProduit = app(ChartController::class)->ventesParProduit();
            $ventesMensuelles = app(ChartController::class)->ventesParJourDuMois();
            $venteParClient = app(ChartController::class)->ventesParClient();
            
            return [
                'chartData' => [
                    [
                        'labels' => $ventesParProduit->pluck('nom')->toArray(),
                        'values' => $ventesParProduit->pluck('total_ventes')->toArray(),
                    ],
                ],
                'monthlySalesData' => [
                    [
                        'labels' => $ventesMensuelles->pluck('mois')->toArray(),
                        'values' => $ventesMensuelles->pluck('total_ventes')->toArray(),
                    ],
                ],
                'ClientData' => [
                    [
                        'labels' => $venteParClient->pluck('client')->toArray(),
                        'values' => $venteParClient->pluck('total_ventes')->toArray(),
                    ],
                ],
            ];
        }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Acceuil';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Bienvenu sur votre application SAFT';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::columns([
                ProductChart::class,
                VenteChart::class,
            ]),
            ClientChart::class,
            Layout::tabs([
                'Devis' => DevisTable::class,
                'Factures' => FactureTable::class,
                'Avoirs' => AvoirTable::class,
            ]),
            

        ];
    }
}
