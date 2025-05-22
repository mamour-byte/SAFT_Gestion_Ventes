<?php

namespace App\Orchid\Screens;
use App\Models\Client;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Group;

class ClientEditScreen extends Screen
{
    /**
     * @var Client
     */
    public $client;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @param Client $client
     * @return array
     */
    public function query(Client $client): array
    {
        $this->client = $client;
        
        return [
            'client' => $this->client
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Editer un Client';
    }

    /**
     * The screen's action buttons.
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Enregistrer')
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    /**
     * The screen's layout elements.
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
                    ->title('Téléphone')
                    ->mask('(+221) 99-999-99-99')
                    ->required(),
    
                Input::make('client.adresse')
                    ->title('Adresse')
                    ->required(),

                Group::make([
                    Input::make('client.NumeroNinea')
                        ->title('Numéro NINEA')
                        ->placeholder('Entrez le numéro NINEA'),

                    Input::make('client.NumeroRegistreCommerce')
                        ->title('Numéro Registre de Commerce')
                        ->placeholder('Entrez le numéro Registre de Commerce'),
                    ]),
            ]),
        ];
    }

    /**
     * Save the client.
     */
    public function save(Client $client, Request $request)
    {
        $request->validate([
            'client.nom' => 'required',
            'client.email' => 'required|email',
            'client.telephone' => 'required',
            'client.adresse' => 'required',
            'client.NumeroNinea' => 'nullable',
            'client.NumeroRegistreCommerce' => 'nullable',
        ]);

        $client->update($request->input('client'));

        return redirect()->route('platform.clients')
            ->with('success', 'Client mis à jour avec succès');
    }
}
