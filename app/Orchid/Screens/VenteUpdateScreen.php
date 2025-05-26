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
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Toast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VenteUpdateScreen extends Screen
{
    public $name = 'Modifier la Vente';
    public $description = 'Éditez les informations de la vente sélectionnée';
    public $vente;

    public function query(Ventes $vente): array
    {
        $vente->load(['client', 'details.product', 'facture']);
        $this->vente = $vente;

        return [
            'vente' => $vente,
        ];
    }

    public function commandBar(): array
    {
        return [
            Button::make('Enregistrer')
                ->method('update')
                ->icon('check'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('vente.id_vente')->type('hidden')->value($this->vente->id_vente),
                Input::make('vente.id_facture')->type('hidden')->value(optional($this->vente->facture)->id_facture),

                Relation::make('vente.id_client')
                    ->title('Client')
                    ->fromModel(Client::class, 'nom')
                    ->required()
                    ->value($this->vente->id_client),

                Switcher::make('vente.tva')
                    ->title('Appliquer la TVA')
                    ->sendTrueOrFalse()
                    ->value(optional($this->vente->facture)->tva),  // ← TVA est dans facture, pas vente

                Input::make('vente.facture.numero_facture')
                    ->title('Numéro de Facture')
                    ->disabled()
                    ->value(optional($this->vente->facture)->numero_facture),

                Select::make('vente.type_document')
                    ->title('Type de document')
                    ->options([
                        'devis' => 'Devis',
                        'facture' => 'Facture',
                        'avoir' => 'Avoir',
                    ])
                    ->required()
                    ->help('Choisissez le type de document')
                    ->value(optional($this->vente->facture)->type_document),
            ]),

            Layout::rows([
                Select::make('produits[]')
                    ->title('Produits')
                    ->fromModel(Product::class, 'nom')
                    ->multiple()
                    ->help('Sélectionnez les produits de la vente')
                    ->value($this->vente->details->pluck('id_product')->toArray()),

                Input::make('quantites')
                    ->title('Quantités (séparées par virgule)')
                    ->placeholder('Exemple : 2,3,1')
                    ->help('Quantité correspondante à chaque produit sélectionné')
                    ->value($this->vente->details->pluck('quantite_vendue')->implode(',')),
            ]),
        ];
    }

    public function update(Request $request)
{
    $request->validate([
        'vente.id_client' => 'required|exists:clients,id_client',
        'vente.id_vente' => 'required|exists:ventes,id_vente',
        'vente.id_facture' => 'required|exists:facture,id_facture',

        'produits' => 'required|array',
        'produits.*' => 'exists:products,id_product',

        'quantites' => 'required|string',

        'vente.tva' => 'nullable|boolean',
        'vente.type_document' => 'required|string|in:devis,facture,avoir',
    ]);

    $id_client = $request->input('vente.id_client');
    $tva = $request->boolean('vente.tva');
    $type_document = $request->input('vente.type_document');
    $produits = $request->input('produits');
    $quantites = array_map('intval', explode(',', $request->input('quantites')));

    $id_vente = $request->input('vente.id_vente');
    $id_facture = $request->input('vente.id_facture');

    if (count($produits) !== count($quantites)) {
        Toast::error('Nombre de produits et quantités incompatibles.');
        return back();
    }

    try {
        DB::beginTransaction();

        $vente = Ventes::with('details')->findOrFail($id_vente);
        $facture = Facture::findOrFail($id_facture);

        // Mise à jour de la facture
        $facture->update([
            'type_document' => $type_document,
            'tva' => $tva,
            'statut' => $type_document === 'devis' ? 'En attente' : ($type_document === 'avoir' ? 'En attente' : 'Validé'),
        ]);

        // Mise à jour du client de la vente
        $vente->update([
            'id_client' => $id_client,
        ]);

        $products = Product::whereIn('id_product', $produits)->get()->keyBy('id_product');

        // Synchronisation des détails de vente
        $existingDetails = $vente->details->keyBy('id_product');

        $nouveauxProduits = collect();

        foreach ($produits as $index => $idProduct) {
            $product = $products[$idProduct] ?? null;
            $quantite = $quantites[$index];

            if (!$product) {
                throw new \Exception("Produit avec l'ID {$idProduct} introuvable.");
            }

            $prixUnitaire = $product->prix_unitaire;
            $prixAvecTva = $tva ? $prixUnitaire * 1.18 : $prixUnitaire;
            $prixTotal = $quantite * $prixAvecTva;

            // Si le détail existe, réajuster le stock
            $ancienDetail = $existingDetails->get($idProduct);
            if ($ancienDetail) {
                $diff = $quantite - $ancienDetail->quantite_vendue;
                $product->decrement('quantite_stock', $diff);
                $ancienDetail->update([
                    'quantite_vendue' => $quantite,
                    'prix_total' => $prixTotal,
                    'date_vente' => now(),
                ]);
            } else {
                $product->decrement('quantite_stock', $quantite);
                DetailVente::create([
                    'id_vente' => $vente->id_vente,
                    'id_product' => $idProduct,
                    'quantite_vendue' => $quantite,
                    'prix_total' => $prixTotal,
                    'date_vente' => now(),
                ]);
            }

            $nouveauxProduits->push($idProduct);
        }

        // Supprimer les anciens détails non sélectionnés cette fois
        $toDelete = $existingDetails->keys()->diff($nouveauxProduits);
        DetailVente::where('id_vente', $vente->id_vente)
            ->whereIn('id_product', $toDelete)
            ->delete();

        DB::commit();
        Toast::success('Vente mise à jour avec succès.');
        return redirect()->route('platform.ventes');

    } catch (\Exception $e) {
        DB::rollBack();
        Toast::error('Erreur : ' . $e->getMessage());
        return back();
    }
}

}
