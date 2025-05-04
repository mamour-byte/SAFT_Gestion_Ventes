<?php

namespace App\Orchid\Layouts;
use Orchid\Screen\Actions\Link;
use App\Models\Product;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;

class ProductListLayout extends Table
{
    // La clé 'products' correspond à celle retournée par la méthode query()
    protected $target = 'product';

    protected function columns(): iterable
    {
        return [
            TD::make('nom', 'Nom')
                ->render(fn ($product) => $product->nom),

            TD::make('description', 'Description')
                ->render(fn ($product) => $product->description),

            TD::make('prix_unitaire', 'Prix Unitaire')
                ->render(fn ($product) => number_format($product->prix_unitaire, ) . ' F CFA'),
            
            TD::make('quantite_stock', 'Quantité en Stock')
                ->render(fn ($product) => $product->quantite_stock),       

            TD::make('Modifier')
                ->render(fn (Product $product) => Link::make()
                    ->icon('pencil')
                    ->route('platform.product.edit', $product)),

            TD::make('Supprimer')
                ->render(fn (Product $product) =>
                    Button::make()
                        ->icon('trash')
                        ->confirm('Voulez-vous vraiment supprimer ce Produit ?')
                        ->method('delete', ['product' => $product->id_product])
                    ),
            
            

        ];
    }
}
