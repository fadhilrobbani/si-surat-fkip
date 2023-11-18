<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="//unpkg.com/alpinejs" defer></script>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Lupa Password | E-surat FKIP</title>
</head>

<body class="bg-slate-50 flex justify-center items-center h-screen">
    <x-notification />
    <main class="mx-auto relative py-6 w-full md:w-3/4 bg-white shadow-lg justify-center items-center flex flex-col">
        <div class="flex flex-col justify-center items-center gap-2">
            <img class="w-28" src="{{ asset('images/logounib.png') }}" alt="logounib" />
            <div class="flex flex-col gap-0 m-0 p-0">
                <h1 class="text-xl  text-slate-700 font-semibold text-center mb-4 mx-auto">Halaman Lupa Password
                </h1>
                <h1 class=" text-md mt-0 text-slate-700  text-center mb-4 mx-auto">Jika Anda lupa password akun, Anda
                    dapat mereset password dengan mengisi form reset password di bawah. <br>Kami akan mengirim link
                    untuk Anda dapat mereset password via email yang terdaftar</h1>
                <h1 class=" text-md mt-0 text-slate-700  text-center mb-4 mx-auto"></h1>
                <form action="{{ route('password.email') }}" method="POST">
                    @csrf
                    @method('post')
                    <div class="mb-6">
                        <label for="email"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Masukkan Email yang
                            terdaftar
                        </label>
                        <input type="text" id="email" name="email"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="emailyangterdaftar@email.com" value="{{ old('email') }}" required>
                        @error('email')
                            <span
                                class="bg-red-100 text-red-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                    <button type="submit"
                        class="text-white mt-4 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Kirim</button>
                </form>
            </div>

        </div>



    </main>
</body>

</html>
