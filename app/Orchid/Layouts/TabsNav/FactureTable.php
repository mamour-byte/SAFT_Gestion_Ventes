<?php

namespace App\Orchid\Layouts\TabsNav;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Models\Client;

class FactureTable extends Table
{
    protected $target = 'factures'; // Doit correspondre à la clé dans query()

    protected function columns(): iterable
    {
        return [
            TD::make('client.nom', 'Client')
                ->render(function ($vente) {
                    return $vente->client->nom ?? 'Client inconnu';
                }),

            TD::make('produits', 'Produits')
                ->render(function ($vente) { 
                    return collect($vente->details)->map(function ($detail) {
                        return ($detail->product->nom ?? 'Produit inconnu') . ' x' . ($detail->quantite_vendue ?? 0);
                    })->join(', ');
                }),

            TD::make('date_vente', 'Date livraison')
                ->render(function ($vente) {
                    return $vente->date_vente
                        ? \Carbon\Carbon::parse($vente->date_vente)->format('d/m/Y')
                        : 'Non spécifiée';
                }),
        ];
    }
}
