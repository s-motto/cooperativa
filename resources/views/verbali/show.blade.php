<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $verbale->oggetto }}
            </h2>
            <div class="flex gap-4">
    <a href="{{ route('verbali.index') }}"
       style="color:#6b7280;font-size:0.875rem;text-decoration:none;padding:8px 16px;">
        ← Verbali
    </a>
    @if($verbale->file_path)
        <a href="{{ Storage::url($verbale->file_path) }}" target="_blank"
           style="background:#4f46e5;color:white;padding:8px 16px;border-radius:8px;font-size:0.875rem;text-decoration:none;">
            📄 Scarica PDF
        </a>
    @endif
    <form method="POST" action="{{ route('verbali.destroy', $verbale->id) }}"
          onsubmit="return confirm('Eliminare questo verbale?')">
        @csrf
        @method('DELETE')
        <button type="submit"
            style="background:#fee2e2;color:#991b1b;padding:8px 16px;border-radius:8px;font-size:0.875rem;border:none;cursor:pointer;">
            Elimina
        </button>
    </form>
</div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">

                <div class="flex gap-4 mb-6 text-sm text-gray-500">
                    <span>📅 {{ $verbale->data->format('d/m/Y') }}</span>
                    <span>·</span>
                    <span>{{ ucfirst($verbale->tipo) }}</span>
                    <span>·</span>
                    <span>Redatto da {{ $verbale->utente->name }}</span>
                </div>

                @if($verbale->contenuto)
                    <div class="prose max-w-none text-gray-800 leading-relaxed">
                        {!! nl2br(e($verbale->contenuto)) !!}
                    </div>
                @else
                    <p class="text-gray-400 italic">Nessun contenuto testuale — vedi il PDF allegato.</p>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>