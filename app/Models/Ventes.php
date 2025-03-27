<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ventes extends Model
{
    use HasFactory;

    // Clé primaire (si différent de 'id')
    protected $primaryKey = 'id_vente';
    protected $fillable = ['id_user', 'id_client'];

    public function details()
    {
        return $this->hasMany(DetailVente::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_user','id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client','id_client');
    }
}
