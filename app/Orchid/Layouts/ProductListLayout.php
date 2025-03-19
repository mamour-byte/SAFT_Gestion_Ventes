<?php

namespace App\Orchid\Layouts;
use Orchid\Screen\Actions\Link;
use App\Models\Product;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ProductListLayout extends Table
{
    // La clé 'products' correspond à celle retournée par la méthode query()
    protected $target = 'products';

    protected function columns(): iterable
    {
        return [
            TD::make('nom', 'Nom')
                ->render(fn ($product) => $product->nom),

            TD::make('description', 'Description')
                ->render(fn ($product) => $product->description),

            TD::make('prix_unitaire', 'Prix Unitaire')
                ->render(fn ($product) => number_format($product->prix_unitaire, 2) . ' f CFA'),
            
            TD::make('quantite_stock', 'Quantité en Stock')
                ->render(fn ($product) => $product->quantite_stock),
            
            TD::make('created_at', 'Date de Création')
                ->render(fn ($product) => $product->created_at->format('Y-m-d')),

            TD::make('Action')
                ->render(fn (Product $product) => 
                    Link::make()
                        ->icon('pencil')
                        ->route('platform.product.edit', $product->id)
                ),
            

        ];
    }
}
