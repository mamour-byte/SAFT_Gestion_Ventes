<?php
namespace App\Orchid\Layouts\TabsNav;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

class HistVentesRow extends Table
{
    protected $target = 'ventesSession';

    protected function columns(): array
    {
        return [
            TD::make('client', 'Client')
                ->render(function ($item) {
                    return $item['nom'];
                }),
                
            TD::make('produits', 'Produits')
                ->render(function ($item) {
                    return collect($item['produits'])->map(function ($produit) {
                        return $produit['nom'].' (x'.$produit['quantite'].') - '.number_format($produit['prix_unitaire'], 2).'€';
                    })->implode('<br>');
                }),
                
            TD::make('total', 'Total')
                ->render(function ($item) {
                    return number_format($item['total'], 2).'€';
                }),
                
            TD::make('actions', 'Actions')
                ->render(function ($item, $key) {
                    return Layout::rows([
                        Button::make('Supprimer')
                            ->method('removeFromVentesTable')
                            ->parameters(['index' => $key])
                            ->class('btn btn-danger btn-sm')
                            ->confirm('Voulez-vous vraiment supprimer cette vente?'),
                            
                        Button::make('Modifier')
                            ->method('editVente')
                            ->parameters(['index' => $key])
                            ->class('btn btn-warning btn-sm'),
                    ]);
                }),
        ];
    }
}