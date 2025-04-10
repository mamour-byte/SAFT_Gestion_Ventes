<?php

namespace App\Orchid\Layouts\TabsNav;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;

class HistVentesRow extends Table
{
    protected $target = 'ventesSession';

    protected function columns(): array
    {
        return [
            TD::make('client', 'Client')
                ->render(function ($item) {
                    $client = \App\Models\Client::find($item['id_client']);
                    return $client ? $client->nom : 'Client inconnu';
                }),
                
            TD::make('produits', 'Produits')
                ->render(function ($item) {
                    return collect($item['produits'])->map(function ($produit) {
                        return $produit['nom'] . ' (x' . $produit['quantite'] . ') - ' . number_format($produit['prix_unitaire'] ) . 'f cfa';
                    })->implode('<br>');
                }),
                
            TD::make('total', 'Total')
                ->render(function ($item) {
                    $total = collect($item['produits'])->sum(function ($produit) {
                        return $produit['quantite'] * $produit['prix_unitaire'];
                    });
                    return number_format($total ) . 'f cfa';
                }),
                
            TD::make('actions', 'Actions')
                ->render(function ($item, $key) {
                    return
                        Button::make('Supprimer')
                            ->method('removeFromVentesTable')
                            ->parameters(['index' => $key])
                            ->class('btn btn-danger btn-sm')
                            ->confirm('Voulez-vous vraiment supprimer cette vente?')
                            ->render() . ' ' .
                        Button::make('Modifier')
                            ->method('editVente')
                            ->parameters(['index' => $key])
                            ->class('btn btn-warning btn-sm')
                            ->render();
                }),
        ];
    }
}