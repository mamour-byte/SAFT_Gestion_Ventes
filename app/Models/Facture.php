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
        'id_client',
        'id_user',
        'type_document', 
        'statut',
    ];

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
