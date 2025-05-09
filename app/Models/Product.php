<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_product';
    protected $fillable = [
        'id_product',
        'nom',
        'description',
        'prix_unitaire',
        'quantite_stock',
        'archived'
    ];

    public function details()
        {
            return $this->hasMany(DetailVente::class, 'id_product', 'id_product');
        }

}

