<?php

namespace App\Http\Controllers;
use App\Models\Movimento;
use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Exports\MovimentiExport;
use Maatwebsite\Excel\Facades\Excel;

class MovimentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Movimento::with('categoria')->orderBy('data', 'desc');

        if ($request->tipo) $query->where('tipo', $request->tipo);
        if ($request->conto) $query->where('conto', $request->conto);
        if ($request->categoria_id) $query->where('categoria_id', $request->categoria_id);
        if ($request->da) $query->whereDate('data', '>=', $request->da);
        if ($request->a) $query->whereDate('data', '<=', $request->a);

        $movimenti = $query->get();

        $saldo_cassa = Movimento::entrate()->cassa()->sum('importo')
                     - Movimento::uscite()->cassa()->sum('importo');
        $saldo_banca = Movimento::entrate()->banca()->sum('importo')
                     - Movimento::uscite()->banca()->sum('importo');

        $categorie = Categoria::orderBy('nome')->get();

        $fatture = \App\Models\Fattura::with('categoria')
            ->when($request->da, fn($q) => $q->whereDate('data', '>=', $request->da))
            ->when($request->a, fn($q) => $q->whereDate('data', '<=', $request->a))
            ->get()
            ->map(fn($f) => (object)[
                'tipo_record'  => 'fattura',
                'data'         => $f->data,
                'descrizione'  => $f->descrizione,
                'controparte'  => $f->controparte,
                'tipo'         => $f->tipo === 'attiva' ? 'entrata' : 'uscita',
                'conto'        => null,
                'importo'      => $f->importo,
                'categoria'    => $f->categoria,
                'isScaduta'    => $f->isScaduta(),
                'stato'        => $f->stato,
                'fattura_id'   => $f->id,
                'movimento_id' => null,
            ]);

        $righe = $movimenti->map(fn($m) => (object)[
                'tipo_record'  => 'movimento',
                'data'         => $m->data,
                'descrizione'  => $m->descrizione,
                'controparte'  => null,
                'tipo'         => $m->tipo,
                'conto'        => $m->conto,
                'importo'      => $m->importo,
                'categoria'    => $m->categoria,
                'isScaduta'    => false,
                'stato'        => null,
                'fattura_id'   => null,
                'movimento_id' => $m->id,
            ])
            ->concat($fatture)
            ->sortByDesc('data')
            ->values();

        $page    = $request->get('page', 1);
        $perPage = 20;
        $righe   = new \Illuminate\Pagination\LengthAwarePaginator(
            $righe->forPage($page, $perPage),
            $righe->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('movimenti.index', compact(
            'righe', 'saldo_cassa', 'saldo_banca', 'categorie'
        ));
    }

    public function create()
    {
        $categorie = Categoria::orderBy('nome')->get();
        return view('movimenti.create', compact('categorie'));
    }

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

    public function show(string $id)
    {
        //
    }

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
        $filename = 'prima-nota-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new MovimentiExport($request->da, $request->a, $request->conto), $filename);
    }

    public function destroy(Movimento $movimento)
    {
        $movimento->delete();
        return redirect()->route('movimenti.index')
            ->with('success', 'Movimento eliminato.');
    }
}