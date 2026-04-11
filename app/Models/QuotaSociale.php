<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotaSociale extends Model
{
    protected $table = 'quote_sociali';
    protected $fillable = [
        'socio_id',
        'anno',
        'importo',
        'data_pagamento',
        'conto',
        'note',
    ];

    protected $casts = [
        'data_pagamento' => 'date',
        'importo'        => 'decimal:2',
    ];

    public function socio()
    {
        return $this->belongsTo(Socio::class);
    }
}
