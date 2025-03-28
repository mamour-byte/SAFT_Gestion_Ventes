<?php

namespace App\Orchid\Layouts\TabsNav;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;

class HistVentesRow extends Table
{
    /**
     * Data source.
     *
     * @var string
     */
    protected $target = 'produitsAjoutes'; // Nom de la clé contenant les données

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('nom', 'Produit')
                ->render(function ($produit) {
                    return $produit['nom'];
                }),

            TD::make('quantite', 'Quantité')
                ->render(function ($produit) {
                    return $produit['quantite'];
                }),

            TD::make('prix_unitaire', 'Prix Unitaire')
                ->render(function ($produit) {
                    return number_format($produit['prix_unitaire'], 2) . ' €';
                }),

            TD::make('total', 'Total')
                ->render(function ($produit) {
                    return number_format($produit['quantite'] * $produit['prix_unitaire'], 2) . ' €';
                }),

            
        ];


    }
}