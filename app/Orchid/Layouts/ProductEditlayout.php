<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Fields\Input;

class ProductEditLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Input::make('product.name')
                ->title('Nom du produit')
                ->placeholder('Entrez le nom du produit')
                ->required(),

            Input::make('product.price')
                ->title('Prix du produit')
                ->type('number')
                ->required(),

            Input::make('product.description')
                ->title('Description du produit')
                ->placeholder('Description'),

            Input::make('product.quantite')
                ->title('Quantite')
                ->placeholder('Quantite'),

        ];
    }
}
