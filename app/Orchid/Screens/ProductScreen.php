<?php

namespace App\Orchid\Screens;
use Orchid\Screen\Actions\Link;
use App\Models\Product;
use App\Orchid\Layouts\ProductListLayout;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;
use Orchid\Support\Facades\Reload;

class ProductScreen extends Screen
{
    public $exists = true; 

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'product' => Product::where('archived', 0)->paginate(10)
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Liste des Produits';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
        {
            return [
                Link::make(__('Add'))
                    ->icon('bs.plus-circle')
                    ->route('platform.product.add'), 
            ];
        }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    // public function layout(): iterable
    
    public function layout(): iterable
    {
        return [
            ProductListLayout::class,
        ];
    }

    /**
     * Handle the deletion of a client.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Product $product)
        {
            $product->archived = 1;
            $product->save();

            Toast::info('Produit archivé avec succès.');
            return Reload::reload();
        }




}
