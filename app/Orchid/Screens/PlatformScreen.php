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
use App\Orchid\Layouts\Charts\typeDocsChart;

class PlatformScreen extends Screen
{
    private const DOCUMENT_TYPES = [
        'devis' => 'devis',
        'facture' => 'facture',
        'avoir' => 'avoir'
    ];

    public function name(): ?string
    {
        return 'Acceuil';
    }

    public function description(): ?string
    {
        return 'Bienvenu sur votre application SAFT';
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function query(): array
    {
        // Récupération des données
        $factures = Ventes::with(['client', 'details.product', 'facture'])
            ->whereHas('facture', fn($q) => $q->where('type_document', self::DOCUMENT_TYPES['facture']))
            ->latest()->take(5)->get();

        $devis = Ventes::with(['client', 'details.product', 'facture'])
            ->whereHas('facture', fn($q) => $q->where('type_document', self::DOCUMENT_TYPES['devis']))
            ->latest()->take(5)->get();

        $avoirs = Ventes::with(['client', 'details.product', 'facture'])
            ->whereHas('facture', fn($q) => $q->where('type_document', self::DOCUMENT_TYPES['avoir']))
            ->latest()->take(5)->get();

        // Appels aux méthodes du ChartController
        $chartController = app(ChartController::class);

        $ventesParProduit = $chartController->ventesParProduit();
        $ventesMensuelles = $chartController->ventesParJourDuMois();
        $venteParClient = $chartController->ventesParClient();
        $MeilleurVente = $chartController->meilleureVenteDuMois();
        $MeilleurClient = $chartController->meilleurClientDuMois();
        $NombreFactures = $chartController->nombreDeFacturesDuMois();
        $TotalGeneré = $chartController->totalGenereDuMois();
        $courbesVentesParJour = $chartController->courbesVentesDuMois();
        $StatsDocs = $chartController->statsDocumentsMois();

        // Variables nécessaires aux métriques
        $NombreDevis = $StatsDocs['devis'] ?? 0;
        $NombreAvoirs = $StatsDocs['avoirs'] ?? 0;
        $NombreDocuments = $StatsDocs['total'] ?? ($NombreDevis + $NombreAvoirs + $NombreFactures);

        $TauxConversion = $NombreDevis > 0 
            ? round(($NombreFactures / $NombreDevis) * 100, 2)
            : 0;

        return [

            'factures' => $factures,
            'devis' => $devis,
            'avoirs' => $avoirs,

            'chartData' => [[
                'labels' => $ventesParProduit->pluck('nom')->toArray(),
                'values' => $ventesParProduit->pluck('total_ventes')->toArray(),
            ]],

            'monthlySalesData' => [[
                'labels' => $ventesMensuelles->pluck('date')->toArray(),
                'values' => $ventesMensuelles->pluck('total_ventes')->toArray(),
            ]],

            'ClientData' => [[
                'labels' => $venteParClient->pluck('client')->toArray(),
                'values' => $venteParClient->pluck('total_ventes')->toArray(),
            ]],

            'metrics' => [
                'Vente'    => ['value' => $MeilleurVente?->produit ?? 'Aucune vente', 'diff' => $MeilleurVente?->total_ventes ?? 0],
                'Client'   => ['value' => $MeilleurClient?->client ?? 'Aucun client', 'diff' => $MeilleurClient?->total_ventes ?? 0],
                'Facture'  => ['value' => $NombreFactures ?? 0, 'diff' => 0],
                'Total'    => ['value' => number_format((float)($TotalGeneré ?? 0), 0, '', ' ') . ' FCFA', 'diff' => 0],
                'Devis'    => ['value' => $NombreDevis, 'diff' => 0],
                'Avoirs'   => ['value' => $NombreAvoirs, 'diff' => 0],
                'Documents'=> ['value' => $NombreDocuments, 'diff' => 0],
                'Taux de conversion' => ['value' => $TauxConversion . '%', 'diff' => 0],
            ],

            'courbesData' => [
                [
                    'name' => 'Factures',
                    'labels' => $courbesVentesParJour->pluck('date')->toArray(),
                    'values' => $courbesVentesParJour->pluck('total_factures')->toArray(),
                ],
                [
                    'name' => 'Devis',
                    'labels' => $courbesVentesParJour->pluck('date')->toArray(),
                    'values' => $courbesVentesParJour->pluck('total_devis')->toArray(),
                ],
                [
                    'name' => 'Avoirs',
                    'labels' => $courbesVentesParJour->pluck('date')->toArray(),
                    'values' => $courbesVentesParJour->pluck('total_avoirs')->toArray(),
                ],
            ],

            'ventesMensuelles' => $ventesMensuelles,
            'MeilleurVente' => $MeilleurVente,
            'MeilleurClient' => $MeilleurClient,
            'NombreFactures' => $NombreFactures,
            'TotalGeneré' => $TotalGeneré,
        ];
    }

    public function layout(): iterable
    {
        return [

            Layout::metrics([
                'Meilleur Vente' => 'metrics.Vente',
                'Meilleur Client' => 'metrics.Client',
                'Nombre de Factures' => 'metrics.Facture',
                'Total Generé' => 'metrics.Total',
            ]),

            Layout::metrics([
                'Devis' => 'metrics.Devis',
                'Avoirs' => 'metrics.Avoirs',
                'Total Documents' => 'metrics.Documents',
                'Taux de conversion' => 'metrics.Taux de conversion',
            ]),

            VenteChart::class,

            Layout::columns([
                ProductChart::class,
                ClientChart::class,
            ]),

            typeDocsChart::class,

            Layout::columns([
                Layout::tabs([
                    'Factures' => FactureTable::class,
                    'Devis' => DevisTable::class,
                    'Avoirs' => AvoirTable::class,
                ]),
            ]),
        ];
    }
}
