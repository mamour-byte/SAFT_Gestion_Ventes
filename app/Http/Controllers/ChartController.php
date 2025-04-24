<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vente;
use App\Models\Produit;

class ChartController extends Controller
{
    /**
     * Filtre les ventes par date.
     */
    private function applyDateFilter($query, $startDate, $endDate)
    {
        return $query->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                     ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));
    }

    public function ventesParProduit($startDate = null, $endDate = null)
    {
        return Produit::query()
            ->withSum(['ventes as total_ventes' => function($query) use ($startDate, $endDate) {
                $this->applyDateFilter($query, $startDate, $endDate);
            }], 'quantite_vendue')
            ->whereHas('ventes', function($query) use ($startDate, $endDate) {
                $this->applyDateFilter($query, $startDate, $endDate);
            })
            ->orderBy('total_ventes', 'DESC')
            ->get(['id', 'nom', 'total_ventes']);
    }

    public function ventesParClient()
    {
        return Vente::withSum('produits as total_ventes', 'quantite_vendue')
            ->groupBy('id_client')
            ->having('total_ventes', '>', 0)
            ->orderBy('total_ventes', 'DESC')
            ->get(['id_client', 'total_ventes']);
    }

    public function ventesParDate()
    {
        return Vente::selectRaw('DATE(date_livraison) as date, SUM(quantite_vendue) as total_ventes')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();
    }

    public function ventesParMois()
    {
        return Vente::selectRaw('DATE_FORMAT(date_livraison, "%Y-%m") as mois, SUM(quantite_vendue) as total_ventes')
            ->groupBy('mois')
            ->orderBy('mois', 'ASC')
            ->get();
    }

    public function ventesParAnnee()
    {
        return Vente::selectRaw('YEAR(date_livraison) as annee, SUM(quantite_vendue) as total_ventes')
            ->groupBy('annee')
            ->orderBy('annee', 'ASC')
            ->get();
    }

    public function ventesParStatut()
    {
        return Vente::selectRaw('statut, SUM(quantite_vendue) as total_ventes')
            ->groupBy('statut')
            ->orderBy('total_ventes', 'DESC')
            ->get();
    }
}