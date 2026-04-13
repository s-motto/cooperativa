<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Saldi di apertura
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">

                <p class="text-sm text-gray-500 mb-6">
                    Inserisci il saldo iniziale di cassa e banca.
                    Usa un valore <strong>negativo</strong> se il saldo è in rosso.
                    Attenzione: salverà questi come movimenti e sostituirà eventuali saldi precedenti.
                </p>

                <form method="POST" action="{{ route('saldi-apertura.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data di riferimento</label>
                        <input type="date" name="data" value="{{ old('data', date('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @error('data')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Saldo Cassa (€)</label>
                        <input type="number" name="saldo_cassa" step="0.01"
                            value="{{ old('saldo_cassa', $saldo_cassa ? ($saldo_cassa->tipo === 'entrata' ? $saldo_cassa->importo : -$saldo_cassa->importo) : '') }}"
                            placeholder="es. 459.03 oppure -13.63"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @error('saldo_cassa')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Saldo Banca (€)</label>
                        <input type="number" name="saldo_banca" step="0.01"
                            value="{{ old('saldo_banca', $saldo_banca ? ($saldo_banca->tipo === 'entrata' ? $saldo_banca->importo : -$saldo_banca->importo) : '') }}"
                            placeholder="es. 22856.12 oppure -500.00"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @error('saldo_banca')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:underline text-sm">← Annulla</a>
                        <button type="submit"
                            style="background:#4f46e5;color:white;padding:8px 24px;border-radius:8px;border:none;cursor:pointer;">
                            Salva saldi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>