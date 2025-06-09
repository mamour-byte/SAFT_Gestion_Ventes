<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    protected $table = 'facture'; // car par défaut Laravel cherche 'factures'

    protected $primaryKey = 'id_facture';

    protected $fillable = [
        'id_facture',
        'id_client',
        'id_user',
        'type_document', 
        'tva',
        'numero_facture',
        'statut',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($facture) {
            if (!$facture->numero_facture) {
                $type = strtoupper(substr($facture->type_document, 0, 3)); 
                $prefix = $type . '-' . now()->format('Ym'); // 

                $lastNumber = self::where('type_document', $facture->type_document)
                    ->whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month)
                    ->count() + 1;

                $facture->numero_facture = sprintf('%s-%04d', $prefix, $lastNumber);
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function ventes()
    {
        return $this->hasMany(Ventes::class, 'id_facture');
    }
}
