<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Illuminate\Http\Request;
use App\Orchid\Layouts\TabsNav\NouvVentesRow;
use App\Orchid\Layouts\TabsNav\HistVentesRow;
use Orchid\Screen\Actions\Button;
use App\Models\Product;

class VentesScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
        {
            $produitsAjoutes = session('produitsAjoutes', []); 
            return [
                'produitsAjoutes' => $produitsAjoutes,
            ];
        }

    /**
     * Display screen name.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Nouvelle Vente';
    }

    /**
     * Button commands.
     *
     * @return array
     */
    public function commandBar(): array
    
    {
    return [

        Button::make('Enregistrer la vente')
            ->route('ventes.save') 
            ->class('btn btn-primary'),
        ];
    }

    /**
     * Views.
     *
     * @return array
     */
    public function layout(): array
    {
        return [
            NouvVentesRow::class,
            HistVentesRow::class,
        ];
    }
   
}