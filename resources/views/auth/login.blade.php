<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>
    <title>Login | E-surat FKIP</title>
    <style>
        .toggle-password {
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-slate-50 h-screen flex items-center justify-center">
    <x-notification />

    <main class="w-full max-w-md bg-white shadow-lg rounded-lg p-8">
        <!-- Logo dan Judul -->
        <div class="flex flex-col items-center mb-6">
            <img class="w-20 mb-4" src="{{ asset('images/logounib.png') }}" alt="Logo UNIB">
            <h1 class="text-xl font-semibold text-slate-700 text-center">Surat Menyurat Akademik FKIP</h1>
            <p class="text-sm text-slate-500 text-center">Silakan masuk dengan akun terdaftar</p>
        </div>

        <!-- Form Login -->
        <form action="{{ route('authLogin') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Username/Email -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Email atau Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan email atau username"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                    value="{{ old('username') }}" required>
                @error('username')
                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                <div class="relative">
                    <input type="password" id="password" name="password" placeholder="•••••••••"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                        value="{{ old('password') }}" required>
                    <span class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center"
                        onclick="togglePassword()">
                        <svg id="eye-icon" class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-width="2"
                                d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                            <path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </span>
                </div>
                @error('password')
                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Links -->
            <div class="flex flex-col items-start space-y-1 text-sm text-blue-600">
                <a href="/register" class="hover:underline">Tidak punya akun? Daftar di sini</a>
                <a href="{{ route('password.request') }}" class="hover:underline">Lupa Kata Sandi?</a>
                <a href="{{ route('report-bug') }}" class="hover:underline">Laporkan bug/error</a>
            </div>

            <!-- Tombol Masuk -->
            <button type="submit"
                class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Masuk
            </button>
        </form>
    </main>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            // Toggle the type attribute
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Change the icon based on the visibility state
            if (type === 'password') {
                eyeIcon.innerHTML =
                    `<path stroke="currentColor" stroke-width="2" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"/>
                                     <path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>`;
            } else {
                eyeIcon.innerHTML =
                    `<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.933 13.909A4.357 4.357 0 0 1 3 12c0-1 4-6 9-6m7.6 3.8A5.068 5.068 0 0 1 21 12c0 1-3 6-9 6-.314 0-.62-.014-.918-.04M5 19 19 5m-4 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>`;
            }
        }
    </script>
</body>

</html>
