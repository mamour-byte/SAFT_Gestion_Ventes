<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ventes extends Model
{
    use HasFactory;

    protected $fillable = ['id_utilisateur', 'id_client'];

    public function details()
    {
        return $this->hasMany(DetailVente::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client');
    }
}
