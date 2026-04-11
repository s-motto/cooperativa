<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Soci
            </h2>
            <a href="{{ route('soci.create') }}"
               style="background:#4f46e5;color:white;padding:8px 16px;border-radius:8px;font-size:0.875rem;text-decoration:none;">
                + Nuovo socio
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:0.875rem;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-600">Cognome e Nome</th>
                            <th class="px-4 py-3 text-left text-gray-600">Email</th>
                            <th class="px-4 py-3 text-left text-gray-600">Telefono</th>
                            <th class="px-4 py-3 text-left text-gray-600">Ingresso</th>
                            <th class="px-4 py-3 text-left text-gray-600">Stato</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($soci as $socio)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    {{ $socio->cognome }} {{ $socio->nome }}
                                </td>
                                <td class="px-4 py-3 text-gray-500">
                                    {{ $socio->email ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-500">
                                    {{ $socio->telefono ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-500">
                                    {{ $socio->data_ingresso->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span style="padding:2px 10px;border-radius:999px;font-size:0.75rem;
                                        background:{{ $socio->stato === 'attivo' ? '#dcfce7' : ($socio->stato === 'sospeso' ? '#fef9c3' : '#fee2e2') }};
                                        color:{{ $socio->stato === 'attivo' ? '#166534' : ($socio->stato === 'sospeso' ? '#854d0e' : '#991b1b') }};">
                                        {{ ucfirst($socio->stato) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('soci.edit', $socio) }}"
                                       class="text-indigo-600 hover:underline text-xs">Modifica</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                    Nessun socio registrato.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($soci->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100">
                        {{ $soci->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>