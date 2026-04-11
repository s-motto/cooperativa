<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Modifica movimento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">

                <form method="POST" action="{{ route('movimenti.update', $movimento->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Tipo --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="tipo" value="entrata"
                                    {{ $movimento->tipo === 'entrata' ? 'checked' : '' }}>
                                <span class="text-green-600 font-medium">Entrata</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="tipo" value="uscita"
                                    {{ $movimento->tipo === 'uscita' ? 'checked' : '' }}>
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
                                    {{ $movimento->conto === 'cassa' ? 'checked' : '' }}>
                                <span>Cassa</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="conto" value="banca"
                                    {{ $movimento->conto === 'banca' ? 'checked' : '' }}>
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
                        <input type="date" name="data"
                            value="{{ $movimento->data->format('Y-m-d') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('data')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descrizione --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrizione</label>
                        <input type="text" name="descrizione"
                            value="{{ $movimento->descrizione }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('descrizione')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Importo --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Importo (€)</label>
                        <input type="number" name="importo"
                            value="{{ $movimento->importo }}"
                            step="0.01" min="0.01"
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
                                    {{ $movimento->categoria_id == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Note --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                        <textarea name="note" rows="3"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $movimento->note }}</textarea>
                    </div>

                    {{-- Bottoni --}}
                    <div class="flex justify-between items-center">

                        {{-- Elimina — form separato FUORI dal form principale --}}
                        <button type="button"
                            onclick="document.getElementById('form-elimina').submit()"
                            style="color:#dc2626;background:none;border:none;cursor:pointer;font-size:0.875rem;">
                            Elimina
                        </button>

                        <div class="flex gap-4 items-center">
                            <a href="{{ route('movimenti.index') }}"
                               class="text-gray-500 hover:underline text-sm">← Annulla</a>
                            <button type="submit"
                                style="background:#4f46e5;color:white;padding:8px 24px;border-radius:8px;border:none;cursor:pointer;">
                                Aggiorna movimento
                            </button>
                        </div>
                    </div>

                </form>

                {{-- Form elimina — fuori dal form principale --}}
                <form id="form-elimina" method="POST"
                      action="{{ route('movimenti.destroy', $movimento->id) }}"
                      onsubmit="return confirm('Sei sicura di voler eliminare questo movimento?')">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>