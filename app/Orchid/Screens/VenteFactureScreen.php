<?php

namespace App\Orchid\Screens;

use App\Models\Ventes;
use App\Models\Facture;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Support\Facades\Toast;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Fields\Group;
use App\Models\Product;

class VenteFactureScreen extends Screen
{
    public $vente;

    public $name = 'Transformation en Facture';

    public function query(int $id): array
    {
        $this->vente = Ventes::with('facture', 'details.product', 'client')->findOrFail($id);

        if (!$this->vente->facture || !in_array($this->vente->facture->type_document, ['devis', 'avoir'])) {
            Toast::error("Seuls les devis ou avoirs peuvent être transformés en facture.");
            redirect()->route('platform.ventes');
        }

        return [
            'vente' => $this->vente,

        'produits_selectionnes' => $this->vente->details->pluck('id_product')->toArray(),
        'quantites_selectionnees' => $this->vente->details->pluck('quantite')->implode(','),
        ];
    }

    public function commandBar(): array
    {
        return [
            Button::make('Créer la facture')
                ->method('transformer')
                ->confirm('Confirmez-vous la création de cette facture ?'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('vente.facture.numero_facture')
                    ->title('Numéro document source')
                    ->disabled(),

                Input::make('vente.client.nom')
                    ->title('Client')
                    ->disabled(),

                Input::make('vente.created_at')
                    ->title('Date du document')
                    ->disabled()
                    ->value(optional($this->vente)->created_at?->format('d/m/Y')),

                Group::make([
                    Select::make('vente.produits')   
                        ->title('Produits')
                        ->options(Product::where('archived', 0)
                        ->pluck('nom', 'id_product'))
                        ->multiple()
                        ->required()
                        // ->value($this->vente->details->product)
                        ->help('Sélectionnez les produits (Ctrl+clic pour multiple)'),
                        
                    Input::make('vente.quantites')
                        ->title('Quantités')
                        ->type('text')
                        ->required()
                        ->help('Format: 1,2,3 (une quantité par produit)'),
                    
                    ]),
                    
                Switcher::make('vente.tva')
                    ->title('Appliquer la TVA 18%')
                    ->sendTrueOrFalse()
                    ->value($this->vente->facture->tva),

            ]),
        ];
    }
    

    public function transformer(\Illuminate\Http\Request $request)
        {
            $vente = Ventes::with('facture')->findOrFail($this->vente->id_vente);

            if (!$vente->facture || !in_array($vente->facture->type_document, ['devis', 'avoir'])) {
                Toast::error("Transformation possible uniquement depuis un devis ou un avoir.");
                return redirect()->route('platform.ventes');
            }

            DB::beginTransaction();

            try {
                $data = $request->get('facture');
                $docSource = $vente->facture;

                $statut = 'Validé';

                // Crée la nouvelle facture
                $nouvelleFacture = Facture::create([
                    'type_document'     => 'facture',
                    'tva'               => $data['tva'] ?? $docSource->tva,
                    'statut'            => $statut,
                    'reference_avoir'   => $docSource->type_document === 'avoir' ? $docSource->numero_facture : null,
                    'id_client'         => $docSource->id_client,
                    'id_user'           => Auth::id(),
                    'note'              => $data['note'] ?? null,
                ]);

                // Mise à jour de la vente avec la nouvelle facture
                $vente->update(['id_facture' => $nouvelleFacture->id_facture]);

                // Mise à jour du document source (avoir ou devis)
                $docSource->update([
                    'statut' => 'Transformé en facture',
                ]);

                DB::commit();

                Toast::success("Facture créée avec succès !");
                return redirect()->route('platform.ventes');

            } catch (\Exception $e) {
                DB::rollBack();
                Toast::error("Erreur lors de la transformation : " . $e->getMessage());
                return redirect()->route('platform.ventes');
            }
        }


}
