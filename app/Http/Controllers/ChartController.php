<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vente;
use App\Models\Produit;
use App\Models\Ventes;

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
            $query = Ventes::query()
                ->selectRaw('products.nom, SUM(details_ventes.quantite_vendue) as total_ventes')
                ->join('details_ventes', 'ventes.id_vente', '=', 'details_ventes.id_vente')
                ->join('products', 'details_ventes.id_product', '=', 'products.id_product');
        
            $this->applyDateFilter($query, $startDate, $endDate);
        
            return $query->groupBy('products.nom')
                ->orderBy('total_ventes', 'DESC')
                ->get();
        }

    public function ventesParClient($startDate = null, $endDate = null)
        {
            $query = Ventes::query()
                ->selectRaw('clients.nom as client, SUM(details_ventes.quantite_vendue) as total_ventes')
                ->join('clients', 'ventes.id_client', '=', 'clients.id_client')
                ->join('details_ventes', 'ventes.id_vente', '=', 'details_ventes.id_vente');
        
            $this->applyDateFilter($query, $startDate, $endDate);
        
            return $query->groupBy('clients.nom')
                ->having('total_ventes', '>', 0)
                ->orderBy('total_ventes', 'DESC')
                ->get();
        }

        public function ventesParJourDuMois()
        {
            $query = Ventes::query()
                ->selectRaw('DATE(ventes.created_at) as date, COUNT(ventes.id_vente) as total_ventes')
                ->whereMonth('ventes.created_at', now()->month)
                ->whereYear('ventes.created_at', now()->year)
                ->groupBy('date')
                ->orderBy('date', 'ASC');
            $query->join('details_ventes', 'ventes.id_vente', '=', 'details_ventes.id_vente')
                ->selectRaw('DATE(ventes.created_at) as date, SUM(details_ventes.quantite_vendue) as total_ventes')
                ->groupBy('date')
                ->orderBy('date', 'ASC');
            return $query->get();
        }

        public function ventesParMois($startDate = null, $endDate = null)
        {
            $query = Ventes::query()
                ->selectRaw('DATE_FORMAT(ventes.created_at, "%Y-%m") as mois, SUM(details_ventes.quantite_vendue) as total_ventes')
                ->join('details_ventes', 'ventes.id_vente', '=', 'details_ventes.id_vente');
        
            $this->applyDateFilter($query, $startDate, $endDate);
        
            return $query->groupBy('mois')
                ->orderBy('mois', 'ASC')
                ->get();
        }

}