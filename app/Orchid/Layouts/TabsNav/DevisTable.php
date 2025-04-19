<?php

namespace App\Orchid\Layouts\TabsNav;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class DevisTable extends Table
{
    protected $target = 'devis';

    protected function columns(): iterable
    {
        return [
            TD::make('client_nom', 'Client')
                ->render(function ($item) {
                    $client = \App\Models\Client::find($item['id_client']);
                    return $client ? $client->nom : 'Client inconnu';
                }), 

            TD::make('produits', 'Produits')
                ->render(function (array $vente) { 
                    return collect($vente['produits'])->map(fn(array $p) => 
                        $p['nom'] . ' x' . $p['quantite']
                    )->join(', ');
                }),

            TD::make('date_livraison', 'Date livraison')
                ->render(fn(array $vente) => $vente['date_livraison']), 
        ];
    }
}