<?php

namespace App\Orchid\Screens;

use App\Models\Ventes;
use App\Models\Client;
use App\Models\Product;
use App\Models\DetailVente;
use App\Models\Facture;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Relation;
use Orchid\Support\Facades\Toast;
use Illuminate\Http\Request;


class VenteUpdateScreen extends Screen
{
    public $name = 'Modifier la Vente';

    public $description = 'Éditez les informations de la vente sélectionnée';

    public $vente;

    public function query(Ventes $vente): array
    {
        $vente->load(['client', 'details.product', 'facture']); // Eager load

        return [
            'vente' => $vente
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Relation::make('vente.id_client')
                    ->title('Client')
                    ->fromModel(Client::class, 'nom')
                    ->required()
                    ->value($this->vente->id_client),

                Switcher::make('vente.tva')
                    ->title('Appliquer la TVA')
                    ->sendTrueOrFalse()
                    ->value($this->vente->tva),

                Input::make('vente.facture.numero_facture')
                    ->title('Numéro de Facture')
                    ->disabled(), 

                Select::make('vente.type_document')
                    ->title('Type de document')
                    ->options([
                        'devis' => 'Devis',
                        'facture' => 'Facture',
                        'avoir' => 'Avoir',
                    ])
                    ->required()
                    ->help('Choisissez le type de document')
                    ->value(optional($this->vente->facture)->type_document), // ← Ajouter cette ligne
                

            ]),

            Layout::rows([
                Select::make('produits[]')
                    ->title('Produits')
                    ->fromModel(Product::class, 'nom')
                    ->multiple()
                    ->help('Sélectionnez les produits de la vente')
                    ->value($this->vente->details->pluck('id_product')->toArray()), // ← Afficher les produits associés

                Input::make('quantites')
                    ->title('Quantités (séparées par virgule)')
                    ->placeholder('Exemple : 2,3,1')
                    ->help('Quantité correspondante à chaque produit sélectionné')
                    ->value($this->vente->details->pluck('quantite_vendue')->implode(',')), 

                
            ])
        ];
    }

    public function commandBar(): array
    {
        return [
            \Orchid\Screen\Actions\Button::make('Enregistrer')
                ->method('save')
                ->icon('check')
        ];
    }

    public function update(Request $request, $id)
        {
            $request->validate([
                'vente.id_client' => 'required|exists:clients,id_client',
                'vente.produits' => 'required|array',
                'vente.produits.*' => 'exists:products,id_product',
                'vente.quantites' => 'required|string',
                'vente.tva' => 'nullable|boolean',
                'vente.type_document' => 'required|string|in:devis,facture,avoir', // Validation du type de document
            ]);

            // Récupérer les données de la vente
            $venteData = $request->input('vente');
            $quantites = array_map('intval', explode(',', $venteData['quantites']));
            $typeDocument = $venteData['type_document'];

            if (count($venteData['produits']) !== count($quantites)) {
                Toast::error('Nombre de produits et quantités incompatible');
                return back();
            }

            try {
                DB::beginTransaction();

                // Mise à jour de la facture avec le nouveau type de document
                $facture = Facture::findOrFail($venteData['id_facture']);
                $facture->update([
                    'type_document' => $typeDocument,
                    'statut' => $typeDocument === 'devis' ? 'En attente' : 'Validé', // Modifier le statut selon le type de document
                ]);

                // Mise à jour de la vente
                $vente = Ventes::findOrFail($venteData['id_vente']);
                $vente->update([
                    'id_client' => $venteData['id_client'],
                    'tva' => $venteData['tva'] ?? false,
                ]);

                $applyTva = $venteData['tva'] ?? false;

                // Mise à jour des détails de vente et du stock
                foreach ($venteData['produits'] as $index => $idProduct) {
                    $product = Product::findOrFail($idProduct);
                    $quantite = $quantites[$index];

                    $prixUnitaire = $product->prix_unitaire;
                    $prixAvecTva = $applyTva ? $prixUnitaire * 1.18 : $prixUnitaire;
                    $prixTotal = $quantite * $prixAvecTva;

                    // Mise à jour des détails de vente
                    DetailVente::where('id_vente', $vente->id_vente)
                        ->where('id_product', $idProduct)
                        ->update([
                            'quantite_vendue' => $quantite,
                            'prix_total' => $prixTotal,
                            'date_vente' => now(),
                        ]);

                    // Mise à jour du stock
                    $product->decrement('quantite_stock', $quantite);
                }

                DB::commit();
                Toast::success('Vente et document mis à jour avec succès !');
                return back();

            } catch (\Exception $e) {
                DB::rollBack();
                Toast::error('Erreur : ' . $e->getMessage());
                return back();
            }
        }

}
