<?php
//

namespace App\Orchid\Layouts\TabsNav;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use App\Models\Ventes;
use App\Models\Facture;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;



class HistVentesRow extends Table
{
    protected $target = 'ventes';

    protected function columns(): array
    {
        return [
            TD::make('date', 'Date')
                ->render(function ($vente) {
                    return $vente->created_at->format('d/m/Y');
                }),


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

            TD::make('status', 'Statut')
                    ->render(function (Ventes $vente) {
                        $statut = $vente->facture->statut ?? 'Non défini';

                        $color = match($statut) {
                            'Validé' => 'text-success',
                            'En attente' => 'text-danger',
                            default => 'text-muted'
                        };

                        return "<span class='{$color}'>{$statut}</span>";
                    }),
                
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(fn (Ventes $vente) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Modifier'))
                                ->route('platform.ventes.edit', $vente->id_vente)
                                ->icon('bs.pencil'),

                            Link::make('Supprimer')
                                ->icon('bs.trash3')
                                ->route('platform.ventes.delete', $vente->id_vente)
                                ->confirm('Êtes-vous sûr de vouloir supprimer cette vente ?'),
                        ])),


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
