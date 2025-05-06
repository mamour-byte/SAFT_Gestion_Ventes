<?php

namespace App\Orchid\Layouts\TabsNav;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Models\Client;

class DevisTable extends Table
{
    protected $target = 'devis';

    protected function columns(): iterable
    {
        return [
            TD::make('client_nom', 'Client')
                ->render(function ($item) {
                    $client = Client::find($item['id_client']);
                    return $client ? $client->nom : 'Client inconnu';
                }), 

            TD::make('produits', 'Produits')
                ->render(function ($vente) {
                    return collect($vente->details)->map(function ($detail) {
                        return ($detail->product->nom ?? 'Produit inconnu') . ' x' . ($detail->quantite_vendue ?? 0);
                    })->join(', ');
                }),
            

            TD::make('date_livraison', 'Date livraison')
                ->render(function( $vente) {
                    return $vente->date_livraison ? $vente->date_livraison->format('d/m/Y') : 'Non spécifiée';
                }), 
        ];
    }
}