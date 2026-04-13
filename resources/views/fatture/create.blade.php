<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuova fattura</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <form method="POST" action="{{ route('fatture.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                            <div class="flex gap-4 mt-2">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="tipo" value="passiva" {{ old('tipo','passiva') === 'passiva' ? 'checked' : '' }}>
                                    <span class="text-red-600 font-medium">Passiva (da pagare)</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="tipo" value="attiva" {{ old('tipo') === 'attiva' ? 'checked' : '' }}>
                                    <span class="text-green-600 font-medium">Attiva (da incassare)</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Numero fattura</label>
                            <input type="text" name="numero" value="{{ old('numero') }}"
                                placeholder="es. 2024/001 (opzionale)"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data fattura</label>
                            <input type="date" name="data" value="{{ old('data', date('Y-m-d')) }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            @error('data')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Scadenza</label>
                            <input type="date" name="data_scadenza" value="{{ old('data_scadenza') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fornitore / Cliente</label>
                        <input type="text" name="controparte" value="{{ old('controparte') }}"
                            placeholder="es. Enel, Mario Rossi..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @error('controparte')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrizione</label>
                        <input type="text" name="descrizione" value="{{ old('descrizione') }}"
                            placeholder="es. Fornitura energia elettrica marzo 2026"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @error('descrizione')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Importo (€)</label>
                            <input type="number" name="importo" value="{{ old('importo') }}"
                                step="0.01" min="0.01"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            @error('importo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                            <select name="categoria_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                <option value="">— Nessuna —</option>
                                @foreach($categorie as $cat)
                                    <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Allega PDF fattura</label>
                        <input type="file" name="file" accept=".pdf"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <p class="text-xs text-gray-400 mt-1">Opzionale — max 5MB</p>
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('fatture.index') }}" class="text-gray-500 hover:underline text-sm">← Annulla</a>
                        <button type="submit"
                            style="background:#4f46e5;color:white;padding:8px 24px;border-radius:8px;border:none;cursor:pointer;">
                            Salva fattura
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>