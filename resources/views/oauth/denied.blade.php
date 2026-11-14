<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Akses Ditolak | E-Surat FKIP</title>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <main class="w-full max-w-lg bg-white shadow-lg rounded-lg p-8">
        <div class="flex flex-col items-center mb-6">
            <img class="w-20 mb-4" src="{{ asset('images/logounib.png') }}" alt="Logo UNIB">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </div>
            <h1 class="text-xl font-semibold text-slate-700 text-center">Akses Ditolak</h1>
        </div>

        <p class="text-sm text-slate-700 text-center mb-4">
            @if ($client)
                Akun Anda belum diizinkan mengakses <strong>{{ $client->name }}</strong>.
            @else
                Aplikasi tidak dikenali atau sudah tidak aktif.
            @endif
        </p>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-900 mb-6">
            @if ($reason === 'email_unverified')
                Email akun Anda belum diverifikasi. Silakan verifikasi email terlebih dahulu.
            @elseif ($reason === 'role_not_allowed')
                Aplikasi ini hanya dapat diakses oleh role tertentu. Akun Anda saat ini tercatat sebagai
                <strong>{{ $user->role->name ?? 'tidak diketahui' }}</strong>.
            @else
                Akses aplikasi sedang dinonaktifkan oleh administrator.
            @endif
        </div>

        <p class="text-sm text-slate-600 text-center mb-6">
            Jika menurut Anda ini keliru, hubungi administrator fakultas.
        </p>

        <a href="{{ url('/home') }}"
            class="block w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg text-sm text-center">
            Kembali ke E-Surat
        </a>
    </main>
</body>

</html>
