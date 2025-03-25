<?php

namespace App\Orchid\Layouts\TabsNav;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class HistVentesRow extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'historiqueVentes';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID')
                ->sort()
                ->filter(TD::FILTER_TEXT),

            TD::make('client', 'Client')
                ->sort()
                ->filter(TD::FILTER_TEXT),

            TD::make('produit', 'Produit')
                ->sort(),

            TD::make('quantite', 'Quantité')
                ->sort(),

            TD::make('total', 'Total')
                ->render(fn ($vente) => number_format($vente->total, 2) . ' €'),

            TD::make('date', 'Date')
                ->sort()
                ->render(fn ($vente) => $vente->date->format('d/m/Y')),
        ];
    }
}