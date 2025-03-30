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

class VenteController extends Controller
{
    public function addToVentesTable(Request $request)
        {
            // Valider la structure de base
            $request->validate([
                'vente.id_client' => 'required|exists:clients,id_client',
                'vente.produits' => 'required|array',
                'vente.produits.*' => 'exists:products,id_product',
                'vente.quantites' => 'required|string',
                'vente.tva' => 'nullable|boolean',
            ]);
        
            $venteData = $request->input('vente');
            $quantites = array_map('intval', explode(',', $venteData['quantites']));
        
            // Vérification que le nombre de produits = nombre de quantités
            if (count($venteData['produits']) !== count($quantites)) {
                return redirect()->back()
                       ->with('error', 'Le nombre de produits doit correspondre au nombre de quantités')
                       ->withInput();
            }

            // Validation supplémentaire
            if (empty($venteData['produits']) || count($venteData['produits']) !== count($quantites)) {
                return back()->with('error', 'Nombre de produits et quantités incompatible');
            }

            // Construction du tableau de produits
            $produitsDetails = [];
            foreach ($venteData['produits'] as $index => $idProduct) {
                $product = Product::find($idProduct);
                if (!$product) {
                    return back()->with('error', "Produit ID $idProduct introuvable");
                }

                $produitsDetails[] = [
                    'id_product' => $product->id_product,
                    'nom' => $product->nom,
                    'quantite' => $quantites[$index] ?? 0,
                    'prix_unitaire' => $product->prix_unitaire,
                ];
            }

            // Stockage en session
            $ventes = session()->get('ventes_temp', []);
            $ventes[] = [
                'id_client' => $venteData['id_client'],
                'produits' => $produitsDetails,
                'tva' => $venteData['tva'] ?? false,
            ];
            
            session()->put('ventes_temp', $ventes);

            return redirect()->back()->with('success', 'Vente ajoutée');
        }




    public function removeFromVentesTable(Request $request)
    {
        $index = $request->validate(['index' => 'required|integer',]);
        $ventes = session()->get('ventes_temp', []);
        
        if (!isset($ventes[$index])) {
            return back()->with('error', 'Vente introuvable');
        }
        
        unset($ventes[$index]);
        session()->put('ventes_temp', array_values($ventes));
        
        return redirect()->back()->with('success', 'Vente supprimée du tableau temporaire');
    }

    public function saveVentes(Request $request)
    {
        $ventes = session()->get('ventes_temp', []);
        
        if (empty($ventes)) {
            return back()->with('error', 'Aucune vente à enregistrer');
        }

        try {
            DB::beginTransaction();
            
            foreach ($ventes as $vente) {
                // Création de la vente principale
                $newVente = Ventes::create([
                    'id_client' => $vente['id_client'],
                    'id_user' => $request->user()->id , // 'id_user' => auth()->id(),
                    'date_vente' => now(),
                ]);

                // Ajout des détails de vente
                foreach ($vente['produits'] as $produit) {
                    DetailVente::create([
                        'id_vente' => $newVente->id_vente,
                        'id_product' => $produit['id_product'],
                        'quantite_vendue' => $produit['quantite_vendue'],
                        'prix_total' => $produit['prix_total'],
                    ]);

                    // Mise à jour du stock
                    $product = Product::find($produit['id_product']);
                    $product->quantite_stock -= $produit['quantite_vendue'];
                    $product->save();
                }
            }
            
            DB::commit();
            session()->forget('ventes_temp');
            
            return redirect()->back()->with('success', count($ventes) . ' ventes enregistrées avec succès!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }
}