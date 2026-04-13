<?php

namespace App\Http\Controllers;

use App\Models\Fattura;
use App\Models\Movimento;
use App\Models\Categoria;
use Illuminate\Http\Request;

class FatturaController extends Controller
{
    public function index()
    {
        $aperte = Fattura::with('categoria')->where('stato', 'aperta')->orderBy('data_scadenza')->get();
        $pagate = Fattura::with('categoria')->where('stato', 'pagata')->orderByDesc('data')->paginate(20);
        return view('fatture.index', compact('aperte', 'pagate'));
    }

    public function create()
    {
        $categorie = Categoria::orderBy('nome')->get();
        return view('fatture.create', compact('categorie'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'data'          => 'required|date',
            'data_scadenza' => 'nullable|date|after_or_equal:data',
            'tipo'          => 'required|in:attiva,passiva',
            'controparte'   => 'required|string|max:255',
            'importo'       => 'required|numeric|min:0.01',
            'descrizione'   => 'required|string|max:255',
            'numero'        => 'nullable|string|max:50',
            'categoria_id'  => 'nullable|exists:categorie,id',
            'file'          => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $file_path = null;
        if ($request->hasFile('file')) {
            $file_path = $request->file('file')->store('fatture', 'public');
        }

        Fattura::create([
            'numero'        => $request->numero,
            'data'          => $request->data,
            'data_scadenza' => $request->data_scadenza,
            'tipo'          => $request->tipo,
            'controparte'   => $request->controparte,
            'importo'       => $request->importo,
            'descrizione'   => $request->descrizione,
            'categoria_id'  => $request->categoria_id,
            'file_path'     => $file_path,
            'user_id'       => auth()->id(),
        ]);

        return redirect()->route('fatture.index')
            ->with('success', 'Fattura registrata correttamente.');
    }

    public function show(Fattura $fattura)
    {
        return view('fatture.show', compact('fattura'));
    }

    public function edit(Fattura $fattura)
    {
        $categorie = Categoria::orderBy('nome')->get();
        return view('fatture.edit', compact('fattura', 'categorie'));
    }

    public function update(Request $request, Fattura $fattura)
    {
        $request->validate([
            'data'          => 'required|date',
            'data_scadenza' => 'nullable|date',
            'tipo'          => 'required|in:attiva,passiva',
            'controparte'   => 'required|string|max:255',
            'importo'       => 'required|numeric|min:0.01',
            'descrizione'   => 'required|string|max:255',
            'numero'        => 'nullable|string|max:50',
            'categoria_id'  => 'nullable|exists:categorie,id',
            'stato'         => 'required|in:aperta,pagata',
        ]);

        // Se si riapre una fattura pagata, elimina il movimento collegato
        if ($request->stato === 'aperta' && $fattura->stato === 'pagata') {
            $fattura->movimento()->delete();
        }

        $fattura->update($request->only([
            'data', 'data_scadenza', 'tipo', 'controparte',
            'importo', 'descrizione', 'numero', 'categoria_id', 'stato'
        ]));

        return redirect()->route('fatture.index')
            ->with('success', 'Fattura aggiornata correttamente.');
    }

    public function pagamento(Fattura $fattura)
    {
        $categorie = Categoria::orderBy('nome')->get();
        return view('fatture.pagamento', compact('fattura', 'categorie'));
    }

    public function storePagamento(Request $request, Fattura $fattura)
    {
        $request->validate([
            'data'         => 'required|date',
            'conto'        => 'required|in:cassa,banca',
            'categoria_id' => 'nullable|exists:categorie,id',
            'note'         => 'nullable|string',
        ]);

        Movimento::create([
            'data'         => $request->data,
            'descrizione'  => 'Pagamento fattura: ' . $fattura->descrizione . ' — ' . $fattura->controparte,
            'tipo'         => $fattura->tipo === 'passiva' ? 'uscita' : 'entrata',
            'conto'        => $request->conto,
            'importo'      => $fattura->importo,
            'categoria_id' => $request->categoria_id ?? $fattura->categoria_id,
            'note'         => $request->note,
            'user_id'      => auth()->id(),
            'fattura_id'   => $fattura->id,
        ]);

        $fattura->update(['stato' => 'pagata']);

        return redirect()->route('fatture.index')
            ->with('success', 'Pagamento registrato e movimento creato.');
    }

    public function destroy(Fattura $fattura)
    {
        if ($fattura->file_path) {
            \Storage::disk('public')->delete($fattura->file_path);
        }
        $fattura->delete();
        return redirect()->route('fatture.index')->with('success', 'Fattura eliminata.');
    }
}