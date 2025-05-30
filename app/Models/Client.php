<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_client';
    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'adresse',
        'NumeroNinea',
        'NumeroRegistreCommerce',
        'archived'
    ];
}
