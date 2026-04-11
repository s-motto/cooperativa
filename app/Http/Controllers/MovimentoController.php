<?php

namespace App\Http\Controllers;
use App\Models\Movimento;
use App\Models\Categoria;
use Illuminate\Http\Request;

class MovimentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $movimenti = Movimento::with('categoria')
        ->orderBy('data', 'desc')
        ->paginate(20);

    $saldo_cassa = Movimento::entrate()->cassa()->sum('importo')
                 - Movimento::uscite()->cassa()->sum('importo');

    $saldo_banca = Movimento::entrate()->banca()->sum('importo')
                 - Movimento::uscite()->banca()->sum('importo');

    return view('movimenti.index', compact('movimenti', 'saldo_cassa', 'saldo_banca'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $categorie = Categoria::orderBy('nome')->get();
    return view('movimenti.create', compact('categorie'));
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'data'         => 'required|date',
        'descrizione'  => 'required|string|max:255',
        'tipo'         => 'required|in:entrata,uscita',
        'conto'        => 'required|in:cassa,banca',
        'importo'      => 'required|numeric|min:0.01',
        'categoria_id' => 'nullable|exists:categorie,id',
        'note'         => 'nullable|string',
    ]);

    Movimento::create([
        'data'         => $request->data,
        'descrizione'  => $request->descrizione,
        'tipo'         => $request->tipo,
        'conto'        => $request->conto,
        'importo'      => $request->importo,
        'categoria_id' => $request->categoria_id,
        'note'         => $request->note,
        'user_id'      => auth()->id(),
    ]);

    return redirect()->route('movimenti.index')
        ->with('success', 'Movimento registrato correttamente.');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
