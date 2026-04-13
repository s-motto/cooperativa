<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifica fattura</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <form method="POST" action="{{ route('fatture.update', $fattura) }}">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                            <div class="flex gap-4 mt-2">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="tipo" value="passiva" {{ $fattura->tipo === 'passiva' ? 'checked' : '' }}>
                                    <span class="text-red-600 font-medium">Passiva (da pagare)</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="tipo" value="attiva" {{ $fattura->tipo === 'attiva' ? 'checked' : '' }}>
                                    <span class="text-green-600 font-medium">Attiva (da incassare)</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Numero fattura</label>
                            <input type="text" name="numero" value="{{ $fattura->numero }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data fattura</label>
                            <input type="date" name="data" value="{{ $fattura->data->format('Y-m-d') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            @error('data')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Scadenza</label>
                            <input type="date" name="data_scadenza" value="{{ $fattura->data_scadenza?->format('Y-m-d') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fornitore / Cliente</label>
                        <input type="text" name="controparte" value="{{ $fattura->controparte }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @error('controparte')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrizione</label>
                        <input type="text" name="descrizione" value="{{ $fattura->descrizione }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @error('descrizione')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Importo (€)</label>
                            <input type="number" name="importo" value="{{ $fattura->importo }}"
                                step="0.01" min="0.01"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            @error('importo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                            <select name="categoria_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                <option value="">— Nessuna —</option>
                                @foreach($categorie as $cat)
                                    <option value="{{ $cat->id }}" {{ $fattura->categoria_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stato</label>
                        <div class="flex gap-4 mt-2">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="stato" value="aperta" {{ $fattura->stato === 'aperta' ? 'checked' : '' }}>
                                <span class="text-amber-600 font-medium">⏳ Aperta</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="stato" value="pagata" {{ $fattura->stato === 'pagata' ? 'checked' : '' }}>
                                <span class="text-green-600 font-medium">✓ Pagata</span>
                            </label>
                        </div>
                        @if($fattura->stato === 'pagata')
                            <p class="text-xs text-amber-600 mt-2">
                                ⚠ Se riporti la fattura ad "Aperta", il movimento di pagamento collegato verrà eliminato.
                            </p>
                        @endif
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('fatture.index') }}" class="text-gray-500 hover:underline text-sm">← Annulla</a>
                        <button type="submit"
                            style="background:#4f46e5;color:white;padding:8px 24px;border-radius:8px;border:none;cursor:pointer;">
                            Aggiorna fattura
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>