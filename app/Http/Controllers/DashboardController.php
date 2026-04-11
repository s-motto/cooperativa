<?php

namespace App\Http\Controllers;

use App\Models\Movimento;
use App\Models\Socio;
use App\Models\Categoria;

class DashboardController extends Controller
{
    public function index()
    {
        // Saldi cassa
        $entrate_cassa = Movimento::entrate()->cassa()->sum('importo');
        $uscite_cassa  = Movimento::uscite()->cassa()->sum('importo');
        $saldo_cassa   = $entrate_cassa - $uscite_cassa;

        // Saldi banca
        $entrate_banca = Movimento::entrate()->banca()->sum('importo');
        $uscite_banca  = Movimento::uscite()->banca()->sum('importo');
        $saldo_banca   = $entrate_banca - $uscite_banca;

        // Ultimi 5 movimenti
        $ultimi_movimenti = Movimento::with('categoria')
            ->orderBy('data', 'desc')
            ->limit(5)
            ->get();

        // Soci attivi
        $soci_attivi = Socio::attivi()->count();

        // Soci che non hanno pagato la quota dell'anno corrente
        $anno_corrente = date('Y');
        $soci_insoluti = Socio::attivi()
            ->whereDoesntHave('quoteSociali', function ($q) use ($anno_corrente) {
                $q->where('anno', $anno_corrente);
            })
            ->count();

        return view('dashboard', compact(
            'saldo_cassa',
            'entrate_cassa',
            'uscite_cassa',
            'saldo_banca',
            'entrate_banca',
            'uscite_banca',
            'ultimi_movimenti',
            'soci_attivi',
            'soci_insoluti'
        ));
    }
}