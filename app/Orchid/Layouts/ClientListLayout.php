<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Models\Client;

class ClientListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'Client';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('nom', 'Nom')
                ->render(fn ($product) => $product->nom),

            TD::make('email', 'email')
                ->render(fn ($product) => $product->description),

            TD::make('telephone', 'Telephone')
                ->render(fn ($product) => number_format($product->prix_unitaire, 2) . ' f CFA'),
            
            TD::make('adresse', 'Adresse')
                ->render(fn ($product) => $product->quantite_stock),

            TD::make('Action')
                ->render(fn (Client $product) => 
                    Link::make()
                        ->icon('pencil')
                        ->route('platform.client.edit', $product->id)
                ),
            

        ];
    }
}
