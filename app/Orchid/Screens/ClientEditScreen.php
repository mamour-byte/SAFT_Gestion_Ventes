<?php

namespace App\Orchid\Screens;
use App\Models\Client;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Layout;

class ClientEditScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Client $client): iterable
        {
            return [
                'client' => $client,
            ];
        }


       /**
     * Save the edited product in the database.
     *
     * @param  array  $data
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(array $data)
        {
            // Récupérer l'ID depuis la route
            $client = Client::find($data['id']);
            
            // Mettre à jour les informations du produit
            $client->update([
                'nom' => $data['nom'],
                'email' => $data['email'],
                'telephone' => $data['telephone'],
                'adresse' => $data['adresse'],
            ]);

            
            return redirect()->route('platform.client.edit', ['id' => $client->id])
                ->with('success', 'Le client a été mis à jour avec succès.');
        }



    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Editer un Client ';
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
                    ->required(),
    
                Input::make('client.email')
                    ->title('Email')
                    ->required(),
    
                Input::make('client.telephone')
                    ->title('Telephone')
                    ->type('number')
                    ->required(),
    
                Input::make('client.adresse')
                    ->title('Adresse')
                    ->required(),
            ]),
        ];
    }
}
