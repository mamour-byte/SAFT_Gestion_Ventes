<?php

namespace App\Orchid\Layouts\TabsNav;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;

class HistVentesRow extends Table
{
    protected $target = 'ventes';


    protected function columns(): array
    {
        return [
            TD::make('client', 'Client')
                ->render(function ($item) {
                    $client = \App\Models\Client::find($item['id_client']);
                    return $client ? $client->nom : 'Client inconnu';
                }),
                
            TD::make('produits', 'Produits')
                ->render(function ($item) {
                    return collect($item['produits'])->map(function ($produit) {
                        return $produit['nom'] . ' (x' . $produit['quantite'] . ') - ' . number_format($produit['prix_unitaire'] ) . 'f cfa';
                    })->implode('<br>');
                }),
                
            TD::make('total', 'Total')
                ->render(function ($item) {
                    $total = collect($item['produits'])->sum(function ($produit) {
                        return $produit['quantite'] * $produit['prix_unitaire'];
                    });
                    return number_format($total ) . 'f cfa';
                }),
            
            TD::make('type_document', 'Type')
                ->render(function ($item) {
                    return ucfirst($item['type_document'] ?? 'Inconnu');
                }),
            
            TD::make('numero_facture', 'N° Document')
                ->render(function ($item) {
                    return $item['numero_facture'] ?? '—';
                }),
            
            TD::make('date_livraison', 'Date livraison')
                ->render(function ($item) {
                    return isset($item['date_livraison']) ? \Carbon\Carbon::parse($item['date_livraison'])->format('d/m/Y') : '—';
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
                    if (($item['type_document'] ?? '') === 'devis') {
                        $buttons[] = Button::make('Transformer en facture')
                            ->method('transformQuoteToInvoice')
                            ->parameters(['index' => $key])
                            ->class('btn btn-primary btn-sm')
                            ->confirm('Confirmer la transformation de ce devis en facture ?')
                            ->render();
                    }
            
                    // Générer le PDF
                    $buttons[] = Button::make('Télécharger PDF')
                        ->method('downloadPDF')
                        ->parameters(['index' => $key])
                        ->class('btn btn-info btn-sm')
                        ->render();
            
                    return implode(' ', $buttons);
                }),
            
        ];
    }
}