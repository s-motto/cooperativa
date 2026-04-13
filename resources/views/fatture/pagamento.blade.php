<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Registra pagamento — {{ $fattura->controparte }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">

                <div class="mb-6 p-4 bg-gray-50 rounded-lg text-sm">
                    <p class="text-gray-500 mb-1">Fattura da {{ $fattura->tipo === 'passiva' ? 'pagare' : 'incassare' }}</p>
                    <p class="font-semibold text-gray-800">{{ $fattura->descrizione }}</p>
                    <p class="text-2xl font-bold {{ $fattura->tipo === 'passiva' ? 'text-red-600' : 'text-green-600' }} mt-1">
                        € {{ number_format($fattura->importo, 2, ',', '.') }}
                    </p>
                </div>

                <form method="POST" action="{{ route('fatture.pagamento.store', $fattura) }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data pagamento</label>
                        <input type="date" name="data" value="{{ old('data', date('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @error('data')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Conto</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="conto" value="cassa" {{ old('conto','banca') === 'cassa' ? 'checked' : '' }}>
                                <span>Cassa</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="conto" value="banca" {{ old('conto','banca') === 'banca' ? 'checked' : '' }}>
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