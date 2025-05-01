<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ventes extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_vente';

    protected $fillable = [
        'id_client', 'id_user', 'date_vente', 'id_facture',
    ];

    public function client()
    {
        return $this->belongsTo(Facture::class, 'id_facture', 'id_facture');
    }

    public function facture()
    {
        return $this->belongsTo(Facture::class, 'id_facture', 'id_facture'); 
    }

    public function details()
    {
        return $this->hasMany(DetailVente::class, 'id_vente', 'id_vente');
    }
}
