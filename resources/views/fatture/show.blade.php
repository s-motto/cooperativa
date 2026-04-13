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
                @if($fattura->stato === 'aperta')
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
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">Tipo</dt>
                        <dd class="font-medium mt-1">{{ $fattura->tipo === 'attiva' ? '🟢 Attiva' : '🔴 Passiva' }}</dd></div>
                    <div><dt class="text-gray-500">Stato</dt>
                        <dd class="font-medium mt-1">{{ $fattura->stato === 'aperta' ? '⏳ Aperta' : '✅ Pagata' }}</dd></div>
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
                    <div><dt class="text-gray-500">Importo</dt>
                        <dd class="font-bold text-lg mt-1 {{ $fattura->tipo === 'attiva' ? 'text-green-600' : 'text-red-600' }}">
                            € {{ number_format($fattura->importo, 2, ',', '.') }}
                        </dd></div>
                    <div><dt class="text-gray-500">Categoria</dt>
                        <dd class="font-medium mt-1">{{ $fattura->categoria?->nome ?? '—' }}</dd></div>
                </dl>

                @if($fattura->movimento)
                    <div class="mt-6 p-4 bg-green-50 rounded-lg border border-green-200 text-sm">
                        <p class="font-semibold text-green-800 mb-1">✅ Pagamento registrato</p>
                        <p class="text-green-700">{{ $fattura->movimento->data->format('d/m/Y') }} —
                            {{ ucfirst($fattura->movimento->conto) }} —
                            € {{ number_format($fattura->movimento->importo, 2, ',', '.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>