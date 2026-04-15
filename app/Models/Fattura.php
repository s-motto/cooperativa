<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fattura extends Model
{
    protected $table = 'fatture';
    protected $fillable = [
        'numero',
        'data',
        'data_scadenza',
        'tipo',
        'controparte',
        'importo',
        'descrizione',
        'stato',
        'categoria_id',
        'file_path',
        'user_id',
    ];

    protected $casts = [
        'data'          => 'date',
        'data_scadenza' => 'date',
        'importo'       => 'decimal:2',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function utente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function movimenti()
    {
        return $this->hasMany(Movimento::class);
    }

    public function totalePagato(): float
    {
        return (float) $this->movimenti()->sum('importo');
    }

    public function residuo(): float
    {
        return (float) $this->importo - $this->totalePagato();
    }

    public function isScaduta(): bool
    {
        return $this->stato !== 'pagata'
            && $this->data_scadenza
            && $this->data_scadenza->isPast();
    }

    public function aggiornaStato(): void
    {
        $residuo = $this->residuo();
        if ($residuo <= 0) {
            $this->update(['stato' => 'pagata']);
        } elseif ($this->totalePagato() > 0) {
            $this->update(['stato' => 'parziale']);
        } else {
            $this->update(['stato' => 'aperta']);
        }
    }
}