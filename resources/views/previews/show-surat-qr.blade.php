<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Cek Keaslian Surat</title>
    <script src="//unpkg.com/alpinejs" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-800 font-sans">
    <main class="max-w-lg mx-auto p-6 bg-white rounded-lg shadow-md">
        <img class="w-28 mx-auto mb-5" src="{{ asset('images/logounib.png') }}" alt="logounib" />

        <h1 class="text-2xl font-bold text-center text-blue-800">Halaman Validasi Keaslian Surat</h1>
        <p class="text-center text-gray-700 mt-2 mb-6">Dengan ini menyatakan bahwa surat ini adalah benar
            diterbitkan dari FKIP UNIB. </p>

        @if ($surat->jenisSurat->user_type == 'mahasiswa')
            <div class="overflow-x-auto shadow-lg rounded-lg">
                <table class="w-full text-sm text-left text-gray-700 bg-white">
                    <tbody>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Nomor Surat:</td>
                            <td class="px-4 py-3">{{ $surat->data['noSurat'] }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Tanggal Surat Diterbitkan:</td>
                            <td class="px-4 py-3">{{ $surat->data['tanggal_selesai'] }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Nama:</td>
                            <td class="px-4 py-3">{{ $surat->data['nama'] }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">NPM:</td>
                            <td class="px-4 py-3">
                                {{ isset($surat->data['npm']) ? $surat->data['npm'] : $surat->data['username'] }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Jenis Surat:</td>
                            <td class="px-4 py-3">{{ $surat->jenisSurat->name }}</td>
                        </tr>

                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Ditandatangani oleh:</td>
                            <td class="px-4 py-3">
                                {{ $surat->data['private']['namaWD1'] ?? ($surat->data['private']['namaWD'] ?? ($surat->data['private']['namaDekan'] ?? '(Nama tidak tersedia)')) }}
                            </td>
                        </tr>

                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50"></td>
                            <td class="px-4 py-3">
                                {{ $surat->data['private']['deksripsiWD1'] ?? ($surat->data['private']['deskripsiWD'] ?? ($surat->data['private']['deskripsiDekan'] ?? '(Nama tidak tersedia)')) }}
                            </td>
                        </tr>

                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50"></td>
                            <td class="px-4 py-3">
                                {{ $surat->data['private']['nipWD1'] ?? ($surat->data['private']['nipWD'] ?? ($surat->data['private']['nipDekan'] ?? '(Nama tidak tersedia)')) }}
                            </td>
                        </tr>


                    </tbody>
                </table>
            </div>
        @endif

        @if (
            ($surat->jenisSurat->user_type == 'staff' && $surat->jenisSurat->slug == 'surat-tugas') ||
                ($surat->jenisSurat->user_type == 'staff' && $surat->jenisSurat->slug == 'surat-tugas-kelompok'))
            <div class="overflow-x-auto shadow-lg rounded-lg">
                <table class="w-full text-sm text-left text-gray-700 bg-white">
                    <tbody>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Nomor Surat:</td>
                            <td class="px-4 py-3">
                                {{ $surat->data['noSurat'] . '/UN30.7/KP/' . $surat->created_at->year }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Tanggal Surat Diterbitkan:</td>
                            <td class="px-4 py-3">{{ $surat->data['tanggal_selesai'] }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Jenis Surat / Perihal:</td>
                            <td class="px-4 py-3">{{ $surat->jenisSurat->name }}</td>
                        </tr>

                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Ditandatangani oleh:</td>
                            <td class="px-4 py-3">
                                {{ $surat->data['private']['namaWD1'] ?? ($surat->data['private']['namaWD'] ?? ($surat->data['private']['namaDekan'] ?? '(Nama tidak tersedia)')) }}
                            </td>
                        </tr>

                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50"></td>
                            <td class="px-4 py-3">
                                {{ $surat->data['private']['deksripsiWD1'] ?? ($surat->data['private']['deskripsiWD'] ?? ($surat->data['private']['deskripsiDekan'] ?? '(Nama tidak tersedia)')) }}
                            </td>
                        </tr>

                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50"></td>
                            <td class="px-4 py-3">
                                {{ $surat->data['private']['nipWD1'] ?? ($surat->data['private']['nipWD'] ?? ($surat->data['private']['nipDekan'] ?? '(Nama tidak tersedia)')) }}
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        @endif

        @if ($surat->jenisSurat->user_type == 'staff-dekan' && $surat->jenisSurat->slug == 'surat-keluar')
            <div class="overflow-x-auto shadow-lg rounded-lg">
                <table class="w-full text-sm text-left text-gray-700 bg-white">
                    <tbody>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Nomor Surat:</td>
                            <td class="px-4 py-3">
                                {{ $surat->data['noSurat'] . '/UN30.7/PP/' . $surat->created_at->year }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Tanggal Surat Diterbitkan:</td>
                            <td class="px-4 py-3">{{ $surat->data['tanggal_selesai'] }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Jenis Surat / Perihal:</td>
                            <td class="px-4 py-3">{{ $surat->data['perihal'] }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Ditandatangani oleh:</td>
                            <td class="px-4 py-3">
                                {{ $surat->data['private']['namaWD1'] ?? ($surat->data['private']['namaWD'] ?? ($surat->data['private']['namaDekan'] ?? '(Nama tidak tersedia)')) }}
                            </td>
                        </tr>

                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50"></td>
                            <td class="px-4 py-3">
                                {{ $surat->data['private']['deksripsiWD1'] ?? ($surat->data['private']['deskripsiWD'] ?? ($surat->data['private']['deskripsiDekan'] ?? '(Nama tidak tersedia)')) }}
                            </td>
                        </tr>

                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50"></td>
                            <td class="px-4 py-3">
                                {{ $surat->data['private']['nipWD1'] ?? ($surat->data['private']['nipWD'] ?? ($surat->data['private']['nipDekan'] ?? '(Nama tidak tersedia)')) }}
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        @endif


        @php
            $url = URL::signedRoute('cetak-surat-qr', ['surat' => $surat->id]);
        @endphp
        <p class="text-center text-sm text-gray-700 mt-4 mb-6">Untuk memastikan bahwa Anda mengakses data surat yang
            benar, pastikan URL pada
            browser berasal dari <a class="underline" href=" https://esurat-fkip.unib.ac.id">
                https://esurat-fkip.unib.ac.id</a></p>

        {{-- <a href="{{ route('lihat-html-surat-qr', ['surat' => $surat->id]) }}">html</a> --}}
        {{-- <iframe src="{{ $url }}" width="100%" height="600"></iframe> --}}
        <div id="pdf-viewer" style="position: relative; max-width: 100%; margin: auto;">
            <!-- Loading Animation -->
            <div id="loading-animation" class="mt-2 flex flex-row text-blue-800 italic"
                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                <p>Loading PDF...</p>
                <x-loading />
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
        <script>
            const pdfUrl = "{{ $url }}";
            const loadingTask = pdfjsLib.getDocument(pdfUrl);
            const viewer = document.getElementById('pdf-viewer');
            const loadingAnimation = document.getElementById('loading-animation');

            loadingTask.promise.then(pdf => {
                pdf.getPage(1).then(page => {
                    // Scale canvas to match the container's width
                    const viewport = page.getViewport({
                        scale: 1
                    });
                    const scale = viewer.clientWidth / viewport.width;
                    const scaledViewport = page.getViewport({
                        scale
                    });

                    // Create and setup the canvas
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.width = scaledViewport.width;
                    canvas.height = scaledViewport.height;

                    viewer.appendChild(canvas);

                    // Render the PDF page
                    page.render({
                        canvasContext: context,
                        viewport: scaledViewport
                    });

                    // Hide the loading animation after rendering
                    loadingAnimation.style.display = 'none';
                });
            }).catch(error => {
                console.error('Error loading PDF:', error);
                loadingAnimation.innerHTML = '<p>Gagal memuat PDF.</p>';
            });

            // Optional: Re-render on window resize to maintain responsive layout
            window.addEventListener('resize', () => {
                viewer.innerHTML = '';
                viewer.appendChild(loadingAnimation);
                loadingAnimation.style.display = 'block';
                loadingTask.promise.then(pdf => pdf.getPage(1).then(page => {
                    const scale = viewer.clientWidth / page.getViewport({
                        scale: 1
                    }).width;
                    const viewport = page.getViewport({
                        scale
                    });

                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;

                    viewer.appendChild(canvas);
                    page.render({
                        canvasContext: context,
                        viewport: viewport
                    });
                    loadingAnimation.style.display = 'none';
                }));
            });
        </script>


        <a href="{{ $url }}"
            class="block text-center mt-14 px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700">Unduh</a>
    </main>
</body>

</html>
