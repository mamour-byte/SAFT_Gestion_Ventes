<?php

namespace App\Orchid\Layouts\TabsNav;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class FactureTable extends Table
{
    protected $target = 'factures'; // Doit correspondre à la clé dans query()

    protected function columns(): iterable
    {
        return [
            TD::make('client', 'Client')
                ->render(function (array $item) { 
                    $client = \App\Models\Client::find($item['id_client']);
                    return $client ? $client->nom : 'Client inconnu';
                }),

            TD::make('produits', 'Produits')
                ->render(function (array $vente) { 
                    return collect($vente['produits'])->map(function (array $p) {
                        return $p['nom'] . ' x' . $p['quantite'];
                    })->join(', ');
                }),

            TD::make('date_livraison', 'Date livraison')
                ->render(fn(array $vente) => $vente['date_livraison']),
        ];
    }
}