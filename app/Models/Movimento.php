<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimento extends Model
{
    protected $table = 'movimenti';
    protected $fillable = [
        'data',
        'descrizione',
        'tipo',
        'conto',
        'importo',
        'categoria_id',
        'user_id',
        'allegato',
        'note',
    ];

    protected $casts = [
        'data'    => 'date',
        'importo' => 'decimal:2',
    ];

    // Relazioni
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function utente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scope per tipo
    public function scopeEntrate($query)
    {
        return $query->where('tipo', 'entrata');
    }

    public function scopeUscite($query)
    {
        return $query->where('tipo', 'uscita');
    }

    // Scope per conto
    public function scopeCassa($query)
    {
        return $query->where('conto', 'cassa');
    }

    public function scopeBanca($query)
    {
        return $query->where('conto', 'banca');
    }

    // Scope per periodo
    public function scopeDelPeriodo($query, $da, $a)
    {
        return $query->whereBetween('data', [$da, $a]);
    }
}
