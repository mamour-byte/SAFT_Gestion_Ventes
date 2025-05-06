<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use App\Http\Controllers\VenteController;
use Illuminate\Http\RedirectResponse;

class ProductEditScreen extends Screen
{

    /**
     * Fetch data to be displayed on the screen.
     *
     * @param Product $product
     * @return array
     */
    public function query(?Product $product): array
        {
            $this->product = $product ?? new Product();
            
            return [
                'product' => $this->product
            ];
        }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return "Éditer le produit : {$this->product->nom}";
    }

    /**
     * The screen's action buttons.
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Enregistrer')
                ->icon('bs.check-circle')
                ->method('save'),
                
            Button::make('Supprimer')
                ->icon('bs.trash')
                ->method('destroy')
                ->confirm('Êtes-vous sûr de vouloir supprimer ce produit?'),
        ];
    }

    /**
     * The screen's layout elements.
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('product.nom')
                    ->title('Nom du produit')
                    ->required(),
    
                TextArea::make('product.description')
                    ->title('Description')
                    ->rows(4)
                    ->required(),
    
                Input::make('product.prix_unitaire')
                    ->title('Prix unitaire')
                    ->type('number')
                    ->step('0.01')
                    ->required(),
    
                Input::make('product.quantite_stock')
                    ->title('Quantité en stock')
                    ->type('number')
                    ->required(),
            ]),
        ];
    }

    /**
     * Save the product.
     */
        public function save(Product $product, Request $request)
        {
            return app(VenteController::class)->update($request, $product);
        }

    /**
     * Remove the product.
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
        {
            return app(VenteController::class)->destroy($product);
        }

    
    
}