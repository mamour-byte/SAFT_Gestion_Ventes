<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    private function filterFacturesValide($query)
    {
        return $query->join('facture', 'facture.id_facture', '=', 'ventes.id_facture')
                     ->where('facture.type_document', 'facture')
                     ->where('facture.statut', 'validé');
    }

    public function ventesParProduit($startDate = null, $endDate = null)
    {
        $this->setDefaultDateRange($startDate, $endDate);

        $query = Ventes::query()
            ->selectRaw('products.nom, SUM(details_ventes.quantite_vendue) as total_ventes')
            ->join('details_ventes', 'ventes.id_vente', '=', 'details_ventes.id_vente')
            ->join('products', 'details_ventes.id_product', '=', 'products.id_product');

        $query = $this->filterFacturesValide($query);
        $this->applyDateFilter($query, $startDate, $endDate);

        return $query->groupBy('products.nom')
                     ->orderBy('total_ventes', 'DESC')
                     ->get();
    }

    public function ventesParClient($startDate = null, $endDate = null)
        {
            $this->setDefaultDateRange($startDate, $endDate);

            $query = Ventes::query()
                ->selectRaw('clients.nom as client, SUM(details_ventes.prix_total) as total_ca')
                ->join('clients', 'ventes.id_client', '=', 'clients.id_client')
                ->join('details_ventes', 'ventes.id_vente', '=', 'details_ventes.id_vente');

            $query = $this->filterFacturesValide($query);
            $this->applyDateFilter($query, $startDate, $endDate);

            return $query->groupBy('client')
                        ->orderByDesc('total_ca')
                        ->get();
        }


    public function ventesParJourDuMois()
    {
        return DB::table('ventes')
            ->join('facture', 'facture.id_facture', '=', 'ventes.id_facture')
            ->selectRaw('DATE(ventes.created_at) as date, COUNT(ventes.id_vente) as total_ventes')
            ->where('facture.type_document', 'facture')
            ->where('facture.statut', 'validé')
            ->whereMonth('ventes.created_at', now()->month)
            ->whereYear('ventes.created_at', now()->year)
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

        $query = $this->filterFacturesValide($query);
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

        $query = $this->filterFacturesValide($query);
        $this->applyDateFilter($query, $startDate, $endDate);

        return $query->groupBy('produit')
                     ->orderBy('total_ventes', 'DESC')
                     ->first();
    }

    public function meilleurClientDuMois($startDate = null, $endDate = null)
    {
        $this->setDefaultDateRange($startDate, $endDate);

        $query = Ventes::query()
            ->selectRaw('clients.nom as client, SUM(details_ventes.prix_total) as total_ca')
            ->join('clients', 'ventes.id_client', '=', 'clients.id_client')
            ->join('details_ventes', 'ventes.id_vente', '=', 'details_ventes.id_vente');

        $query = $this->filterFacturesValide($query);
        $this->applyDateFilter($query, $startDate, $endDate);

        return $query->groupBy('client')
                    ->orderByDesc('total_ca')
                    ->first();
    }


    public function NombredeFacturesDuMois($mois = null, $annee = null)
    {
        $mois = $mois ?? now()->month;
        $annee = $annee ?? now()->year;

        return Ventes::join('facture', 'facture.id_facture', '=', 'ventes.id_facture')
                    ->where('facture.type_document', 'facture')
                    ->where('facture.statut', 'validé')
                    ->whereMonth('ventes.created_at', $mois)
                    ->whereYear('ventes.created_at', $annee)
                    ->count();
    }

    public function totalGenereDuMois()
    {
        return DB::table('details_ventes')
            ->join('ventes', 'ventes.id_vente', '=', 'details_ventes.id_vente')
            ->join('facture', 'facture.id_facture', '=', 'ventes.id_facture')
            ->where('facture.type_document', 'facture')
            ->where('facture.statut', 'validé')
            ->whereMonth('details_ventes.date_vente', now()->month)
            ->whereYear('details_ventes.date_vente', now()->year)
            ->sum('prix_total');
    }

    public function courbesVentesDuMois()
    {
        return DB::table('ventes')
            ->join('facture', 'facture.id_facture', '=', 'ventes.id_facture')
            ->selectRaw('
                DATE(ventes.created_at) as date, 
                SUM(CASE WHEN facture.type_document = "facture" THEN 1 ELSE 0 END) as total_factures,
                SUM(CASE WHEN facture.type_document = "devis" THEN 1 ELSE 0 END) as total_devis,
                SUM(CASE WHEN facture.type_document = "avoir" THEN 1 ELSE 0 END) as total_avoirs
            ')
            ->whereMonth('ventes.created_at', now()->month)
            ->whereYear('ventes.created_at', now()->year)
            ->groupBy(DB::raw('DATE(ventes.created_at)'))
            ->orderBy('date', 'ASC')
            ->get();
    }

    public function statsDocumentsMois()
    {
        $dateLimite = now()->subDays(7)->startOfDay();

        $documents = DB::table('facture')
            ->selectRaw('
                SUM(CASE WHEN type_document = "devis" THEN 1 ELSE 0 END) as total_devis,
                SUM(CASE WHEN type_document = "avoir" THEN 1 ELSE 0 END) as total_avoirs,
                SUM(CASE WHEN type_document = "facture" THEN 1 ELSE 0 END) as total_factures,
                COUNT(*) as total_documents
            ')
            ->whereDate('created_at', '>=', $dateLimite)
            ->first();

        $moyenne_journaliere_factures = $documents->total_factures > 0
            ? round($documents->total_factures / 7, 2)
            : 0;

        return [
            'devis' => $documents->total_devis,
            'avoirs' => $documents->total_avoirs,
            'factures' => $documents->total_factures,
            'total' => $documents->total_documents,
            'moyenne_journaliere_factures' => $moyenne_journaliere_factures,
        ];
    }



    public function chiffreAffaireParMois()
        {
            $annee = now()->year;

            $ventesParMois = DB::table('details_ventes')
                ->join('ventes', 'ventes.id_vente', '=', 'details_ventes.id_vente')
                ->join('facture', 'facture.id_facture', '=', 'ventes.id_facture')
                ->selectRaw('
                    MONTH(details_ventes.date_vente) as mois, 
                    SUM(details_ventes.prix_total) as total_chiffre_affaire
                ')
                ->whereYear('details_ventes.date_vente', $annee)
                ->where('facture.type_document', 'facture')
                ->where('facture.statut', 'validé')
                ->groupBy('mois')
                ->orderBy('mois')
                ->get();

            // Structure le résultat sous forme de tableau [1 => 0, 2 => 12000, ..., 12 => 0]
            $result = array_fill(1, 12, 0); // initialise tous les mois à 0

            foreach ($ventesParMois as $row) {
                $result[$row->mois] = round($row->total_chiffre_affaire, 2);
            }

            return response()->json($result);
        }




}
