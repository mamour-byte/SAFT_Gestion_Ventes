<?php

namespace App\Orchid\Screens;

use App\Http\Controllers\VenteController;
use App\Orchid\Layouts\TabsNav\NouvVentesRow;
use App\Orchid\Layouts\TabsNav\HistVentesRow;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;

class VentesScreen extends Screen
{
    public function query()
    {
        return [
            'ventesSession' => session()->get('ventes_temp', []),
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