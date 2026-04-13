<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Fatture</h2>
            <a href="{{ route('fatture.create') }}"
               style="background:#4f46e5;color:white;padding:8px 16px;border-radius:8px;font-size:0.875rem;text-decoration:none;">
                + Nuova fattura
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Fatture aperte --}}
            <h3 class="font-semibold text-gray-700 mb-3">In attesa di pagamento</h3>
            <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-600">Data</th>
                            <th class="px-4 py-3 text-left text-gray-600">Numero</th>
                            <th class="px-4 py-3 text-left text-gray-600">Controparte</th>
                            <th class="px-4 py-3 text-left text-gray-600">Tipo</th>
                            <th class="px-4 py-3 text-left text-gray-600">Scadenza</th>
                            <th class="px-4 py-3 text-right text-gray-600">Importo</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($aperte as $fattura)
                            <tr class="{{ $fattura->isScaduta() ? 'bg-red-50' : '' }} hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">{{ $fattura->data->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $fattura->numero ?? '—' }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $fattura->controparte }}</td>
                                <td class="px-4 py-3">
                                    <span style="padding:2px 10px;border-radius:999px;font-size:0.75rem;
                                        background:{{ $fattura->tipo === 'attiva' ? '#dcfce7' : '#fee2e2' }};
                                        color:{{ $fattura->tipo === 'attiva' ? '#166534' : '#991b1b' }};">
                                        {{ $fattura->tipo === 'attiva' ? 'Attiva' : 'Passiva' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 {{ $fattura->isScaduta() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                    {{ $fattura->data_scadenza?->format('d/m/Y') ?? '—' }}
                                    @if($fattura->isScaduta()) ⚠ @endif
                                </td>
                                <td class="px-4 py-3 text-right font-semibold
                                    {{ $fattura->tipo === 'attiva' ? 'text-green-600' : 'text-red-600' }}">
                                    € {{ number_format($fattura->importo, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex gap-2 justify-end">
                                        <a href="{{ route('fatture.edit', $fattura) }}"
                                           class="text-indigo-600 hover:underline text-xs">Modifica</a>
                                        <a href="{{ route('fatture.show', $fattura) }}"
                                           class="text-gray-500 hover:underline text-xs">Dettaglio</a>
                                        <a href="{{ route('fatture.pagamento', $fattura) }}"
                                           style="background:#4f46e5;color:white;padding:3px 10px;border-radius:6px;font-size:0.75rem;text-decoration:none;">
                                            Registra pagamento
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                    Nessuna fattura in attesa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Fatture pagate --}}
            <h3 class="font-semibold text-gray-700 mb-3">Pagate</h3>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-600">Data</th>
                            <th class="px-4 py-3 text-left text-gray-600">Numero</th>
                            <th class="px-4 py-3 text-left text-gray-600">Controparte</th>
                            <th class="px-4 py-3 text-left text-gray-600">Tipo</th>
                            <th class="px-4 py-3 text-right text-gray-600">Importo</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pagate as $fattura)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">{{ $fattura->data->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $fattura->numero ?? '—' }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $fattura->controparte }}</td>
                                <td class="px-4 py-3">
                                    <span style="padding:2px 10px;border-radius:999px;font-size:0.75rem;
                                        background:{{ $fattura->tipo === 'attiva' ? '#dcfce7' : '#fee2e2' }};
                                        color:{{ $fattura->tipo === 'attiva' ? '#166534' : '#991b1b' }};">
                                        {{ $fattura->tipo === 'attiva' ? 'Attiva' : 'Passiva' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-600">
                                    € {{ number_format($fattura->importo, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex gap-2 justify-end">
                                        <a href="{{ route('fatture.edit', $fattura) }}"
                                           class="text-indigo-600 hover:underline text-xs">Modifica</a>
                                        <a href="{{ route('fatture.show', $fattura) }}"
                                           class="text-gray-500 hover:underline text-xs">Dettaglio</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                    Nessuna fattura pagata.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($pagate->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100">{{ $pagate->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>