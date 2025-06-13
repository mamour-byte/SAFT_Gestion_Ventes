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
use App\Models\Facture;
use \App\Http\Controllers\pdfController;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Toast;
use PDF;
use Orchid\Screen\AsSource;
use App\Exports\VentesExport;
use Orchid\Screen\Actions\DropDown;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\QueryException;



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
                try {
                    $baseQuery = Ventes::with(['client', 'details.product', 'facture']);

                    return [
                        'ventes' => (clone $baseQuery)->latest()->paginate(25),
                        'devis' => (clone $baseQuery)->whereHas('facture', fn($q) => $q->where('type_document', 'devis'))->latest()->paginate(20),
                        'factures' => (clone $baseQuery)->whereHas('facture', fn($q) => $q->where('type_document', 'facture'))->latest()->paginate(20),
                        'avoirs' => (clone $baseQuery)->whereHas('facture', fn($q) => $q->where('type_document', 'avoir'))->latest()->paginate(20),
                        'factures_transformees' => (clone $baseQuery)->whereHas('facture', function ($q) {$q->where('type_document', 'facture')
                                ->where('statut', 'like', '%transformée en avoir%');
                            })->latest()->paginate(20),


                        'erreur_mysql' => false,
                    ];
                } catch (QueryException $e) {
                    return [
                        'erreur_mysql' => true,
                    ];
                }
            }




    public function layout(): array
        {
            if (isset($this->query()['erreur_mysql']) && $this->query()['erreur_mysql']) {
                // Affiche la vue d'erreur si une erreur MySQL a été détectée
                return [
                    Layout::view('orchid.errors.mysql-error'),
                ];
            }

            return [
                Layout::tabs([
                    'Nouvelle Vente' => [NouvVentesRow::class],
                    'Historique' => [HistVentesRow::class],
                    'Factures' => [FactureTable::class],
                    'Devis' => [DevisTable::class],
                    'Avoirs' => [AvoirTable::class],

                ]),
            ];
        }



    public function addToVentesTable(Request $request)
        {
            return (new VenteController)->addToVentesTable($request);
        }

    public function exportVentes(Request $request)
        {
            $type = $request->get('type', 'all'); 
            $fileName = 'ventes_' . $type . '_' . now()->format('Y_m_d_His') . '.xlsx';

            return Excel::download(new VentesExport($type), $fileName);
        }


        
        
        
        
}