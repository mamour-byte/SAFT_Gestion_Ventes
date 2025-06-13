<?php

namespace App\Orchid\Layouts\TabsNav;

use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Switcher;
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
                    ->options(Product::where('archived', 0)
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
                ])
                ->required()
                ->help('Choisissez le type de document'),

            Switcher::make('vente.tva')
                ->title('Appliquer la TVA 18%')
                ->sendTrueOrFalse(),

            Group::make([
                Input::make('vente.numeroCommande')
                ->title('Numero de Bon de Commande')
                ->help('Entrez le numero de commande'),

                Input::make('vente.dateLivraison')
                    ->title('Date de Livraison')
                    ->help('Entrer la date de livraison')
                    ->type('date')
            ]),
                
            Button::make('Nouvelle Vente')
                ->method('addToVentesTable')
                ->confirm('Confirmez l\'ajout au tableau?')
                ->class('btn btn-primary'),
            
        ];
    }
}