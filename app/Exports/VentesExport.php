<?php


namespace App\Exports;

use App\Models\Ventes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VentesExport implements FromCollection, WithHeadings
{
    protected $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function collection()
    {
        $query = Ventes::with('facture', 'client');

        switch ($this->type) {
            case 'facture':
                $query->whereHas('facture', fn($q) => $q->where('type_document', 'facture'));
                break;

            case 'devis':
                $query->whereHas('facture', fn($q) => $q->where('type_document', 'devis'));
                break;

            case 'avoir':
                $query->whereHas('facture', fn($q) => $q->where('type_document', 'avoir'));
                break;

            case 'all':
            default:
                // ❗ Ne filtre rien, récupère tout
                break;
        }

        return $query->get()->map(function ($vente) {
            return [
                'Date'       => $vente->created_at->format('d/m/Y'),
                'Client'     => $vente->client->nom ?? '',
                'Document'   => $vente->facture->type_document ?? '',
                'Numéro'     => $vente->facture->numero_facture ?? '',
                'Statut'     => $vente->facture->statut ?? '',
                // 'Total'      => $vente->total ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Client',
            'Document',
            'Numéro',
            'Statut',
            // 'Total',
        ];
    }
}
