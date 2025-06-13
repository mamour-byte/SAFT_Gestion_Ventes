<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use App\Models\Ventes;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Layout;

class BordereauPreview extends Screen
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
                Layout::view('orchid.preview-bordereau'),
            ];
        }
}
