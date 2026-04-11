<?php

namespace App\Http\Controllers;
use App\Models\Socio;
use Illuminate\Http\Request;
use App\Models\QuotaSociale;
use App\Models\Movimento;

class SocioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $soci = Socio::orderBy('cognome')->paginate(20);
    return view('soci.index', compact('soci'));
}

public function create()
{
    return view('soci.create');
}

public function store(Request $request)
{
    $request->validate([
        'nome'            => 'required|string|max:255',
        'cognome'         => 'required|string|max:255',
        'codice_fiscale'  => 'nullable|string|max:16|unique:soci',
        'email'           => 'nullable|email',
        'telefono'        => 'nullable|string|max:20',
        'data_ingresso'   => 'required|date',
        'stato'           => 'required|in:attivo,sospeso,uscito',
        'note'            => 'nullable|string',
    ]);

    Socio::create($request->all());

    return redirect()->route('soci.index')
        ->with('success', 'Socio aggiunto correttamente.');
}

public function edit(Socio $socio)
{
    return view('soci.edit', compact('socio'));
}

public function update(Request $request, Socio $socio)
{
    $request->validate([
        'nome'            => 'required|string|max:255',
        'cognome'         => 'required|string|max:255',
        'codice_fiscale'  => 'nullable|string|max:16|unique:soci,codice_fiscale,' . $socio->id,
        'email'           => 'nullable|email',
        'telefono'        => 'nullable|string|max:20',
        'data_ingresso'   => 'required|date',
        'stato'           => 'required|in:attivo,sospeso,uscito',
        'note'            => 'nullable|string',
    ]);

    $socio->update($request->all());

    return redirect()->route('soci.index')
        ->with('success', 'Socio aggiornato correttamente.');
}

public function destroy(Socio $socio)
{
    $socio->delete();
    return redirect()->route('soci.index')
        ->with('success', 'Socio eliminato.');
}

public function storeQuota(Request $request, Socio $socio)
{
    $request->validate([
        'anno'           => 'required|integer|min:2000|max:2099',
        'importo'        => 'required|numeric|min:0.01',
        'data_pagamento' => 'required|date',
        'conto'          => 'required|in:cassa,banca',
        'note'           => 'nullable|string',
    ]);

    // Controlla se esiste già una quota per quell'anno
    if ($socio->quoteSociali()->where('anno', $request->anno)->exists()) {
        return back()->with('error', 'Quota già registrata per l\'anno ' . $request->anno);
    }

    $socio->quoteSociali()->create($request->all());

    // Registra anche come movimento nella prima nota
    Movimento::create([
        'data'        => $request->data_pagamento,
        'descrizione' => 'Quota sociale ' . $request->anno . ' - ' . $socio->cognome . ' ' . $socio->nome,
        'tipo'        => 'entrata',
        'conto'       => $request->conto,
        'importo'     => $request->importo,
        'user_id'     => auth()->id(),
    ]);

    return back()->with('success', 'Quota registrata correttamente.');
}
}
