<?php

namespace App\Orchid\Screens;
use Orchid\Screen\Actions\Link;
use App\Models\Client;
use App\Orchid\Layouts\ClientListLayout;
use Orchid\Screen\Screen;

class ClientScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'Clients' => Client::paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Liste des Client';
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
                    ->route('platform.product.add'),
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
