<?php

namespace App\Orchid\Screens;
use Orchid\Screen\Actions\Link;
use App\Models\Client;
use App\Orchid\Layouts\ClientListLayout;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class ClientsScreen extends Screen
{
    public $exists = true; 
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
        {
            $clients = Client::paginate(10);
            return [
                'clients' => Client::where('archived', 0)->paginate(10),
            ];
        }


    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Liste des Clients';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Ajouter'))
                    ->icon('bs.plus-circle')
                    ->route('platform.clients.add'),
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
            ClientListLayout::class,
        ];
    }


    /**
     * Handle the deletion of a client.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Client $client)
        {
            $client->archived = 1;
            $client->save();

            Toast::info('Client archivé avec succès.');
        }

        

}
