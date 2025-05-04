<?php

namespace App\Orchid\Layouts\TabsNav;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions;
use Orchid\Support\Facades\Toast;
use App\Models\Client;

class HistVentesRow extends Table
{
    protected $target = 'ventes';


    protected function columns(): array
    {
        return [
            TD::make('client', 'Client')
                ->render(function ($item) {
                    return Client::where('id_client', $item['id_client'])
                            ->value('nom') ?? 'Client inconnu';
                }),

            TD::make('produits', 'Produits')
                ->render(function ($item) {
                    return collect($item['produits'])->map(function ($produit) {
                        return $produit['nom'] . ' (x' . $produit['quantite'] . ') - ' . number_format($produit['prix_unitaire'] ) . 'f cfa';
                    })->implode('<br>');
                }),
                       
                
            TD::make('date_livraison', 'TVA Applicable')
                ->render(function ($item) {
                    return isset($item['facture']['tva']) && $item['facture']['tva'] ? 'Oui' : 'Non';
                }),            
            
                    
            TD::make('numero_facture', 'N° Document')
                ->render(function ($item) {
                    return $item['numero_facture'] ?? '—';
                }),
            
            TD::make('total', 'Total TTC')
                ->render(function ($item) {
                    $total = collect($item['produits'])->sum(function ($produit) {
                        return $produit['quantite'] * $produit['prix_unitaire']* 1.18;
                    });
                    return number_format($total ) . 'f cfa';
                }),

                
            TD::make('actions', 'Actions')
                ->render(function ($item, $key) {
                    $buttons = [];
            
                    // Supprimer
                    $buttons[] = Button::make('Supprimer')
                        ->method('removeFromVentesTable')
                        ->parameters(['index' => $key])
                        ->class('btn btn-danger btn-sm')
                        ->confirm('Voulez-vous vraiment supprimer cette vente?')
                        ->render();
            
                    // Modifier
                    $buttons[] = Button::make('Modifier')
                        ->method('editVente')
                        ->parameters(['index' => $key])
                        ->class('btn btn-warning btn-sm')
                        ->render();
            
                    // Transformer un devis en facture
                    if (in_array($item['type_document'] ?? '', ['devis', 'avoir'])) {
                        $buttons[] = Button::make('Transformer en facture')
                            ->method('transformQuoteToInvoice')
                            ->parameters(['index' => $key])
                            ->class('btn btn-primary btn-sm')
                            ->confirm('Confirmer la transformation de ce document en facture ?')
                            ->render();
                    }
                    return implode(' ', $buttons);
                }),
            

            
                TD::make('pdf', 'PDF')
                ->render(function ($item) {
                    $type = $item['type_document'] ?? 'facture';
                    $id = $item['document_id'] ?? null;

                    if (!in_array($type, ['facture', 'devis', 'avoir']) || !is_numeric($id)) {
                        return '—';
                    }

                    return Button::make('Télécharger PDF')
                        ->icon('cloud-download')
                        ->class('btn btn-info btn-sm')
                        ->method('documentsDownload')
                        ->parameters([
                            'type' => $type,
                            'id' => $id,
                        ])
                        ->confirm('Voulez-vous vraiment télécharger ce PDF ?');
                })
        ];

    }
}