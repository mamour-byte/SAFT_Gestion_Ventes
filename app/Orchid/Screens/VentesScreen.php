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
use Orchid\Screen\AsSource;
use App\Exports\VentesExport;
use Orchid\Screen\Actions\DropDown;
use Maatwebsite\Excel\Facades\Excel;



class VentesScreen extends Screen
{
    use AsSource;
    /**
     * Query data.
     *
     * @return array
     */


    public $exists = true;


    public function commandBar(): iterable
        {
            return [
                 DropDown::make('Exporter Excel')
                    ->icon('filetype-xlsx')
                    ->list([
                        Button::make('Toutes')->method('exportVentes')->parameters(['type' => 'all'])->rawClick(),
                        Button::make('Factures')->method('exportVentes')->parameters(['type' => 'facture'])->rawClick(),
                        Button::make('Devis')->method('exportVentes')->parameters(['type' => 'devis'])->rawClick(),
                        Button::make('Avoirs')->method('exportVentes')->parameters(['type' => 'avoir'])->rawClick(),
                    ]),
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
                $baseQuery = Ventes::with(['client', 'details.product', 'facture']);

                return [
                    'ventes' => (clone $baseQuery)
                        ->latest()
                        ->paginate(20),

                    'devis' => (clone $baseQuery)
                        ->whereHas('facture', fn($q) => $q->where('type_document', 'devis'))
                        ->latest()
                        ->paginate(20),

                    'factures' => (clone $baseQuery)
                        ->whereHas('facture', fn($q) => $q->where('type_document', 'facture'))
                        ->latest()
                        ->paginate(20),

                    'avoirs' => (clone $baseQuery)
                        ->whereHas('facture', fn($q) => $q->where('type_document', 'avoir'))
                        ->latest()
                        ->paginate(20),
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

    public function goToDelete(Request $request): void
        {
            $vente = Ventes::with(['facture', 'details'])->findOrFail($$request->get('id_vente'));

            try {
                $vente->details()->delete();
                if ($vente->facture) {
                    $vente->facture()->delete();
                }
                $vente->delete();

                Toast::info('Vente supprimée avec succès.');
            } catch (\Exception $e) {
                report($e);
                Toast::error('Erreur lors de la suppression de la vente.');
            }
            
        }

    public function exportVentes(Request $request)
        {
            $type = $request->get('type', 'all'); // 'facture', 'devis', 'avoir' ou 'all'
            $fileName = 'ventes_' . $type . '_' . now()->format('Y_m') . '.xlsx';

            return Excel::download(new VentesExport($type), $fileName);
        }

        
        
        
        
}