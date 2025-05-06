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
    private const DOCUMENT_TYPES = [
        'devis' => 'devis',
        'facture' => 'facture',
        'avoir' => 'avoir'
    ];

        private function formatVentes($ventes)
        {
            return $ventes->map(function ($vente) {
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
        }

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): array
        {
            $factures = Ventes::with(['client', 'details.product', 'facture'])
            ->whereHas('facture', fn($q) => $q->where('type_document', self::DOCUMENT_TYPES['facture']))
            ->latest()
            ->take(5)
            ->get();
    
            $devis = Ventes::with(['client', 'details.product', 'facture'])
            ->whereHas('facture', fn($q) => $q->where('type_document', self::DOCUMENT_TYPES['devis']))
            ->latest()
            ->take(5)
            ->get();
    
            $avoirs = Ventes::with(['client', 'details.product', 'facture'])
            ->whereHas('facture', fn($q) => $q->where('type_document', self::DOCUMENT_TYPES['avoir']))
            ->latest()
            ->take(5)
            ->get();

            // $formatted = $this->formatVentes($ventes);

            $ventesParProduit = app(ChartController::class)->ventesParProduit();
            $ventesMensuelles = app(ChartController::class)->ventesParJourDuMois();
            $venteParClient = app(ChartController::class)->ventesParClient();
            $MeilleurVente=app(ChartController::class)->meilleureVenteDuMois();
            $MeilleurClient=app(ChartController::class)->meilleurClientDuMois();
            $NombreFactures=app(ChartController::class)->nombreDeFacturesDuMois();
            $TotalGeneré=app(ChartController::class)->totalGenereDuMois();
            
            return [

                'factures' => $factures,
                'devis' => $devis,  
                'avoirs' => $avoirs,

                'chartData' => [
                    [
                        'labels' => $ventesParProduit->pluck('nom')->toArray(),
                        'values' => $ventesParProduit->pluck('total_ventes')->toArray(),
                    ],
                ],
                'monthlySalesData' => [
                    [
                        'labels' => $ventesMensuelles->pluck('date')->toArray(),
                        'values' => $ventesMensuelles->pluck('total_ventes')->toArray(),
                    ],
                ],
                'ClientData' => [
                    [
                        'labels' => $venteParClient->pluck('client')->toArray(),
                        'values' => $venteParClient->pluck('total_ventes')->toArray(),
                    ],
                ],

                
                'metrics' => [ 
                    'Vente'    => ['value' => $MeilleurVente?->produit ?? 'Aucune vente', 'diff' => $MeilleurVente?->total_ventes ?? 0],
                    'Client'   => ['value' => $MeilleurClient?->client ?? 'Aucun client', 'diff' => $MeilleurClient?->total_ventes ?? 0],
                    'Facture' => ['value' => $NombreFactures ?? 0, 'diff' => 0],
                    'Total'    => $TotalGeneré->first()?->total_ventes ?? 0,
                ],
                
                'ventesMensuelles' => $ventesMensuelles,
                'MeilleurVente' => $MeilleurVente,
                'MeilleurClient' => $MeilleurClient,
                'NombreFactures' => $NombreFactures,
                'TotalGeneré' => $TotalGeneré->first()?->total_ventes ?? 0, 
                
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
            Layout::metrics([
                'Meilleur Vente'    => 'metrics.Vente',
                'Meilleur Client' => 'metrics.Client',
                'Nombre de Factures du mois' => 'metrics.Facture',
                'Total Generé' => 'metrics.Total',
            ]),

            Layout::columns([
                Layout::tabs([
                    'Factures' => FactureTable::class,
                    'Devis' => DevisTable::class,
                    'Avoirs' => AvoirTable::class,
                ]),
                
                
            ]),
            layout::columns([
                ProductChart::class,
                VenteChart::class,
            ]),
            ClientChart::class,
            
        ];
    }
}
