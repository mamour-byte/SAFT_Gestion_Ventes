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
use \App\Http\Controllers\pdfController;


use Orchid\Support\Facades\Toast;
use PDF;
use Orchid\Screen\Actions\Button;

class VentesScreen extends Screen
{


    public function commandBar(): iterable
        {
            return [ 
            ];
        }


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
                ->where('id_user', auth()->user()->id)
                ->latest()
                ->get();
                
        
            $formatted = $ventes->map(function ($vente) {
                return [
                    'id' => $vente->id_vente, // Utilisez 'id_vente' comme clé primaire
                    'document_id' => $vente->facture['id_facture'] ?? $vente->id_vente, // ID du document
                    'id_client' => $vente->id_client,
                    'client_nom' => $vente->client['nom'] ?? 'Client inconnu',
                    'produits' => $vente->details->map(function ($detail) {
                        return [
                            'nom' => $detail->product->nom ?? 'Produit inconnu',
                            'quantite' => $detail->quantite_vendue ?? 0,
                            'prix_unitaire' => $detail->product->prix_unitaire ?? 0,
                        ];
                    })->values()->all(),
                    'type_document' => $vente->facture['type_document'] ?? 'facture', // Type du document
                    'numero_facture' => $vente->facture['numero_facture'] ?? null,
                    'date_livraison' => $vente->date_vente ?? null,
                ];
            });
        
            return [
                'ventes' => $formatted,
                'devis' => $formatted->filter(fn($v) => $v['type_document'] === 'devis'),
                'factures' => $formatted->filter(fn($v) => $v['type_document'] === 'facture'),
                'avoirs' => $formatted->filter(fn($v) => $v['type_document'] === 'avoir'),
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

        public function documentsDownload(Request $request)
        {
            // dd($request->all());
    
            Toast::info('Méthode documentsDownload appelée : Type=' . $request->type . ' | ID=' . $request->id);
            return app(pdfController::class)->downloadPDF($request);
        }


}