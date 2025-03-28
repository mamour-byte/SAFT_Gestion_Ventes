<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Models\Client;

class ClientListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'clients';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('nom', 'Nom')
                ->render(fn ($client) => $client->nom),

            TD::make('email', 'Email')
                ->render(fn ($client) => $client->email),

            TD::make('telephone', 'Telephone')
                ->render(fn ($client) => $client->client ),
            
            TD::make('adresse', 'Adresse')
                ->render(fn ($client) => $client->adresse),

            TD::make('Editer')
                ->render(fn (Client $client) => 
                    Link::make()
                        ->icon('pencil')
                        ->route('platform.clients.edit', ['id' => $client->id]) // Ensure 'id' is passed as an array
                ),

            TD::make('Supprimer')
                ->render(fn (Client $client) => 
                    Link::make('Supprimer')
                    ->icon('trash')
                    ->method('delete')
                    ->confirm(__('Voulez-vous vraiment supprimer ce client ?'))
                    ->parameters(['id_client' => $client->id]),
                ),
            

        ];
    }
}
