<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailVente extends Model
{
    use HasFactory;

    protected $fillable = ['id_vente', 'id_product', 'quantite_vendue', 'prix_total'];

    public function vente()
    {
        return $this->belongsTo(Ventes::class, 'id_vente','id_vente');
    }

    public function produit()
    {
        return $this->belongsTo(Product::class, 'id_product','id_product');
    }
}

