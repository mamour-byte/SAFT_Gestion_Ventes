<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
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
                    Input::make('product.name')
                        ->title('Nom du produit')
                        ->required(),
    
                    Input::make('product.price')
                        ->title('Prix du produit')
                        ->type('number')
                        ->required(),
                ]),
            ];
        
    }


    public function save($request)
        {
            Product::create($request->get('product'));

            Toast::info('Produit ajouté avec succès.');

            return redirect()->route('platform.product.list');
        }
}
