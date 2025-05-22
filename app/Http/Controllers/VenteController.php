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
     * Générer un nouveau numéro de facture au format INV-000001
     */
    private function generateNumeroFacture()
    {
        $lastFacture = Facture::orderBy('numero_facture', 'desc')->first();

        if ($lastFacture && preg_match('/INV-(\d+)/', $lastFacture->numero_facture, $matches)) {
            $lastNumber = (int) $matches[1];
        } else {
            $lastNumber = 0;
        }

        $newNumber = $lastNumber + 1;
        return 'INV-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

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

                // Générer un numéro de document pour tous les types (facture, devis, avoir)
                $numeroFacture = $this->generateNumeroFacture(); // On génère un numéro pour tous les types de documents

                // Création de la facture, devis, ou avoir
                $facture = Facture::create([
                    'id_client' => $venteData['id_client'],
                    'id_user' => $request->user()->id,
                    'type_document' => $typeDocument,
                    'tva' => $venteData['tva'] ?? false,
                    'numero_facture' => $numeroFacture,
                    'statut' => match ($typeDocument) {
                        'devis', 'avoir' => 'En attente',
                        'facture' => 'Validé',
                        default => 'En attente',
                    },

                ]);

                // Création de la vente
                $vente = Ventes::create([
                    'id_client' => $venteData['id_client'],
                    'id_user' => $request->user()->id,
                    'id_facture' => $facture->id_facture,
                    'tva' => $venteData['tva'] ?? false,
                ]);

                $applyTva = $venteData['tva'] ?? false;

                // Ajout des détails de vente et mise à jour du stock
                foreach ($venteData['produits'] as $index => $idProduct) {
                    $product = Product::findOrFail($idProduct);
                    $quantite = $quantites[$index];

                    $prixUnitaire = $product->prix_unitaire;
                    $prixAvecTva = $applyTva ? $prixUnitaire * 1.18 : $prixUnitaire;
                    $prixTotal = $quantite * $prixAvecTva;

                    DetailVente::create([
                        'id_vente' => $vente->id_vente,
                        'id_product' => $idProduct,
                        'quantite_vendue' => $quantite,
                        'prix_total' => $prixTotal,
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
    public function destroy($id)
        {
            $vente = Ventes::with(['facture', 'details'])->findOrFail($id);

            try {
                $vente->details()->delete();
                if ($vente->facture) {
                    $vente->facture()->delete();
                }
                $vente->delete();

                Toast::info('Vente supprimée avec succès.');
            } catch (\Exception $e) {
                report($e);
                Toast::error('Erreur lors de la suppression de la vente.');
            }

            return redirect()->back();
        }



    public function transformQuoteToInvoice($id)
    {
        $facture = Facture::findOrFail($id);

        if ($facture->type_document !== 'devis') {
            Toast::error('Ce document n\'est pas un devis.');
            return back();
        }

        $facture->update([
            'type_document' => 'facture',
            'statut' => 'Validé',
            'numero_facture' => $this->generateNumeroFacture(),
        ]);

        Toast::success('Devis transformé en facture avec succès !');
        return back();
    }


}
