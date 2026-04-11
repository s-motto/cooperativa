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

            </div>
        </div>
    </div>
</x-app-layout>