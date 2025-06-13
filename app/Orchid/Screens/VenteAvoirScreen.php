<?php

namespace App\Orchid\Screens;

use App\Models\Ventes;
use App\Models\Facture;
use Orchid\Screen\Screen;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Textarea;
use Orchid\Screen\Fields\Label;
use Orchid\Support\Facades\Toast;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class VenteAvoirScreen extends Screen
{
    public $vente;

    public $name = 'Transformation en Avoir';

    public function query(int $id): array
    {
        $vente = Ventes::with('facture', 'details', 'client')->findOrFail($id);

        if (!$vente->facture || $vente->facture->type_document !== 'facture') {
            Toast::error("Seules les factures peuvent être transformées en avoir.");
            redirect()->route('platform.ventes');
        }

        return [
            'vente' => $vente,
        ];
    }

    public function commandBar(): array
    {
        return [
            Button::make('Valider la transformation')
                ->method('transformer')
                ->confirm('Confirmez-vous la transformation de cette facture en avoir ?')
                ->parameters(['id' => $this->vente->id_vente]),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Label::make('vente.facture.numero_facture')
                    ->title('Numéro de facture'),

                Label::make('vente.client.nom')
                    ->title('Client'),

                Label::make('vente.created_at')
                    ->title('Date')
                    ->value(optional($this->vente)->created_at?->format('d/m/Y')),

                Label::make('vente.facture.tva')
                    ->title('TVA')
                    ->value(fn () => $this->vente->facture->tva ? 'Oui' : 'Non'),

                Label::make('vente.details')
                    ->title('Produits')
                    ->value(function () {
                        return $this->vente->details->map(function ($detail) {
                            $product = $detail->product;
                            return $product
                                ? $product->nom . ' (x' . $detail->quantite_vendue . ')'
                                : 'Produit supprimé';
                        })->implode(', ');
                    }),
            ]),
        ];
    }

    public function transformer(int $id)
    {
        $vente = Ventes::with('facture')->findOrFail($id);

        if (!$vente->facture || $vente->facture->type_document !== 'facture') {
            Toast::error("La transformation est uniquement possible pour des factures.");
            return redirect()->route('platform.ventes');
        }

        DB::beginTransaction();

        try {
            $ancienneFacture = $vente->facture;

            // Numérotation
            $numeroAvoir = 'AV-' . Str::padLeft(Facture::where('type_document', 'avoir')->count() + 1, 5, '0');

            // Création de l’avoir
            $avoir = Facture::create([
                'numero_facture'    => $numeroAvoir,
                'type_document'     => 'avoir',
                'tva'               => $ancienneFacture->tva,
                'statut'            => 'En attente',
                'reference_facture' => $ancienneFacture->numero_facture,
                'id_client'         => $ancienneFacture->id_client,
                'id_user'           => Auth::id(),
            ]);


            // Mise à jour de la vente
            $vente->update(['id_facture' => $avoir->id_facture]);

            // Marquage de la facture originale
            $ancienneFacture->update([
                'statut'         => 'Transformée en avoir',
                'reference_avoir'=> $numeroAvoir,
            ]);

            DB::commit();

            Toast::success("Avoir créé avec succès sous le numéro $numeroAvoir.");
            return redirect()->route('platform.ventes'); // vers la liste des ventes


        } catch (\Exception $e) {
            DB::rollBack();
            Toast::error("Erreur lors de la transformation : " . $e->getMessage());
            return redirect()->route('platform.ventes');
        }
    }
}
