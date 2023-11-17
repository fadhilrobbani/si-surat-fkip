<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cek Keaslian Surat</title>
    <script src="//unpkg.com/alpinejs" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <main class="mt-10 flex flex-col justify-center items-center">

        <h1 class="mx-auto text-center font-bold">Halaman Validasi Keaslian Surat</h1>
        <br>
        <div class="flex flex-col gap-4 md:flex-row justify-evenly items-start">

            <div class="relative overflow-x-auto shadow-lg sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <tbody>

                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Nama:&nbsp;
                            </td>
                            <td class="px-6 py-4">{{ $surat->data['name'] }}
                            </td>
                        </tr>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Nomor Pokok Mahasiswa
                                (NPM):&nbsp;
                            </td>
                            <td class="px-6 py-4">{{ $surat->data['username'] }}
                            </td>
                        </tr>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Jenis Surat:&nbsp;
                            </td>
                            <td class="px-6 py-4">{{ $surat->jenisSurat->name }}
                            </td>
                        </tr>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Nomor Surat:&nbsp;
                            </td>
                            <td class="px-6 py-4">{{ $surat->data['noSurat'] }}
                            </td>
                        </tr>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Tanggal Surat
                                Diterbitkan:&nbsp;
                            </td>
                            <td class="px-6 py-4">{{ $surat->data['tanggal_selesai'] }}
                            </td>
                        </tr>




                    </tbody>
                </table>
            </div>


        </div>


        <a href="{{ route('preview-surat-qr', $surat->id) }}"
            class="m-10 p-2 text-white  text-center bg-blue-600 hover:bg-blue-700 rounded-lg"><button>Preview</button></a>



    </main>
</body>

</html>
