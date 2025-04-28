<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Ventes;
use App\Models\DetailVente;
use App\Models\Facture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Orchid\Support\Facades\Toast;

class VenteController extends Controller
{
    /**
     * Ajouter une vente et mettre à jour le stock
     */
    public function addToVentesTable(Request $request)
    {
        $request->validate([
            'vente.id_client' => 'required|exists:clients,id_client',
            'vente.produits' => 'required|array',
            'vente.produits.*' => 'exists:products,id_product',
            'vente.quantites' => 'required|string',
            'vente.tva' => 'nullable|boolean',
            'vente.type_document' => 'required|string|in:facture,devis,avoir',
        ]);

        $venteData = $request->input('vente');
        $quantites = array_map('intval', explode(',', $venteData['quantites']));
        $typeDocument = $venteData['type_document'];

        if (count($venteData['produits']) !== count($quantites)) {
            Toast::error('Nombre de produits et quantités incompatible');
            return back();
        }

        try {
            DB::beginTransaction();

            // Création de la facture ou devis
            $facture = Facture::create([
                'id_client' => $venteData['id_client'],
                'id_user' => $request->user()->id,
                'type_document' => $typeDocument,
                'statut' => $typeDocument === 'devis' ? 'En attente' : 'Validé',
            ]);

            // Création de la vente
            $vente = Ventes::create([
                'id_client' => $venteData['id_client'],
                'id_user' => $request->user()->id,
                'id_facture' => $facture->id_facture,
                'tva' => $venteData['tva'] ?? false,
            ]);

            // Ajout des détails et mise à jour du stock
            foreach ($venteData['produits'] as $index => $idProduct) {
                $product = Product::findOrFail($idProduct);
                $quantite = $quantites[$index];

                DetailVente::create([
                    'id_vente' => $vente->id_vente,
                    'id_product' => $idProduct,
                    'quantite_vendue' => $quantite,
                    'prix_total' => $quantite * $product->prix_unitaire,
                    'date_vente' => now(),
                ]);

                $product->decrement('quantite_stock', $quantite);
            }

            DB::commit();
            Toast::success('Vente et document enregistrés avec succès !');
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            Toast::error('Erreur : ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Supprimer une vente et mettre à jour le stock
     */
    public function destroy(Product $product)
        {
            $product->delete();

            return redirect()->route('platform.product.list')
                ->with('success', 'Produit supprimé avec succès');
        }
    


    /**
     * Update the specified product.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product.nom' => 'required|string|max:255',
            'product.description' => 'required|string',
            'product.prix_unitaire' => 'required|numeric|min:0',
            'product.quantite_stock' => 'required|integer|min:0',
        ]);

        $product->update($request->input('product'));

        return redirect()->route('platform.product')
            ->with('success', 'Produit mis à jour avec succès');
    }
}
