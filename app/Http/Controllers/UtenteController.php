<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UtenteController extends Controller
{
    public function create()
    {
        return view('utenti.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'ruolo'    => 'required|in:admin,membro',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'ruolo'    => $request->ruolo,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Utente creato correttamente.');
    }
}