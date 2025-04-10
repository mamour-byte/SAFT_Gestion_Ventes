<?php
<<<<<<< HEAD

=======
>>>>>>> dfcd6ef55fb9b0cb2df691f1c7e7cf19b2ed9af1
namespace App\Orchid\Layouts\TabsNav;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
<<<<<<< HEAD
=======
use Orchid\Support\Facades\Layout;
>>>>>>> dfcd6ef55fb9b0cb2df691f1c7e7cf19b2ed9af1

class HistVentesRow extends Table
{
    protected $target = 'ventesSession';

    protected function columns(): array
    {
        return [
            TD::make('client', 'Client')
                ->render(function ($item) {
<<<<<<< HEAD
                    $client = \App\Models\Client::find($item['id_client']);
                    return $client ? $client->nom : 'Client inconnu';
=======
                    return $item['nom'];
>>>>>>> dfcd6ef55fb9b0cb2df691f1c7e7cf19b2ed9af1
                }),
                
            TD::make('produits', 'Produits')
                ->render(function ($item) {
                    return collect($item['produits'])->map(function ($produit) {
<<<<<<< HEAD
                        return $produit['nom'] . ' (x' . $produit['quantite'] . ') - ' . number_format($produit['prix_unitaire'] ) . 'f cfa';
=======
                        return $produit['nom'].' (x'.$produit['quantite'].') - '.number_format($produit['prix_unitaire'], 2).'€';
>>>>>>> dfcd6ef55fb9b0cb2df691f1c7e7cf19b2ed9af1
                    })->implode('<br>');
                }),
                
            TD::make('total', 'Total')
                ->render(function ($item) {
<<<<<<< HEAD
                    $total = collect($item['produits'])->sum(function ($produit) {
                        return $produit['quantite'] * $produit['prix_unitaire'];
                    });
                    return number_format($total ) . 'f cfa';
=======
                    return number_format($item['total'], 2).'€';
>>>>>>> dfcd6ef55fb9b0cb2df691f1c7e7cf19b2ed9af1
                }),
                
            TD::make('actions', 'Actions')
                ->render(function ($item, $key) {
<<<<<<< HEAD
                    return
=======
                    return Layout::rows([
>>>>>>> dfcd6ef55fb9b0cb2df691f1c7e7cf19b2ed9af1
                        Button::make('Supprimer')
                            ->method('removeFromVentesTable')
                            ->parameters(['index' => $key])
                            ->class('btn btn-danger btn-sm')
<<<<<<< HEAD
                            ->confirm('Voulez-vous vraiment supprimer cette vente?')
                            ->render() . ' ' .
                        Button::make('Modifier')
                            ->method('editVente')
                            ->parameters(['index' => $key])
                            ->class('btn btn-warning btn-sm')
                            ->render();
=======
                            ->confirm('Voulez-vous vraiment supprimer cette vente?'),
                            
                        Button::make('Modifier')
                            ->method('editVente')
                            ->parameters(['index' => $key])
                            ->class('btn btn-warning btn-sm'),
                    ]);
>>>>>>> dfcd6ef55fb9b0cb2df691f1c7e7cf19b2ed9af1
                }),
        ];
    }
}