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
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Toast;
use PDF;

class VentesScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public $exists = true;


    public function commandBar(): iterable
        {
            return [];
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
                $baseQuery = Ventes::with(['client', 'details.product', 'facture'])
                    ->where('id_user', auth()->user()->id);

                return [
                    'ventes' => (clone $baseQuery)
                        ->latest()
                        ->paginate(10),

                    'devis' => (clone $baseQuery)
                        ->whereHas('facture', fn($q) => $q->where('type_document', 'devis'))
                        ->latest()
                        ->paginate(10),

                    'factures' => (clone $baseQuery)
                        ->whereHas('facture', fn($q) => $q->where('type_document', 'facture'))
                        ->latest()
                        ->paginate(10),

                    'avoirs' => (clone $baseQuery)
                        ->whereHas('facture', fn($q) => $q->where('type_document', 'avoir'))
                        ->latest()
                        ->paginate(10),
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
        
        
        
        
}