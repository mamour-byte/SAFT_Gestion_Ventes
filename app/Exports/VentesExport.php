<?php
namespace App\Exports;

use App\Models\Ventes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VentesExport implements FromCollection, WithHeadings
{
    protected string $type;

    public function __construct(string $type = 'all')
    {
        $this->type = $type;
    }

    public function collection()
    {
        $query = Ventes::with(['client', 'facture', 'details.product'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);

        if ($this->type !== 'all') {
            $query->whereHas('facture', fn($q) => $q->where('type_document', $this->type));
        }

        return $query->get()->map(function ($vente) {
            return [
                'Client' => $vente->client->nom ?? 'N/A',
                'Type de document' => $vente->facture->type_document ?? 'N/A',
                'Statut' => $vente->facture->statut ?? 'N/A',
                'Produits' => $vente->details->map(fn($d) => $d->product->nom ?? 'Produit supprimé')->join(', '),
                'Montant' => $vente->details->sum(fn($d) => $d->product->prix_unitaire * $d->quantite_vendue),
                'Date' => $vente->created_at->format('d/m/Y'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Client', 'Type de document', 'Statut', 'Produits', 'Montant', 'Date'];
    }
}
