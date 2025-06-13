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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VentesExport;

class VenteController extends Controller
{
    /**
     * Générer un numéro unique par type de document (ex: FAC-202506-0001, DEVIS-202506-0001, AVO-202506-0001)
     */
    private function generateNumeroDocument(string $type)
    {
        $prefix = match ($type) {
            'facture' => 'FAC',
            'devis' => 'DEVIS',
            'avoir' => 'AVO',
            default => 'DOC',
        };

        $base = $prefix . '-' . now()->format('Ym');

        $count = Facture::where('type_document', $type)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;

        return sprintf('%s-%04d', $base, $count);
    }

    /**
     * Ajouter une vente et créer une facture / devis / avoir
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
            'vente.numeroCommande' => 'nullable|string|max:255', 
            'vente.dateLivraison' => 'nullable|date',  
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

            // Numéro unique par type
            $numeroFacture = $this->generateNumeroDocument($typeDocument);

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

            $vente = Ventes::create([
                'id_client' => $venteData['id_client'],
                'id_user' => $request->user()->id,
                'id_facture' => $facture->id_facture,
                'tva' => $venteData['tva'] ?? false,
            ]);

            $applyTva = $venteData['tva'] ?? false;
            $numeroBonLivraison = 'BL-'  . now()->format('Ym') .'-'. str_pad(DetailVente::count() + 1, 6, '0', STR_PAD_LEFT) ;

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
                    'numeroCommande' => $venteData['numeroCommande'] ?? null, 
                    'dateLivraison' => $venteData['dateLivraison'] ?? null, 
                    'numeroBonLivraison' => $numeroBonLivraison,
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
     * Mise à jour produit
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product.nom' => 'required|string|max:255',
            'product.description' => 'required|string',
            'product.prix_unitaire' => 'required|numeric|min:0',
            'product.quantite_stock' => 'integer|min:0',
        ]);

        $product->update($request->input('product'));

        return redirect()->route('platform.product')
            ->with('success', 'Produit mis à jour avec succès');
    }

    

    /**
     * Transformer un devis en facture
     */
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
            'numero_facture' => $this->generateNumeroDocument('facture'),
        ]);

        Toast::success('Devis transformé en facture avec succès !');
        return back();
    }

    public function exportExcel($type)
    {
        $fileName = "ventes_" . $type . "_" . now()->format('Y_m_d_His') . ".xlsx";
        return Excel::download(new VentesExport($type), $fileName);
    }
}
