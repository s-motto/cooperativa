<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nuovo movimento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">

                <form method="POST" action="{{ route('movimenti.store') }}">
                    @csrf

                    {{-- Tipo --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="tipo" value="entrata"
                                    {{ old('tipo') === 'entrata' ? 'checked' : '' }}>
                                <span class="text-green-600 font-medium">Entrata</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="tipo" value="uscita"
                                    {{ old('tipo') === 'uscita' ? 'checked' : '' }}>
                                <span class="text-red-600 font-medium">Uscita</span>
                            </label>
                        </div>
                        @error('tipo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Conto --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Conto</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="conto" value="cassa"
                                    {{ old('conto') === 'cassa' ? 'checked' : '' }}>
                                <span>Cassa</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="conto" value="banca"
                                    {{ old('conto') === 'banca' ? 'checked' : '' }}>
                                <span>Banca</span>
                            </label>
                        </div>
                        @error('conto')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Data --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                        <input type="date" name="data" value="{{ old('data', date('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('data')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descrizione --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrizione</label>
                        <input type="text" name="descrizione" value="{{ old('descrizione') }}"
                            placeholder="es. Pagamento bolletta luce"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('descrizione')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Importo --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Importo (€)</label>
                        <input type="number" name="importo" value="{{ old('importo') }}"
                            step="0.01" min="0.01" placeholder="0,00"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('importo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Categoria --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                        <select name="categoria_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Nessuna categoria —</option>
                            @foreach($categorie as $categoria)
                                <option value="{{ $categoria->id }}"
                                    {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Note --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                        <textarea name="note" rows="3" placeholder="Opzionale..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('note') }}</textarea>
                    </div>

                    {{-- Bottoni --}}
                    <div class="flex justify-between">
                        <a href="{{ route('movimenti.index') }}"
                           class="text-gray-500 hover:underline text-sm">← Annulla</a>
                        <button type="submit" style="background:red;color:white;padding:8px 16px;border-radius:8px;">
    Salva movimento
</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>