<x-app-layout>
    <x-slot name="header">
        <div class="flex gap-3">
    <a href="{{ route('movimenti.export') }}"
       style="background:#16a34a;color:white;padding:8px 16px;border-radius:8px;font-size:0.875rem;text-decoration:none;">
        ↓ Esporta Excel
    </a>
    <a href="{{ route('movimenti.create') }}"
       style="background:#4f46e5;color:white;padding:8px 16px;border-radius:8px;font-size:0.875rem;text-decoration:none;">
        + Nuovo movimento
    </a>
</div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Saldi --}}
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-500">Saldo Cassa</p>
                    <p class="text-2xl font-bold {{ $saldo_cassa >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        € {{ number_format($saldo_cassa, 2, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-500">Saldo Banca</p>
                    <p class="text-2xl font-bold {{ $saldo_banca >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        € {{ number_format($saldo_banca, 2, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- Filtri --}}
<form method="GET" action="{{ route('movimenti.index') }}" class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Tipo</label>
            <select name="tipo" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Tutti</option>
                <option value="entrata" {{ request('tipo') === 'entrata' ? 'selected' : '' }}>Entrate</option>
                <option value="uscita" {{ request('tipo') === 'uscita' ? 'selected' : '' }}>Uscite</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Conto</label>
            <select name="conto" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Tutti</option>
                <option value="cassa" {{ request('conto') === 'cassa' ? 'selected' : '' }}>Cassa</option>
                <option value="banca" {{ request('conto') === 'banca' ? 'selected' : '' }}>Banca</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Categoria</label>
            <select name="categoria_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Tutte</option>
                @foreach($categorie as $categoria)
                    <option value="{{ $categoria->id }}"
                        {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nome }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Dal</label>
            <input type="date" name="da" value="{{ request('da') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Al</label>
            <input type="date" name="a" value="{{ request('a') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
    </div>
    <div class="flex gap-3 mt-3">
        <button type="submit"
            style="background:#4f46e5;color:white;padding:6px 16px;border-radius:8px;border:none;cursor:pointer;font-size:0.875rem;">
            Filtra
        </button>
        <a href="{{ route('movimenti.index') }}"
           class="text-gray-500 hover:underline text-sm" style="padding:6px 0;">
            Azzera filtri
        </a>
    </div>
</form>

            {{-- Tabella movimenti --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-600">Data</th>
                            <th class="px-4 py-3 text-left text-gray-600">Descrizione</th>
                            <th class="px-4 py-3 text-left text-gray-600">Categoria</th>
                            <th class="px-4 py-3 text-left text-gray-600">Conto</th>
                            <th class="px-4 py-3 text-right text-gray-600">Importo</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($movimenti as $movimento)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $movimento->data->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    {{ $movimento->descrizione }}
                                </td>
                                <td class="px-4 py-3 text-gray-500">
                                    {{ $movimento->categoria?->nome ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs
                                        {{ $movimento->conto === 'cassa' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($movimento->conto) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold
                                    {{ $movimento->tipo === 'entrata' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movimento->tipo === 'entrata' ? '+' : '-' }}
                                    € {{ number_format($movimento->importo, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('movimenti.edit', $movimento) }}"
                                       class="text-indigo-600 hover:underline text-xs">Modifica</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                    Nessun movimento registrato ancora.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Paginazione --}}
                @if($movimenti->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100">
                        {{ $movimenti->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>