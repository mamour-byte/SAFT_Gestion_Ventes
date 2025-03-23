<?php

namespace App\Orchid\Screens;
use Orchid\Screen\Screen;
use App\Models\Product;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;

class ProductEditScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query($id): iterable
        {
            $product = Product::find($id);

            if (!$product) {
                abort(404, 'Produit non trouvé');
            }

            return [
                'product' => $product,
            ];
        }


        /**
     * Save the edited product in the database.
     *
     * @param  array  $data
     * @return \Illuminate\Http\RedirectResponse
     */
        public function save(array $data)
            {
                // Récupérer l'ID depuis la route
                $product = Product::find($data['id_product']);
                
                // Mettre à jour les informations du produit
                $product->update([
                    'nom' => $data['nom'],
                    'description' => $data['description'],
                    'prix_unitaire' => $data['prix_unitaire'],
                    'quantite_stock' => $data['quantite_stock'],
                ]);

                
                return redirect()->route('platform.product.edit', ['id_product' => $product->id])
                    ->with('success', 'Le produit a été mis à jour avec succès.');
            }


    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'ProductEditScreen';
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
                        ->required(),
        
                    Input::make('product.quantite_stock')
                        ->title('Quantité en stock')
                        ->type('number')
                        ->required(),
                
            
        ];
    }
}
