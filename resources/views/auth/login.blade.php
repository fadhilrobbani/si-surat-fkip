<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>
    <title>Login | E-surat FKIP</title>
</head>

<body class="relative bg-slate-50 h-screen flex flex-col items-center justify-center ">
    <x-notification />
    <main class=" py-6 w-[400px] bg-white shadow-xl rounded-lg justify-center items-center flex flex-col">
        <div class="flex flex-col justify-center items-center gap-2">
            <img class="w-28" src="{{ asset('images/logounib.png') }}" alt="logounib" />
            <div class="flex flex-col gap-0 m-0 p-0">
                <h1 class="text-xl  text-slate-700 font-semibold text-center mb-4 mx-auto">Surat Menyurat
                    Akademik FKIP
                </h1>
                <h1 class=" text-sm mt-0 text-slate-700  text-center mb-4 mx-auto">Silahkan login dengan
                    akun
                    yang
                    terdaftar </h1>
            </div>

        </div>

        <form action="{{ route('authLogin') }}" class="flex justify-center mx-auto flex-col px-12  w-full"
            method="POST">
            @csrf
            <div class="mb-6">
                <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username
                </label>
                <input type="text" id="username" name="username"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan NPM atau username" value="{{ old('username') }}" required>
                @error('username')
                    <span
                        class="bg-red-100 text-red-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                        {{ $message }}
                    </span>
                @enderror
            </div>


            <div class="mb-6">
                <label for="password"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                <input type="password" id="password" name="password" value="{{ old('password') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="•••••••••" required>
                @error('password')
                    <span
                        class="bg-red-100 text-red-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                        {{ $message }}
                    </span>
                @enderror
            </div>
            <label for="register">Tidak punya akun?
                <a href="/register" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Daftar di sini</a>
            </label>
            <button type="submit"
                class="text-white mt-4 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
        </form>

    </main>

</body>

</html>
