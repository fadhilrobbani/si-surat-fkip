<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Masuk ke {{ $client->name }} | e-Surat FKIP</title>
    {{--
        Halaman login khusus alur SSO. Tampilannya mengikuti aplikasi klien
        (e-Skripsi FKIP: Material 3, navy & kuning UNIB) agar mahasiswa tidak
        merasa berpindah aplikasi. Login biasa e-Surat tetap memakai auth.login.
    --}}
    <style>
        :root {
            --navy: oklch(0.33 0.07 265);
            --kuning: oklch(0.88 0.1 95);
            --primer: oklch(0.37 0.09 264);
            --latar: oklch(0.975 0.006 262);
            --teks: oklch(0.22 0.02 268);
            --teks-redup: oklch(0.45 0.025 268);
            --garis: oklch(0.6 0.02 268);
            --tonal: oklch(0.975 0.008 262);
            --error-latar: oklch(0.935 0.04 25);
            --error-teks: oklch(0.38 0.13 27);
        }

        body {
            font-family: 'Public Sans', ui-sans-serif, system-ui, sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-[var(--latar)] text-[var(--teks)] antialiased">
    <div class="flex min-h-screen lg:grid lg:grid-cols-2 lg:p-3">
        <aside class="relative hidden overflow-hidden rounded-[28px] bg-[var(--navy)] text-white lg:flex lg:flex-col">
            <img src="{{ asset('images/rektorat-unib.jpg') }}" alt="Gedung Rektorat Universitas Bengkulu"
                class="absolute inset-x-0 top-0 h-[68%] w-full object-cover object-center saturate-[0.8]">
            <div aria-hidden="true" class="absolute inset-0"
                style="background: linear-gradient(to bottom, color-mix(in oklab, var(--navy) 35%, transparent) 0%, transparent 25%, color-mix(in oklab, var(--navy) 70%, transparent) 48%, var(--navy) 68%);">
            </div>

            <div class="relative p-8 xl:p-10">
                <div class="inline-flex items-center gap-3 rounded-2xl py-2 pl-2 pr-5 ring-1 ring-white/15 backdrop-blur-md"
                    style="background: color-mix(in oklab, var(--navy) 55%, transparent);">
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-white">
                        <img src="{{ asset('images/logounib.png') }}" alt="" class="h-7 w-7 object-contain">
                    </span>
                    <span class="leading-tight">
                        <span class="block text-base font-medium">Universitas Bengkulu</span>
                        <span class="block text-sm text-white/85">Fakultas Keguruan dan Ilmu Pendidikan</span>
                    </span>
                </div>
            </div>

            <div class="relative mt-auto flex flex-col gap-6 p-10 xl:p-12">
                <div class="flex flex-col gap-2">
                    <p class="text-sm font-medium text-[var(--kuning)]">Masuk dengan akun e-Surat</p>
                    <h2 class="text-5xl font-normal leading-none">{{ $client->name }}</h2>
                    @if ($client->description)
                        <p class="mt-2 max-w-md text-base leading-relaxed text-white/75">{{ $client->description }}</p>
                    @endif
                </div>
            </div>
        </aside>

        <main class="flex flex-1 flex-col">
            <div class="flex items-center px-6 py-4 sm:px-10 lg:invisible">
                <span class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logounib.png') }}" alt="Logo Universitas Bengkulu" class="h-8 w-8 object-contain">
                    <span class="text-base font-medium">{{ $client->name }}</span>
                </span>
            </div>

            <div class="flex flex-1 items-center justify-center px-6 py-10 sm:px-10">
                <div class="w-full max-w-sm">
                    <header class="mb-8">
                        <h1 class="text-[2rem] font-normal leading-tight">Masuk</h1>
                        <p class="mt-2 text-sm leading-relaxed text-[var(--teks-redup)]">
                            Gunakan akun e-Surat Anda untuk melanjutkan ke {{ $client->name }}.
                        </p>
                    </header>

                    @if ($errors->any())
                        <div role="alert"
                            class="mb-5 rounded-xl bg-[var(--error-latar)] px-4 py-3 text-sm text-[var(--error-teks)]">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div role="status" class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('authLogin') }}" method="POST" class="flex flex-col gap-4"
                        x-data="{ lihat: false, kirim: false }" x-on:submit="kirim = true">
                        @csrf

                        <div class="grid gap-2">
                            <label for="username" class="text-sm font-medium">Email atau username</label>
                            <input type="text" id="username" name="username" value="{{ old('username') }}" required
                                autofocus autocomplete="username" placeholder="nama@unib.ac.id"
                                class="h-11 w-full rounded-lg border border-[var(--garis)] bg-transparent px-3.5 text-sm text-[var(--teks)] shadow-none placeholder:text-[var(--teks-redup)] hover:border-[var(--teks)] focus:border-[var(--primer)] focus:ring-1 focus:ring-[var(--primer)]">
                        </div>

                        <div class="grid gap-2">
                            <div class="flex items-center">
                                <label for="password" class="text-sm font-medium">Kata sandi</label>
                                <a href="{{ route('password.request') }}"
                                    class="ml-auto text-sm font-medium text-[var(--primer)] hover:underline">Lupa kata sandi?</a>
                            </div>
                            <div class="relative">
                                <input x-bind:type="lihat ? 'text' : 'password'" type="password" id="password"
                                    name="password" required autocomplete="current-password" placeholder="Kata sandi"
                                    class="h-11 w-full rounded-lg border border-[var(--garis)] bg-transparent pl-3.5 pr-12 text-sm text-[var(--teks)] shadow-none placeholder:text-[var(--teks-redup)] hover:border-[var(--teks)] focus:border-[var(--primer)] focus:ring-1 focus:ring-[var(--primer)]">
                                <button type="button" x-on:click="lihat = !lihat"
                                    x-bind:aria-label="lihat ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                                    aria-label="Tampilkan kata sandi"
                                    class="absolute inset-y-0 right-1 my-auto grid h-9 w-9 place-items-center rounded-full text-[var(--teks-redup)] hover:bg-black/5">
                                    <svg x-show="!lihat" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <svg x-show="lihat" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="M9.9 4.2A10 10 0 0 1 12 4c6.4 0 10 8 10 8a17 17 0 0 1-2.2 3.2M6.6 6.6C3.9 8.3 2 12 2 12s3.6 8 10 8a9.7 9.7 0 0 0 5.4-1.6M3 3l18 18M9.9 9.9a3 3 0 0 0 4.2 4.2" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit" x-bind:disabled="kirim"
                            class="mt-2 h-12 w-full rounded-full bg-[var(--primer)] px-8 text-base font-medium text-white transition-shadow hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--primer)] focus-visible:ring-offset-2 disabled:opacity-60">
                            <span x-show="!kirim">Masuk</span>
                            <span x-show="kirim" x-cloak>Memproses…</span>
                        </button>
                    </form>

                    <div class="mt-6 flex flex-col gap-3">
                        <p class="rounded-2xl bg-[var(--tonal)] px-4 py-3.5 text-sm text-[var(--teks-redup)]">
                            Belum punya akun e-Surat?
                            <a href="/register" class="font-medium text-[var(--primer)] hover:underline">Daftar di sini</a>
                        </p>
                        @if ($kembaliUrl)
                            <a href="{{ $kembaliUrl }}"
                                class="inline-flex w-fit items-center gap-2 text-sm font-medium text-[var(--teks-redup)] hover:text-[var(--teks)]">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M19 12H5M12 19l-7-7 7-7" />
                                </svg>
                                Batal, kembali ke {{ $client->name }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <footer class="px-6 py-5 text-sm text-[var(--teks-redup)] sm:px-10">
                &copy; {{ date('Y') }} FKIP Universitas Bengkulu · Layanan akun oleh e-Surat FKIP
            </footer>
        </main>
    </div>
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>[x-cloak] { display: none !important; }</style>
</body>

</html>
