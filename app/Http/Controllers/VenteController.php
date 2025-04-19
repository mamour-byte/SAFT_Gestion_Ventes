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
    public function removeFromVentesTable(Request $request)
    {
        $request->validate(['id_vente' => 'required|exists:ventes,id_vente']);

        try {
            DB::beginTransaction();

            $vente = Ventes::findOrFail($request->id_vente);

            foreach ($vente->details as $detail) {
                $product = Product::find($detail->id_product);
                if ($product) {
                    $product->increment('quantite_stock', $detail->quantite_vendue);
                }
            }

            // Supprimer les détails, la vente et la facture liée
            $vente->details()->delete();
            $vente->delete();
            $vente->facture?->delete();

            DB::commit();

            return back()->with('success', 'Vente supprimée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }
}
