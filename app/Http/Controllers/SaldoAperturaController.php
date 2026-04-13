<?php

namespace App\Http\Controllers;

use App\Models\Movimento;
use Illuminate\Http\Request;

class SaldoAperturaController extends Controller
{
    public function create()
    {
        // Cerca saldi di apertura già inseriti
        $saldo_cassa = Movimento::where('descrizione', 'LIKE', 'Saldo iniziale cassa%')->latest()->first();
        $saldo_banca = Movimento::where('descrizione', 'LIKE', 'Saldo iniziale banca%')->latest()->first();

        return view('saldi-apertura.create', compact('saldo_cassa', 'saldo_banca'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'data'         => 'required|date',
            'saldo_cassa'  => 'required|numeric',
            'saldo_banca'  => 'required|numeric',
        ]);

        // Elimina eventuali saldi di apertura precedenti
        Movimento::where('descrizione', 'LIKE', 'Saldo iniziale cassa%')->delete();
        Movimento::where('descrizione', 'LIKE', 'Saldo iniziale banca%')->delete();

        // Crea saldo cassa
        $importo_cassa = $request->saldo_cassa;
        Movimento::create([
            'data'        => $request->data,
            'descrizione' => 'Saldo iniziale cassa al ' . \Carbon\Carbon::parse($request->data)->format('d/m/Y'),
            'tipo'        => $importo_cassa >= 0 ? 'entrata' : 'uscita',
            'conto'       => 'cassa',
            'importo'     => abs($importo_cassa),
            'user_id'     => auth()->id(),
        ]);

        // Crea saldo banca
        $importo_banca = $request->saldo_banca;
        Movimento::create([
            'data'        => $request->data,
            'descrizione' => 'Saldo iniziale banca al ' . \Carbon\Carbon::parse($request->data)->format('d/m/Y'),
            'tipo'        => $importo_banca >= 0 ? 'entrata' : 'uscita',
            'conto'       => 'banca',
            'importo'     => abs($importo_banca),
            'user_id'     => auth()->id(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Saldi di apertura impostati correttamente.');
    }
}