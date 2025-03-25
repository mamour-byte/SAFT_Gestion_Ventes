<?php

namespace App\Orchid\Screens;
use Orchid\Screen\Screen;
use App\Models\Ventes;
use App\Models\Product;
use App\Models\Client;
use App\Orchid\Layouts\TabsNav\NouvVentesRow;
use App\Orchid\Layouts\TabsNav\HistVentesRow;

class VentesScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'clients' => Client::pluck('nom', 'id'),
            'produits' => Product::all(),
            'nouvelleVentes' => Ventes::where('status', 'nouvelle')->get(),
            'historiqueVentes' => Ventes::where('status', 'historique')->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Ventes';
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
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
        {
            return [
                NouvVentesRow::class,
                HistVentesRow::class,
            ];
        }
}
