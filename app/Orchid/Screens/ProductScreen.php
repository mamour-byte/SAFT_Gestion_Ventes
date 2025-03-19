<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use App\Orchid\Layouts\ProductListLayout;
use Orchid\Screen\Screen;

class ProductScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'products' => Product::paginate(10), // Utiliser paginate() pour optimiser
        ];
    }

    public function name(): ?string
    {
        return 'Liste des Produits';
    }

    public function layout(): iterable
    {
        return [
            ProductListLayout::class,
        ];
    }
}
