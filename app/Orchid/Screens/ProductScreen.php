<?php

namespace App\Orchid\Screens;
use Orchid\Screen\Actions\Link;
use App\Models\Product;
use App\Orchid\Layouts\ProductListLayout;
use Orchid\Screen\Screen;

class ProductScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'product' => Product::paginate(10), 
        ];
    }

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

    public function layout(): iterable
    {
        return [
            ProductListLayout::class,
        ];
    }
}
