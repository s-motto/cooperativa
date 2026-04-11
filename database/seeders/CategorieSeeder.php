<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorie = [
            ['nome' => 'Quote sociali',      'tipo' => 'entrata',  'colore' => '#22c55e'],
            ['nome' => 'Contributi pubblici', 'tipo' => 'entrata',  'colore' => '#3b82f6'],
            ['nome' => 'Donazioni',          'tipo' => 'entrata',  'colore' => '#a855f7'],
            ['nome' => 'Rimborsi',           'tipo' => 'entrata',  'colore' => '#f59e0b'],
            ['nome' => 'Utenze',             'tipo' => 'uscita',   'colore' => '#ef4444'],
            ['nome' => 'Affitti',            'tipo' => 'uscita',   'colore' => '#f97316'],
            ['nome' => 'Spese operative',    'tipo' => 'uscita',   'colore' => '#ec4899'],
            ['nome' => 'Personale',          'tipo' => 'uscita',   'colore' => '#14b8a6'],
            ['nome' => 'Manutenzione',       'tipo' => 'uscita',   'colore' => '#8b5cf6'],
            ['nome' => 'Varie',              'tipo' => 'entrambi', 'colore' => '#6b7280'],
        ];

        foreach ($categorie as $cat) {
            \App\Models\Categoria::create($cat);
        }
    }
}
