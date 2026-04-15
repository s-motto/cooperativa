<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Registra pagamento — {{ $fattura->controparte }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">

                {{-- Riepilogo fattura --}}
                <div class="mb-6 p-4 bg-gray-50 rounded-lg text-sm">
                    <p class="text-gray-500 mb-1">{{ $fattura->descrizione }}</p>
                    <div class="flex justify-between items-center mt-2">
                        <div>
                            <p class="text-xs text-gray-400">Importo totale</p>
                            <p class="font-semibold text-gray-700">€ {{ number_format($fattura->importo, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Già pagato</p>
                            <p class="font-semibold text-green-600">€ {{ number_format($fattura->totalePagato(), 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Residuo</p>
                            <p class="font-bold text-lg {{ $fattura->tipo === 'passiva' ? 'text-red-600' : 'text-green-600' }}">
                                € {{ number_format($fattura->residuo(), 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Storico pagamenti --}}
                @if($fattura->movimenti->count() > 0)
                    <div class="mb-6">
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Pagamenti precedenti</p>
                        <div class="divide-y divide-gray-100 border border-gray-100 rounded-lg">
                            @foreach($fattura->movimenti as $m)
                                <div class="px-3 py-2 flex justify-between text-sm">
                                    <span class="text-gray-600">{{ $m->data->format('d/m/Y') }} — {{ ucfirst($m->conto) }}</span>
                                    <span class="font-semibold text-gray-800">€ {{ number_format($m->importo, 2, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('fatture.pagamento.store', $fattura) }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data pagamento</label>
                        <input type="date" name="data" value="{{ old('data', date('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @error('data')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Importo pagato (€)
                            <span class="text-gray-400 font-normal ml-1">max € {{ number_format($fattura->residuo(), 2, ',', '.') }}</span>
                        </label>
                        <input type="number" name="importo" step="0.01" min="0.01"
                            max="{{ $fattura->residuo() }}"
                            placeholder="0,00"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @error('importo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Conto</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="conto" value="cassa" {{ old('conto') === 'cassa' ? 'checked' : '' }}>
                                <span>Cassa</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="conto" value="banca" checked>
                                <span>Banca</span>
                            </label>
                        </div>
                        @error('conto')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                        <select name="categoria_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">— Stessa della fattura —</option>
                            @foreach($categorie as $cat)
                                <option value="{{ $cat->id }}" {{ $fattura->categoria_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                        <input type="text" name="note" placeholder="Opzionale..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('fatture.index') }}" class="text-gray-500 hover:underline text-sm">← Annulla</a>
                        <button type="submit"
                            style="background:#16a34a;color:white;padding:8px 24px;border-radius:8px;border:none;cursor:pointer;">
                            Conferma pagamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>