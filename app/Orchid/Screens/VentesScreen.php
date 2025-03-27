<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Illuminate\Http\Request;
use App\Orchid\Layouts\TabsNav\NouvVentesRow;
use App\Orchid\Layouts\TabsNav\HistVentesRow;
use Orchid\Screen\Actions\Button;
use App\Models\Product;

class VentesScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'produitsAjoutes' => session('produitsAjoutes', []), // Récupère les produits ajoutés depuis la session
        ];
    }

    /**
     * Display screen name.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Nouvelle Vente';
    }

    /**
     * Button commands.
     *
     * @return array
     */
    public function commandBar(): array
    
    {
    return [

        Button::make('Enregistrer la vente')
            ->route('ventes.save') 
            ->class('btn btn-primary'),
        ];
    }

    /**
     * Views.
     *
     * @return array
     */
    public function layout(): array
    {
        return [
            NouvVentesRow::class,
            HistVentesRow::class,
        ];
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

        // Charger les produits ajoutés dans la session
        $produitsAjoutes = session('produitsAjoutes', []);

        foreach ($produits as $index => $produit) {
            $produitsAjoutes[] = [
                'nom' => $produit->nom,
                'quantite' => $quantites[$index] ?? 1,
                'prix_unitaire' => $produit->prix, // Supposez que chaque produit a un champ `prix`
                'total' => ($quantites[$index] ?? 1) * $produit->prix,
            ];
        }

        // Sauvegarder les produits dans la session
        session(['produitsAjoutes' => $produitsAjoutes]);

        return redirect()->back()->with('success', 'Produits ajoutés au tableau.');
    }

    /**
     * Enregistrer la vente.
     */
    public function saveVente(Request $request)
    {
        // Logique pour enregistrer la vente
        session()->forget('produitsAjoutes'); // Nettoyer la session après l'enregistrement
        return redirect()->back()->with('success', 'Vente enregistrée avec succès.');
    }
}