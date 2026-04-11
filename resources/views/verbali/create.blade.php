<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nuovo verbale
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">

                <form method="POST" action="{{ route('verbali.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                            <input type="date" name="data" value="{{ old('data', date('Y-m-d')) }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            @error('data')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                            <select name="tipo" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                <option value="assemblea" {{ old('tipo') === 'assemblea' ? 'selected' : '' }}>Assemblea</option>
                                <option value="consiglio" {{ old('tipo') === 'consiglio' ? 'selected' : '' }}>Consiglio</option>
                                <option value="straordinaria" {{ old('tipo') === 'straordinaria' ? 'selected' : '' }}>Straordinaria</option>
                            </select>
                            @error('tipo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Oggetto</label>
                        <input type="text" name="oggetto" value="{{ old('oggetto') }}"
                            placeholder="es. Approvazione bilancio 2025"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @error('oggetto')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contenuto</label>
                        <textarea name="contenuto" rows="6"
                            placeholder="Testo del verbale... (opzionale se carichi il PDF)"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('contenuto') }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Allega PDF</label>
                        <input type="file" name="file" accept=".pdf"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <p class="text-xs text-gray-400 mt-1">Opzionale — max 5MB</p>
                        @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('verbali.index') }}" class="text-gray-500 hover:underline text-sm">← Annulla</a>
                        <button type="submit"
                            style="background:#4f46e5;color:white;padding:8px 24px;border-radius:8px;border:none;cursor:pointer;">
                            Salva verbale
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>