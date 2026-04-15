<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">{{ $fattura->descrizione }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('fatture.index') }}" class="text-gray-500 text-sm hover:underline">← Fatture</a>
                @if($fattura->file_path)
                    <a href="{{ Storage::url($fattura->file_path) }}" target="_blank"
                       style="background:#4f46e5;color:white;padding:8px 16px;border-radius:8px;font-size:0.875rem;text-decoration:none;">
                        📄 PDF
                    </a>
                @endif
                @if($fattura->stato !== 'pagata')
                    <a href="{{ route('fatture.pagamento', $fattura) }}"
                       style="background:#16a34a;color:white;padding:8px 16px;border-radius:8px;font-size:0.875rem;text-decoration:none;">
                        Registra pagamento
                    </a>
                @endif
                <form method="POST" action="{{ route('fatture.destroy', $fattura) }}"
                      onsubmit="return confirm('Eliminare questa fattura?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        style="background:#fee2e2;color:#991b1b;padding:8px 16px;border-radius:8px;font-size:0.875rem;border:none;cursor:pointer;">
                        Elimina
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">

                <dl class="grid grid-cols-2 gap-4 text-sm mb-6">
                    <div><dt class="text-gray-500">Tipo</dt>
                        <dd class="font-medium mt-1">{{ $fattura->tipo === 'attiva' ? '🟢 Attiva' : '🔴 Passiva' }}</dd></div>
                    <div><dt class="text-gray-500">Stato</dt>
                        <dd class="font-medium mt-1">
                            @if($fattura->stato === 'aperta') ⏳ Aperta
                            @elseif($fattura->stato === 'parziale') 🔶 Parzialmente pagata
                            @else ✅ Pagata
                            @endif
                        </dd></div>
                    <div><dt class="text-gray-500">Controparte</dt>
                        <dd class="font-medium mt-1">{{ $fattura->controparte }}</dd></div>
                    <div><dt class="text-gray-500">Numero</dt>
                        <dd class="font-medium mt-1">{{ $fattura->numero ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Data fattura</dt>
                        <dd class="font-medium mt-1">{{ $fattura->data->format('d/m/Y') }}</dd></div>
                    <div><dt class="text-gray-500">Scadenza</dt>
                        <dd class="font-medium mt-1 {{ $fattura->isScaduta() ? 'text-red-600' : '' }}">
                            {{ $fattura->data_scadenza?->format('d/m/Y') ?? '—' }}
                            @if($fattura->isScaduta()) ⚠ scaduta @endif
                        </dd></div>
                    <div><dt class="text-gray-500">Importo totale</dt>
                        <dd class="font-bold text-lg mt-1 {{ $fattura->tipo === 'attiva' ? 'text-green-600' : 'text-red-600' }}">
                            € {{ number_format($fattura->importo, 2, ',', '.') }}
                        </dd></div>
                    <div><dt class="text-gray-500">Residuo</dt>
                        <dd class="font-bold text-lg mt-1 {{ $fattura->residuo() > 0 ? 'text-orange-500' : 'text-green-600' }}">
                            € {{ number_format($fattura->residuo(), 2, ',', '.') }}
                        </dd></div>
                </dl>

                {{-- Storico pagamenti --}}
                @if($fattura->movimenti->count() > 0)
                    <div class="border-t border-gray-100 pt-4">
                        <h3 class="font-semibold text-gray-700 mb-3 text-sm">Pagamenti registrati</h3>
                        <div class="divide-y divide-gray-100">
                            @foreach($fattura->movimenti as $m)
                                <div class="py-2 flex justify-between text-sm">
                                    <div>
                                        <span class="text-gray-600">{{ $m->data->format('d/m/Y') }}</span>
                                        <span class="ml-2">
                                            <span class="px-2 py-0.5 rounded-full text-xs
                                                {{ $m->conto === 'cassa' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ ucfirst($m->conto) }}
                                            </span>
                                        </span>
                                        @if($m->note)
                                            <span class="text-gray-400 text-xs ml-2">{{ $m->note }}</span>
                                        @endif
                                    </div>
                                    <span class="font-semibold text-gray-800">
                                        € {{ number_format($m->importo, 2, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-200 flex justify-between text-sm">
                            <span class="font-semibold text-gray-700">Totale pagato</span>
                            <span class="font-bold text-green-600">€ {{ number_format($fattura->totalePagato(), 2, ',', '.') }}</span>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>