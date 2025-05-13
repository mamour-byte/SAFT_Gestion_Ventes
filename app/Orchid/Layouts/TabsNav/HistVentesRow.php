<?php
//

namespace App\Orchid\Layouts\TabsNav;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use App\Models\Ventes;
use App\Models\Facture;


class HistVentesRow extends Table
{
    protected $target = 'ventes';

    protected function columns(): array
    {
        return [
            TD::make('client', 'Client')
                ->render(function (Ventes $vente) {
                    return $vente->client ? $vente->client->nom : 'Client inconnu';
                }),

            TD::make('produits', 'Produits')
                ->render(function (Ventes $vente) {
                    return $vente->details->map(function ($detail) {
                        $product = $detail->product;
                        if (!$product) {
                            return 'Produit supprimé (x' . $detail->quantite_vendue . ')';
                        }
                        return $product->nom
                            . ' (x' . $detail->quantite_vendue . ') - '
                            . number_format($product->prix_unitaire) . ' F CFA';
                    })->implode('<br>');
                }),

            TD::make('tva', 'TVA Applicable')
                ->render(function (Ventes $vente) {
                    return ($vente->facture && $vente->facture->tva) ? 'Oui' : 'Non';
                }),

            TD::make('Type de document', 'Type de document')
                ->render(function (Ventes $vente) {
                    return $vente->facture ? $vente->facture->type_document : 'Non défini';
                }),

                TD::make('total', 'Total TTC')
                ->render(function (Ventes $vente) {
                    $total = $vente->details->sum(function ($detail) {
                        return $detail->prix_total ?? 0;
                    });
                    return number_format($total) . ' F CFA';
                }),

            TD::make('actions', 'Actions')
                ->render(function (Ventes $vente) {

                    $buttons[] = Button::make('Supprimer')
                        ->method('removeFromVentesTable')
                        ->parameters(['id' => $vente->id])
                        ->class('btn btn-danger btn-sm')
                        ->confirm('Voulez-vous vraiment supprimer cette vente?')
                        ->render();

                    $buttons[] = Link::make('Modifier')
                        ->route('platform.ventes.edit', $vente->id_vente) 
                        ->class('btn btn-warning btn-sm')
                        ->render();

                    return implode(' ', $buttons);
                }),

            TD::make('pdf', 'PDF')
                ->render(function (Ventes $vente) {
                    $type = $vente->facture->type_document ?? 'facture';
            
                    return Link::make(' PDF')
                        ->icon('file-pdf-fill')
                        ->class('btn btn-success btn-sm') 
                        ->route('platform.facture.preview', [
                            'id' => $vente->id_vente,
                            'type' => $type,
                        ]);
                }),
            
            
            
        ];
    }
}
