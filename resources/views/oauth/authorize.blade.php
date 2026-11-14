<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Otorisasi Akses | E-Surat FKIP</title>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <main class="w-full max-w-lg bg-white shadow-lg rounded-lg p-8">
        <div class="flex flex-col items-center mb-6">
            <img class="w-20 mb-4" src="{{ asset('images/logounib.png') }}" alt="Logo UNIB">
            <h1 class="text-xl font-semibold text-slate-700 text-center">
                Permintaan Akses Aplikasi
            </h1>
            <p class="text-sm text-slate-500 text-center mt-1">
                E-Surat FKIP Universitas Bengkulu
            </p>
        </div>

        <div class="border border-slate-200 rounded-lg p-4 mb-6 bg-slate-50">
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-1">Aplikasi</p>
            <p class="text-base font-semibold text-slate-800">{{ $client->name }}</p>
            @if ($client->description)
                <p class="text-sm text-slate-600 mt-1">{{ $client->description }}</p>
            @endif
        </div>

        <p class="text-sm text-slate-700 mb-3">
            Aplikasi ini akan menerima data berikut dari akun Anda:
        </p>

        <ul class="text-sm text-slate-700 space-y-1 mb-6">
            @foreach ($scopes as $scope)
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 text-green-600 shrink-0" fill="none" stroke="currentColor"
                        stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span>{{ $scope->description }}</span>
                </li>
            @endforeach
        </ul>

        <div class="border-t border-slate-200 pt-4 mb-6">
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-1">Masuk sebagai</p>
            <p class="text-sm font-semibold text-slate-800">{{ $user->name }}</p>
            <p class="text-xs text-slate-500">
                {{ $user->role->name ?? '-' }} &middot; {{ $user->username }}
            </p>
        </div>

        <form method="POST" action="{{ route('passport.authorizations.approve') }}">
            @csrf
            <input type="hidden" name="state" value="{{ $request->session()->get('authToken') }}">
            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Izinkan
                </button>
                <button type="submit" formaction="{{ route('passport.authorizations.deny') }}"
                    class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                    Batal
                </button>
            </div>
        </form>

        <p class="text-xs text-slate-400 text-center mt-6">
            Anda hanya akan melihat halaman ini sekali untuk setiap aplikasi.
        </p>
    </main>
</body>

</html>
