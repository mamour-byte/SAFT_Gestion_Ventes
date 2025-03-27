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
    }