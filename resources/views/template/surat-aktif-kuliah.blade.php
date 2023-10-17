<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Resmi</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="p-6">
    <div class="max-w-2xl mx-auto bg-white shadow p-6">
        <!-- Kepala Surat -->
        <div class="mb-6 text-center">
            <img src="logo.png" alt="Logo Perusahaan" class="w-16 h-16 mx-auto mb-2">
            <h1 class="text-2xl font-semibold">UNIVERSITAS BENGKULU</h1>
            <p>Alamat Perusahaan</p>
            <p>Kota, Kode Pos</p>
            <p>No. Telepon: (123) 456-7890</p>
            <p>Email: info@perusahaan.com</p>
        </div>

        <!-- Alamat Penerima -->
        <div class="mb-6">
            <p class="font-semibold">Kepada:</p>
            <address>
                Nama Penerima<br>
                Alamat Penerima<br>
                Kota, Kode Pos<br>
            </address>
        </div>

        <!-- Tanggal Surat dan Perihal -->
        <div class="mb-6">
            <p class="font-semibold">Tanggal:</p>
            <p>Tanggal Surat</p>
            <p class="font-semibold mt-2">Perihal:</p>
            <p>Perihal Surat</p>
        </div>

        <!-- Isi Surat -->
        <div class="mb-6">
            <p class="font-semibold">Isi Surat:</p>
            <p>Ini adalah isi surat resmi. Anda dapat menambahkan lebih banyak teks di sini sesuai dengan kebutuhan
                Anda.</p>
        </div>

        <!-- Tanda Tangan -->
        <div class="mb-6">
            <p class="font-semibold">Hormat kami,</p>
            <img src="tanda_tangan.png" alt="Tanda Tangan" class="w-32 h-16">
            <p>{{ $mahasiswa->name }}</p>
            <p>Jabatan</p>
        </div>
    </div>
</body>

</html>
