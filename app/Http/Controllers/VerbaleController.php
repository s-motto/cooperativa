<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Verbale;
use Illuminate\Support\Facades\Storage;
class VerbaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    $verbali = Verbale::orderBy('data', 'desc')->paginate(20);
    return view('verbali.index', compact('verbali'));
}

public function create()
{
    return view('verbali.create');
}

public function store(Request $request)
{
    $request->validate([
        'data'      => 'required|date',
        'tipo'      => 'required|in:assemblea,consiglio,straordinaria',
        'oggetto'   => 'required|string|max:255',
        'contenuto' => 'nullable|string',
        'file'      => 'nullable|file|mimes:pdf|max:5120',
    ]);

    $file_path = null;

    if ($request->hasFile('file')) {
        $file_path = $request->file('file')->store('verbali', 'public');
    }

    Verbale::create([
        'data'      => $request->data,
        'tipo'      => $request->tipo,
        'oggetto'   => $request->oggetto,
        'contenuto' => $request->contenuto,
        'file_path' => $file_path,
        'user_id'   => auth()->id(),
    ]);

    return redirect()->route('verbali.index')
        ->with('success', 'Verbale salvato correttamente.');
}

public function show(Verbale $verbale)
{
    return view('verbali.show', compact('verbale'));
}

public function destroy(Verbale $verbale)
{
    if ($verbale->file_path) {
        \Storage::disk('public')->delete($verbale->file_path);
    }
    $verbale->delete();

    return redirect()->route('verbali.index')
        ->with('success', 'Verbale eliminato.');
}
}
