<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Modifica socio — {{ $socio->cognome }} {{ $socio->nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">

                <form method="POST" action="{{ route('soci.update', $socio->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                            <input type="text" name="nome" value="{{ $socio->nome }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            @error('nome')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cognome</label>
                            <input type="text" name="cognome" value="{{ $socio->cognome }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            @error('cognome')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Codice Fiscale</label>
                        <input type="text" name="codice_fiscale" value="{{ $socio->codice_fiscale }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @error('codice_fiscale')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ $socio->email }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telefono</label>
                            <input type="text" name="telefono" value="{{ $socio->telefono }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data ingresso</label>
                            <input type="date" name="data_ingresso"
                                value="{{ $socio->data_ingresso->format('Y-m-d') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stato</label>
                            <select name="stato" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                <option value="attivo" {{ $socio->stato === 'attivo' ? 'selected' : '' }}>Attivo</option>
                                <option value="sospeso" {{ $socio->stato === 'sospeso' ? 'selected' : '' }}>Sospeso</option>
                                <option value="uscito" {{ $socio->stato === 'uscito' ? 'selected' : '' }}>Uscito</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                        <textarea name="note" rows="3"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ $socio->note }}</textarea>
                    </div>

                    <div class="flex justify-between items-center">
                        <button type="button"
                            onclick="document.getElementById('form-elimina-socio').submit()"
                            style="color:#dc2626;background:none;border:none;cursor:pointer;font-size:0.875rem;">
                            Elimina socio
                        </button>
                        <div class="flex gap-4 items-center">
                            <a href="{{ route('soci.index') }}" class="text-gray-500 hover:underline text-sm">← Annulla</a>
                            <button type="submit"
                                style="background:#4f46e5;color:white;padding:8px 24px;border-radius:8px;border:none;cursor:pointer;">
                                Aggiorna socio
                            </button>
                        </div>
                    </div>

                </form>

                <form id="form-elimina-socio" method="POST"
                      action="{{ route('soci.destroy', $socio->id) }}"
                      onsubmit="return confirm('Eliminare questo socio?')">
                    @csrf
                    @method('DELETE')
                </form>

                {{-- Storico quote --}}
                <div class="mt-8">
                    <h3 class="font-semibold text-gray-800 mb-4">Quote sociali</h3>

                    {{-- Lista quote esistenti --}}
                    @if($socio->quoteSociali->count() > 0)
                        <table class="w-full text-sm mb-6">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-gray-600">Anno</th>
                                    <th class="px-4 py-2 text-left text-gray-600">Importo</th>
                                    <th class="px-4 py-2 text-left text-gray-600">Data pagamento</th>
                                    <th class="px-4 py-2 text-left text-gray-600">Conto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($socio->quoteSociali->sortByDesc('anno') as $quota)
                                    <tr>
                                        <td class="px-4 py-2 font-medium">{{ $quota->anno }}</td>
                                        <td class="px-4 py-2 text-green-600">€ {{ number_format($quota->importo, 2, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $quota->data_pagamento->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2">{{ ucfirst($quota->conto) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-400 text-sm mb-6">Nessuna quota registrata.</p>
                    @endif

                    {{-- Form nuova quota --}}
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:16px;">
                        <h4 class="font-medium text-gray-700 mb-4 text-sm">Registra nuova quota</h4>
                        <form method="POST" action="{{ route('soci.quote.store', $socio->id) }}">
                            @csrf
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Anno</label>
                                    <input type="number" name="anno" value="{{ date('Y') }}"
                                        min="2000" max="2099"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Importo (€)</label>
                                    <input type="number" name="importo" step="0.01" min="0.01"
                                        placeholder="50.00"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Data pagamento</label>
                                    <input type="date" name="data_pagamento" value="{{ date('Y-m-d') }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Conto</label>
                                    <select name="conto" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                        <option value="cassa">Cassa</option>
                                        <option value="banca">Banca</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Note</label>
                                <input type="text" name="note" placeholder="Opzionale..."
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <button type="submit"
                                style="background:#4f46e5;color:white;padding:8px 20px;border-radius:8px;border:none;cursor:pointer;font-size:0.875rem;">
                                Registra quota
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>