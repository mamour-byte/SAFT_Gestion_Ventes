<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class VentesListeLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'ventes'; // Nom de la clé qui contient les données des ventes

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('nom', 'Nom Produit')
                ->render(fn ($vente) => $vente->nom),

            TD::make('prix', 'Prix')
                ->render(fn ($vente) => number_format($vente->prix, 2) . ' F cfa'),

            TD::make('quantite', 'Quantité')
                ->render(fn ($vente) => $vente->quantite),

            TD::make('date_vente', 'Date de Vente')
                ->render(fn ($vente) => $vente->date_vente->format('d/m/Y')),

            TD::make('actions', 'Actions') // Exemple d'une colonne pour des actions (boutons)
                ->render(fn ($vente) => view('components.action-buttons', ['vente' => $vente])),
        ];
    }
}
