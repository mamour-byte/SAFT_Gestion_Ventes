<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ventes;
use App\Models\DetailVente;
use App\Models\Product;
use Orchid\Support\Facades\Toast;

class VenteController extends Controller
    {
        public function saveVente(Request $request)
        {
            // Valider les données
            $request->validate([
                'id_client' => 'required|exists:clients,id',
                'produits' => 'required|array',
                'produits.*.id' => 'required|exists:produits,id_product',
                'produits.*.quantite' => 'required|integer|min:1',
                'produits.*.prix' => 'required|numeric|min:0',
                'status' => 'nullable|boolean',
            ]);

            // Créer la vente
            $vente = Ventes::create([
                // 'id_user' => auth()->id(),
                'id_client' => $request->id_client,
            ]);

            // Insérer les détails de la vente
            foreach ($request->produits as $produit) {
                $prixTotal = $produit['prix'] * $produit['quantite'];

                // Ajouter la TVA si cochée
                if ($request->status) {
                    $prixTotal += $prixTotal * 0.18; // Ajoute 18% de TVA
                }

                DetailVente::create([
                    'id_vente' => $vente->id_vente,
                    'id_produit' => $produit['id'],
                    'quantite_vendue' => $produit['quantite'],
                    'prix_total' => $prixTotal,
                ]);

                // Mettre à jour le stock
                $produitModel = Product::find($produit['id']);
                $produitModel->decrement('quantite_stock', $produit['quantite']);
            }

            // Afficher un message de succès
            Toast::info('Vente enregistrée avec succès, TVA incluse si applicable !');

            return response()->json(['message' => 'Vente enregistrée avec succès']);
        }




        /**
     * Ajouter un produit au tableau.
     */
    public function addToTable(Request $request)
{
    $data = $request->validate([
        'id_client' => 'required|exists:clients,id_client',
        'produits' => 'required|array',
        'produits.*' => 'exists:products,id_product',
        'produits_quantites' => 'required|string',
    ]);

    // Récupérer les produits et leurs quantités
    $produits = Product::whereIn('id_product', $data['produits'])->get();
    $quantites = explode(',', $data['produits_quantites']);

    // Charger les produits déjà dans la session
    $produitsAjoutes = session('produitsAjoutes', []);

    foreach ($produits as $index => $produit) {
        $quantite = isset($quantites[$index]) ? (int) $quantites[$index] : 1;
        $prixUnitaire = (float) $produit->prix;
        $total = $quantite * $prixUnitaire;

        // Vérifier si le produit existe déjà dans le tableau
        $produitTrouve = false;
        foreach ($produitsAjoutes as &$produitAjoute) {
            if ($produitAjoute['id'] === $produit->id_product) {
                // Mise à jour de la quantité et recalcul du total
                $produitAjoute['quantite'] += $quantite;
                $produitAjoute['total'] = $produitAjoute['quantite'] * $prixUnitaire;
                $produitTrouve = true;
                break;
            }
        }

        // Si le produit n'existe pas encore, on l'ajoute
        if (!$produitTrouve) {
            $produitsAjoutes[] = [
                'id' => $produit->id_product,
                'nom' => $produit->nom,
                'quantite' => $quantite,
                'prix_unitaire' => $prixUnitaire,
                'total' => $total,
            ];
        }
    }

    // Mettre à jour la session
    session(['produitsAjoutes' => $produitsAjoutes]);

    // Vérifier si la session est bien mise à jour
    dd(session('produitsAjoutes'));

    return redirect()->back()->with('success', 'Produits ajoutés ou mis à jour dans le tableau.');
}

}
