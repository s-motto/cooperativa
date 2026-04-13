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

    public function movimento()
    {
        return $this->hasOne(Movimento::class);
    }

    public function isScaduta(): bool
    {
        return $this->stato === 'aperta'
            && $this->data_scadenza
            && $this->data_scadenza->isPast();
    }
}