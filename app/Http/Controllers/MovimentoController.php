<?php

namespace App\Http\Controllers;
use App\Models\Movimento;
use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Exports\MovimentiExport;
use Maatwebsite\Excel\Facades\Excel;
class MovimentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $query = Movimento::with('categoria')->orderBy('data', 'desc');

    // Filtro per tipo
    if ($request->tipo) {
        $query->where('tipo', $request->tipo);
    }

    // Filtro per conto
    if ($request->conto) {
        $query->where('conto', $request->conto);
    }

    // Filtro per categoria
    if ($request->categoria_id) {
        $query->where('categoria_id', $request->categoria_id);
    }

    // Filtro per periodo
    if ($request->da) {
        $query->whereDate('data', '>=', $request->da);
    }
    if ($request->a) {
        $query->whereDate('data', '<=', $request->a);
    }

    $movimenti = $query->paginate(20)->withQueryString();

    // Saldi filtrati
    $saldo_cassa = Movimento::entrate()->cassa()->sum('importo')
                 - Movimento::uscite()->cassa()->sum('importo');
    $saldo_banca = Movimento::entrate()->banca()->sum('importo')
                 - Movimento::uscite()->banca()->sum('importo');

    $categorie = Categoria::orderBy('nome')->get();

    return view('movimenti.index', compact(
        'movimenti', 'saldo_cassa', 'saldo_banca', 'categorie'
    ));
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
 public function edit(Movimento $movimento)
{
    $categorie = Categoria::orderBy('nome')->get();
    return view('movimenti.edit', compact('movimento', 'categorie'));
}

public function update(Request $request, Movimento $movimento)
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

    $movimento->update($request->all());

    return redirect()->route('movimenti.index')
        ->with('success', 'Movimento aggiornato correttamente.');
}

public function export(Request $request)
{
    $da    = $request->da;
    $a     = $request->a;
    $conto = $request->conto;

    $filename = 'prima-nota-' . now()->format('Y-m-d') . '.xlsx';

    return Excel::download(new MovimentiExport($da, $a, $conto), $filename);
}
public function destroy(Movimento $movimento)
{
    $movimento->delete();

    return redirect()->route('movimenti.index')
        ->with('success', 'Movimento eliminato.');
}
}
