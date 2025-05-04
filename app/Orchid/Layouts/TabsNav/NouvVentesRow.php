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
    protected function fields(): array
    {
        return [
            Select::make('vente.id_client')
                ->title('Client')
                ->options(
                    Client::where('archived', 0)->pluck('nom', 'id_client')
                )
                ->required()
                ->help('Sélectionnez le client'),
                
            Group::make([
                Select::make('vente.produits')   
                    ->title('Produits')
                    ->options(Product::where('quantite_stock', '>', 0)
                    ->where('archived', 0)
                    ->pluck('nom', 'id_product'))
                    ->multiple()
                    ->required()
                    ->help('Sélectionnez les produits (Ctrl+clic pour multiple)'),
                    
                Input::make('vente.quantites')
                    ->title('Quantités')
                    ->type('text')
                    ->required()
                    ->help('Format: 1,2,3 (une quantité par produit)'),
            ]),
            
            
            Select::make('vente.type_document')
                ->title('Type de document')
                ->options([
                    'devis' => 'Devis',
                    'facture' => 'Facture',
                    'avoir' => 'Avoir',
                ])
                ->required()
                ->help('Choisissez le type de document'),

            CheckBox::make('vente.tva')
                ->sendTrueOrFalse()
                ->title('TVA Applicable (18%)'),
                
            Button::make('Nouvelle Vente')
                ->method('addToVentesTable')
                ->confirm('Confirmez l\'ajout au tableau?')
                ->class('btn btn-primary'),
            
        ];
    }
}