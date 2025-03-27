<?php

namespace App\Orchid\Layouts\TabsNav;

use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Layouts\Layout; // Import manquant ajouté
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Button; // Import manquant ajouté
use App\Models\Client; 

class NouvVentesRow extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'nouvelleVentes';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            
                Select::make('id_client')
                    ->title('Client')
                    ->options(Client::pluck('nom', 'id'))
                    ->required(),
    
                Group::make([
                    Input::make('produits[0][id]')
                        ->title('Produit ID'),
                    Input::make('produits[0][quantite]')
                        ->title('Quantité'),
                    Input::make('produits[0][prix]')
                        ->title('Prix Total'),
                ]),
    
                CheckBox::make('status')
                    ->sendTrueOrFalse()
                    ->title('TVA'),
    
                // Button::make('Enregistrer la vente')
                //     ->method('saveVente') 
                //     ->confirm('Voulez-vous vraiment enregistrer cette vente ?'),
            
        ];
    }
}