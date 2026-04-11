<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Riquadri saldo --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                {{-- Saldo Cassa --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-500 mb-1">Saldo Cassa</p>
                    <p class="text-2xl font-bold text-gray-800">
                        € {{ number_format($saldo_cassa, 2, ',', '.') }}
                    </p>
                    <div class="mt-3 flex justify-between text-sm">
                        <span class="text-green-600">▲ € {{ number_format($entrate_cassa, 2, ',', '.') }}</span>
                        <span class="text-red-600">▼ € {{ number_format($uscite_cassa, 2, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Saldo Banca --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-500 mb-1">Saldo Banca</p>
                    <p class="text-2xl font-bold text-gray-800">
                        € {{ number_format($saldo_banca, 2, ',', '.') }}
                    </p>
                    <div class="mt-3 flex justify-between text-sm">
                        <span class="text-green-600">▲ € {{ number_format($entrate_banca, 2, ',', '.') }}</span>
                        <span class="text-red-600">▼ € {{ number_format($uscite_banca, 2, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Soci --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-500 mb-1">Soci attivi</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $soci_attivi }}</p>
                    <div class="mt-3 text-sm">
                        @if($soci_insoluti > 0)
                            <span class="text-orange-500">⚠ {{ $soci_insoluti }} senza quota {{ date('Y') }}</span>
                        @else
                            <span class="text-green-600">✓ Tutti in regola</span>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Ultimi movimenti --}}
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Ultimi movimenti</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($ultimi_movimenti as $movimento)
                        <div class="p-4 flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-800">{{ $movimento->descrizione }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $movimento->data->format('d/m/Y') }} —
                                    {{ $movimento->conto }} —
                                    {{ $movimento->categoria?->nome ?? 'Senza categoria' }}
                                </p>
                            </div>
                            <span class="{{ $movimento->tipo === 'entrata' ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                {{ $movimento->tipo === 'entrata' ? '+' : '-' }}
                                € {{ number_format($movimento->importo, 2, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500">
                            Nessun movimento registrato ancora.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>