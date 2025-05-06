<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use App\Models\Ventes;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Layout;
use App\Http\Controllers\FacturePdfController;


//         

class FacturePreviewScreen extends Screen
{
    public $name = 'Aperçu du document PDF';

    public function query(Request $request): iterable
    {
        return [
            'venteId' => $request->id,
        ];
    }

    public function layout(): array
    {
        return [
            Layout::view('orchid.preview-pdf'),
        ];
    }
}
