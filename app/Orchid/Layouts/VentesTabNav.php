<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Actions\Menu;
use Orchid\Screen\Layouts\TabMenu;

class VentesTabNav extends TabMenu
{
    /**
     * Définir les onglets de navigation.
     *
     * @return Menu[]
     */
    protected function navigations(): iterable
    {
        return [
            Menu::make('Formulaire')
                ->route('platform.ventes', ['tab' => 'formulaire'])
                ->icon('note'),

            Menu::make('Tableau')
                ->route('platform.ventes', ['tab' => 'tableau'])
                ->icon('grid'),

            Menu::make('Historique')
                ->route('platform.ventes', ['tab' => 'historique'])
                ->icon('clock'),
        ];
    }

    /**
     * Onglet actif.
     */
    protected function activeTab(): ?string
    {
        return request()->get('tab', 'formulaire');
    }
}
