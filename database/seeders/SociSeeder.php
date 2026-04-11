<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SociSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $soci = [
            ['nome' => 'Mario',    'cognome' => 'Rossi',    'data_ingresso' => '2020-01-15', 'stato' => 'attivo'],
            ['nome' => 'Giulia',   'cognome' => 'Bianchi',  'data_ingresso' => '2020-03-10', 'stato' => 'attivo'],
            ['nome' => 'Luca',     'cognome' => 'Verdi',    'data_ingresso' => '2021-06-01', 'stato' => 'attivo'],
            ['nome' => 'Anna',     'cognome' => 'Neri',     'data_ingresso' => '2021-09-20', 'stato' => 'attivo'],
            ['nome' => 'Roberto',  'cognome' => 'Conti',    'data_ingresso' => '2019-05-05', 'stato' => 'sospeso'],
        ];

        foreach ($soci as $socio) {
            \App\Models\Socio::create($socio);
        }
    }
}
