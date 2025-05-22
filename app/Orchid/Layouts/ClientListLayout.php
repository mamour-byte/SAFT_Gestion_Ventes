<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Models\Client;
use Orchid\Screen\Actions\Button;

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
            ->render(fn ($client) => $client->telephone),
            
            TD::make('adresse', 'Adresse')
                ->render(fn ($client) => $client->adresse),

            // TD::make('NumeroNinea', 'Ninea')
            //     ->render(fn ($client) => $client->NumeroNinea),

            TD::make('Editer')
                ->render(fn (Client $client) =>         
                Link::make()
                    ->icon('pencil')
                    ->route('platform.clients.edit',  $client->id_client)                      
                ),

                TD::make('Supprimer')
                    ->render(fn (Client $client) =>
                        Button::make()
                            ->icon('trash')
                            ->confirm('Voulez-vous vraiment supprimer ce client ?')
                            ->method('delete', ['client' => $client->id_client])
                    ),
            

        ];
    }

}
