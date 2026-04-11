<?php

namespace App\Http\Controllers;
use App\Models\Socio;
use Illuminate\Http\Request;

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
}
