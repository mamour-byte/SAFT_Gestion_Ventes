<?php

namespace App\Orchid\Layouts\TabsNav;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Models\Client;
use App\Models\Ventes;

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

            TD::make('date', 'Date')
                ->render(function ($vente) {
                    return $vente->created_at->format('d/m/Y');
                }),
            
            TD::make('total', 'Total TTC')
                ->render(function (Ventes $vente) {
                    $total = $vente->details->sum(function ($detail) {
                        return $detail->prix_total ?? 0;
                    });
                    return number_format($total) . ' F CFA';
                }),

            TD::make('status', 'Statut')
                    ->render(function (Ventes $vente) {
                        $statut = $vente->facture->statut ?? 'Non défini';

                        $color = match($statut) {
                            'Validé' => 'text-success',
                            'En attente' => 'text-danger',
                            default => 'text-muted'
                        };

                        return "<span class='{$color}'>{$statut}</span>";
                    })
        ];
    }
}
