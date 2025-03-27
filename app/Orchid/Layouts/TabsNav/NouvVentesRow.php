<?php

namespace App\Orchid\Layouts\TabsNav;

use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Actions\Button;
use App\Models\Client;
use App\Models\Product;

class NouvVentesRow extends Rows
{
    /**
     * Get the fields elements to be displayed.
     *
     * @return array
     */
    protected function fields(): array
    {
        return [
            // Sélection du client
            Select::make('id_client')
                ->title('Client')
                ->options(Client::pluck('nom', 'id_client'))
                ->required()
                ->help('Sélectionnez le client pour cette vente.'),

            // Groupe pour les produits et quantités
            Group::make([
                Select::make('produits')
                    ->title('Produits')
                    ->options(Product::pluck('nom', 'id_product'))
                    ->multiple() 
                    ->help('Recherchez et sélectionnez plusieurs produits.')
                    ->required(),

                Input::make('produits_quantites')
                    ->title('Quantités')
                    ->type('text')
                    ->help('Entrez les quantités pour chaque produit sélectionné, séparées par des virgules.')
                    ->required(),
            ]),

            // Champ pour le total calculé
            Input::make('total')
                ->title('Total')
                ->type('number')
                ->readonly()
                ->help('Ce champ sera calculé automatiquement.'),

            // Checkbox pour la TVA
            CheckBox::make('status')
                ->sendTrueOrFalse()
                ->title('TVA')
                ->help('Cochez si la TVA est applicable.'),

            Button::make('Ajouter au tableau')
                ->method('addToTable')
                ->class('btn btn-secondary'),
            
        ];
    }
}