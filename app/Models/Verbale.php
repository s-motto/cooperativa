<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verbale extends Model
{
    protected $fillable = [
        'data',
        'tipo',
        'oggetto',
        'contenuto',
        'file_path',
        'user_id',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public function utente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
