<?php

namespace App\Orchid\Screens;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

use Orchid\Screen\Screen;

class ClientAddScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'ClientAddScreen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
            return [
                Button::make('Enregistrer')
                    ->method('save'),
            ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('clients.nom')
                    ->title('Nom du Client')
                    ->required()
                    ->placeholder('Entrez le nom du client'),

                Input::make('client.email')
                    ->title('Email')
                    ->rows(3)
                    ->placeholder('Entrez l email '),

                Input::make('clients.telephone')
                    ->title('Telehpone')
                    ->type('number')
                    ->required()
                    ->placeholder('Entrez le numero de telephone'),

                Input::make('clients.adresse')
                    ->title('Adresse')
                    ->required()
                    ->placeholder('Entrez l Adresse '),
            ]),
        ];
    }
}
