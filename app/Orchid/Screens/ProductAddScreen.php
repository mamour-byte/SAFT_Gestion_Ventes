<?php

namespace App\Orchid\Screens;

use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use App\Models\Product;

class ProductAddScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Ajouter un Produit';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
                Button::make('Enregistrer')
                    ->method('save'),
            ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
    
        return [
            Layout::rows([
                Input::make('product.nom')
                    ->title('Nom du produit')
                    ->required()
                    ->placeholder('Entrez le nom du produit'),

                TextArea::make('product.description')
                    ->title('Description')
                    ->rows(3)
                    ->placeholder('Entrez une description (optionnel)'),

                Input::make('product.prix_unitaire')
                    ->title('Prix unitaire')
                    ->type('number')
                    ->step(0.01)
                    ->required()
                    ->placeholder('Entrez le prix unitaire'),

                Input::make('product.quantite_stock')
                    ->title('Quantité en stock')
                    ->type('number')
                    ->required()
                    ->placeholder('Entrez la quantité disponible'),
            ]),
        ];
        
    }


    public function save(Request $request)
        {
            // Validation et enregistrement
            Product::create($request->input('product'));

            // Message de confirmation
            Toast::info('Produit ajouté avec succès.');

            // Redirection vers la liste des produits
            return redirect()->route('platform.product');
        }
}
