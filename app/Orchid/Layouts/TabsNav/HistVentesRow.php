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
                


            TD::make('Action')
                ->render(function (Ventes $vente) {
                    $facture = $vente->facture;
                    if (!$facture) {
                        return 'Aucun document';
                    }

                    return match ($facture->type_document) {
                        'facture' => Link::make('Transformer en Avoir')
                                    ->icon('refresh')
                                    ->route('platform.ventes.transformer_avoir', ['id' => $vente->id_vente])
                                    ->confirm('Confirmez-vous la transformation de cette facture en avoir ?'),

                        'avoir', 'devis' => Link::make('Générer une Facture')
                                    ->icon('file-text')
                                    ->route('platform.ventes.generer_facture', ['id' => $vente->id_vente])
                                    ->confirm('Voulez-vous générer une facture pour ce document ?'),

                        default => 'Action inconnue',
                    };
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

            TD::make('BorderauLivraison', 'Bordereau de Livraison')
                ->render(function (Ventes $vente) {
                    return Link::make('Bordereau')
                        ->icon('file-earmark-text')
                        ->class('btn btn-primary btn-sm')
                        ->route('platform.bordereau.preview', [
                            'id' => $vente->id_vente,
                        ]);
                }),
            
            
            
        ];
    }
}
