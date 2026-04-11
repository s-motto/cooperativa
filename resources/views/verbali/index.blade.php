<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Verbali
            </h2>
            <a href="{{ route('verbali.create') }}"
               style="background:#4f46e5;color:white;padding:8px 16px;border-radius:8px;font-size:0.875rem;text-decoration:none;">
                + Nuovo verbale
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-600">Data</th>
                            <th class="px-4 py-3 text-left text-gray-600">Tipo</th>
                            <th class="px-4 py-3 text-left text-gray-600">Oggetto</th>
                            <th class="px-4 py-3 text-left text-gray-600">PDF</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($verbali as $verbale)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $verbale->data->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span style="padding:2px 10px;border-radius:999px;font-size:0.75rem;
                                        background:{{ $verbale->tipo === 'assemblea' ? '#dbeafe' : ($verbale->tipo === 'consiglio' ? '#ede9fe' : '#fef9c3') }};
                                        color:{{ $verbale->tipo === 'assemblea' ? '#1e40af' : ($verbale->tipo === 'consiglio' ? '#6d28d9' : '#854d0e') }};">
                                        {{ ucfirst($verbale->tipo) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    {{ $verbale->oggetto }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($verbale->file_path)
                                        <a href="{{ Storage::url($verbale->file_path) }}"
                                           target="_blank"
                                           class="text-indigo-600 hover:underline text-xs">
                                            📄 Scarica PDF
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('verbali.show', $verbale->id) }}"
                                       class="text-indigo-600 hover:underline text-xs">Visualizza</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                    Nessun verbale registrato.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($verbali->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100">
                        {{ $verbali->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>