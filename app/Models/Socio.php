<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Socio extends Model
{
    protected $table = 'soci';
    protected $fillable = [
        'nome',
        'cognome',
        'codice_fiscale',
        'email',
        'telefono',
        'data_ingresso',
        'stato',
        'note',
    ];

    protected $casts = [
        'data_ingresso' => 'date',
    ];

    // Un socio ha molte quote
    public function quoteSociali()
    {
        return $this->hasMany(QuotaSociale::class);
    }

    // Accessor — restituisce nome e cognome insieme
    public function getNomeCognomeAttribute(): string
    {
        return "{$this->nome} {$this->cognome}";
    }

    // Scope — filtra solo i soci attivi
    public function scopeAttivi($query)
    {
        return $query->where('stato', 'attivo');
    }
}
