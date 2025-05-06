<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vente;
use App\Models\Ventes;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{

    private function applyDateFilter($query, $startDate, $endDate)
    {
        return $query->when($startDate, fn($q) => $q->whereDate('ventes.created_at', '>=', $startDate))
                    ->when($endDate, fn($q) => $q->whereDate('ventes.created_at', '<=', $endDate));
    }

    private function setDefaultDateRange(&$startDate, &$endDate)
    {
        if (!$startDate) {
            $startDate = now()->startOfMonth()->toDateString();
        }
        if (!$endDate) {
            $endDate = now()->endOfMonth()->toDateString();
        }
    }

    public function ventesParProduit($startDate = null, $endDate = null)
    {
        $this->setDefaultDateRange($startDate, $endDate);

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
        $this->setDefaultDateRange($startDate, $endDate);

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
                return DB::table('ventes')
                    ->selectRaw('DATE(created_at) as date, COUNT(id_vente) as total_ventes')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->groupBy('date')
                    ->orderBy('date', 'ASC')
                    ->get();
            }

    public function ventesParMois($startDate = null, $endDate = null)
    {
        $this->setDefaultDateRange($startDate, $endDate);

        $query = Ventes::query()
            ->selectRaw('DATE_FORMAT(details_ventes.date_vente, "%Y-%m") as mois, SUM(details_ventes.quantite_vendue) as total_ventes')
            ->join('details_ventes', 'ventes.id_vente', '=', 'details_ventes.id_vente');

        $this->applyDateFilter($query, $startDate, $endDate);

        return $query->groupBy('mois')
            ->orderBy('mois', 'ASC')
            ->get();
    }

    public function meilleureVenteDuMois($startDate = null, $endDate = null)
    {
        $this->setDefaultDateRange($startDate, $endDate);

        $query = Ventes::query()
            ->selectRaw('products.nom as produit, SUM(details_ventes.quantite_vendue) as total_ventes')
            ->join('details_ventes', 'ventes.id_vente', '=', 'details_ventes.id_vente')
            ->join('products', 'details_ventes.id_product', '=', 'products.id_product');

        $this->applyDateFilter($query, $startDate, $endDate);

        return $query->groupBy('produit')
            ->orderBy('total_ventes', 'DESC')
            ->first();
    }

    public function meilleurClientDuMois($startDate = null, $endDate = null)
    {
        $this->setDefaultDateRange($startDate, $endDate);

        $query = Ventes::query()
            ->selectRaw('clients.nom as client, SUM(details_ventes.quantite_vendue) as total_ventes')
            ->join('clients', 'ventes.id_client', '=', 'clients.id_client')
            ->join('details_ventes', 'ventes.id_vente', '=', 'details_ventes.id_vente');

        $this->applyDateFilter($query, $startDate, $endDate);

        return $query->groupBy('client')
            ->orderBy('total_ventes', 'DESC')
            ->first();
    }

    public function NombredeFacturesDuMois($mois = null, $annee = null)
        {
            $mois = $mois ?? now()->month;
            $annee = $annee ?? now()->year;

            return Ventes::whereMonth('created_at', $mois)
                        ->whereYear('created_at', $annee)
                        ->count();
        }

        public function totalGenereDuMois()
            {
                return DB::table('details_ventes')
                    ->selectRaw('SUM(prix_total) as total_ventes')
                    ->whereMonth('date_vente', now()->month)
                    ->whereYear('date_vente', now()->year)
                    ->value('total_ventes') ?? 0; // Retxourne 0 si aucune vente
            }
}
