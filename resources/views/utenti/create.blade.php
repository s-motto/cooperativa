<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crea nuovo utente
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">

                <form method="POST" action="{{ route('utenti.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Conferma password</label>
                        <input type="password" name="password_confirmation"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ruolo</label>
                        <select name="ruolo" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="membro">Membro</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:underline text-sm">← Annulla</a>
                        <button type="submit"
                            style="background:#4f46e5;color:white;padding:8px 24px;border-radius:8px;border:none;cursor:pointer;">
                            Crea utente
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>