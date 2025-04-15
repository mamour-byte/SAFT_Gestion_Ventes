<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Ventes;
use App\Models\DetailVente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Actions\Button;
use App\Models\Facture;
use Orchid\Support\Facades\Toast;

class VenteController extends Controller
{
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // Ajouter une vente et mettre à jour le stock
    public function addToVentesTable(Request $request)
        {
            $request->validate([
                'vente.id_client' => 'required|exists:clients,id_client',
                'vente.produits' => 'required|array',
                'vente.produits.*' => 'exists:products,id_product',
                'vente.quantites' => 'required|string',
                'vente.tva' => 'nullable|boolean',
            ]);

            $venteData = $request->input('vente');
            $quantites = array_map('intval', explode(',', $venteData['quantites']));

            if (count($venteData['produits']) !== count($quantites)) {
                Toast::error('Nombre de produits et quantités incompatible');
                return back(); // <- retourne juste une redirection
            }

            try {
                DB::beginTransaction();

                $facture = Facture::create([
                    'id_client' => $venteData['id_client'],
                    'id_user' => $request->user()->id,
                    'statut' => 'Validé',
                ]);

                $newVente = Ventes::create([
                    'id_client' => $venteData['id_client'],
                    'id_user' => $request->user()->id,
                    'date_vente' => now(),
                    'tva' => $venteData['tva'] ?? false,
                    'id_facture' => $facture->id_facture,
                ]);

                foreach ($venteData['produits'] as $index => $idProduct) {
                    $product = Product::findOrFail($idProduct);
                    $quantite = $quantites[$index];

                    DetailVente::create([
                        'id_vente' => $newVente->id_vente,
                        'id_product' => $idProduct,
                        'quantite_vendue' => $quantite,
                        'prix_total' => $quantite * $product->prix_unitaire,
                    ]);

                    $product->decrement('quantite_stock', $quantite);
                }

                DB::commit();
                Toast::success('Vente et facture enregistrées avec succès !');
                return back();

            } catch (\Exception $e) {
                DB::rollBack();
                Toast::error('Erreur : ' . $e->getMessage());
                return back();
            }
        }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // Supprimer une vente et mettre à jour le stock
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

                // Supprimer les détails puis la vente
                $vente->details()->delete();
                $vente->delete();

                DB::commit();

                return back()->with('success', 'Vente supprimée avec succès.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Erreur : ' . $e->getMessage());
            }
        }


    
}