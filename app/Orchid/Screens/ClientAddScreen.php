<?php

namespace App\Orchid\Screens;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Http\Request;
use App\Models\Client;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Group;

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
                Input::make('client.nom')
                    ->title('Nom du Client')
                    ->required()
                    ->placeholder('Entrez le nom du client'),

                Group::make([
                    Input::make('client.email')
                    ->title('Email')
                    ->placeholder('Entrez l email '),

                    Input::make('client.telephone')
                        ->title('Telehpone')
                        ->type('number')
                        ->mask('(+221) 99-999-99-99')
                        ->placeholder('Entrez le numero de telephone'),

                    Input::make('client.adresse')
                        ->title('Adresse')
                        ->placeholder('Entrez l Adresse '),
                    ]),

                    Group::make([
                    Input::make('client.NumeroNinea')
                        ->title('Numéro NINEA')
                        ->placeholder('Entrez le numéro NINEA'),

                    Input::make('client.NumeroRegistreCommerce')
                        ->title('Numéro Registre de Commerce')
                        ->placeholder('Entrez le numéro Registre de Commerce'),
                    ]),
            ])
           
        ];
    }


    public function save(Request $request)
        {
            // Validation et enregistrement
            Client::create($request->input('client'));

            // Message de confirmation
            Toast::info('Client ajouté avec succès.');

            // Redirection vers la liste des produits
            return redirect()->route('platform.clients');
        }
}
