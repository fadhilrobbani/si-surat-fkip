<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="//unpkg.com/alpinejs" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Register | E-surat FKIP</title>
</head>

<body class="bg-slate-50">
    <x-notification/>
    <main class="mx-auto py-6 w-full md:w-3/4 bg-white shadow-lg justify-center items-center flex flex-col">
        <div class="flex flex-col justify-center items-center gap-2">
            <img class="w-28" src="{{ asset('images/logounib.png') }}" alt="logounib" />
            <div class="flex flex-col gap-0 m-0 p-0">
                <h1 class="text-xl  text-slate-700 font-semibold text-center mb-4 mx-auto">Verifikasi Email Anda
                </h1>
                <h1 class=" text-sm mt-0 text-slate-700  text-center mb-4 mx-auto">Pendaftaran Akun Baru </h1>
            </div>

        </div>
        <a href="/logout">Logout</a>



    </main>
</body>

</html>
