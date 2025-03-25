<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Actions\Menu;
use Orchid\Screen\Layouts\TabMenu;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;

class VentesTabNav extends TabMenu
{
    /**
     * Get the menu elements to be displayed.
     *
     * @return Menu[]
     */
    protected function navigations(): iterable
    {
        return [
            Menu::make('Nouvelle Vente')
                ->route('platform.ventes.nouvelle'),

            Menu::make('Historique des Ventes')
                ->route('platform.ventes.historique'),
        ];
    }

   



}
