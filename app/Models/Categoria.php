<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorie';
    protected $fillable = [
        'nome',
        'tipo',
        'colore',
    ];

    // Una categoria ha molti movimenti
    public function movimenti()
    {
        return $this->hasMany(Movimento::class);
    }
}
