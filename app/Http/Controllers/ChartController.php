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
                return \DB::table('ventes')
                    ->join('details_ventes', 'ventes.id_vente', '=', 'details_ventes.id_vente')
                    ->selectRaw('DATE(details_ventes.date_vente) as date, COUNT(ventes.id_vente) as total_ventes, SUM(details_ventes.quantite_vendue) as total_quantite')
                    ->whereMonth('details_ventes.date_vente', 4)
                    ->whereYear('details_ventes.date_vente', 2025)
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->get();
            }

        public function ventesParMois($startDate = null, $endDate = null)
            {
                $query = Ventes::query()
                    ->selectRaw('DATE_FORMAT(details_ventes.date_vente, "%Y-%m") as mois, SUM(details_ventes.quantite_vendue) as total_ventes')
                    ->join('details_ventes', 'ventes.id_vente', '=', 'details_ventes.id_vente');
            
                $this->applyDateFilter($query, $startDate, $endDate);
            
                return $query->groupBy('mois')
                    ->orderBy('mois', 'ASC')
                    ->get();
            }

}