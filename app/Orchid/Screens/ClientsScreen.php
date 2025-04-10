<?php

namespace App\Orchid\Screens;
use Orchid\Screen\Actions\Link;
use App\Models\Client;
use App\Orchid\Layouts\ClientListLayout;
use Orchid\Screen\Screen;

class ClientsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
        {
            $clients = Client::paginate(10);
            return [
                'clients' => $clients,
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
            Link::make(__('Add'))
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


    
}
