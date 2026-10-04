<?php

//ke format ex: Minggu, 23 Oktober 2023 12:45:30
if (!function_exists('convertToTitleCase')) {
    function convertToTitleCase($variableName)
    {
        // Memisahkan kata berdasarkan huruf besar, tetapi mempertahankan angka
        $words = preg_split('/(?<=\D)(?=\d)|(?=\D)(?<=\d)|(?=[A-Z])/', $variableName);

        // Menggabungkan kata-kata yang telah dipisahkan dengan spasi
        $joinedWords = implode(' ', $words);

        // Mengubah kata-kata yang telah digabungkan ke dalam title case
        $titleCase = ucwords($joinedWords);

        return $titleCase;
    }
}

if (!function_exists('formatNomorSurat')) {
    function formatNomorSurat($noSurat, $defaultEmpty = '....................................................', $dotsPrefix = '........')
    {
        if (empty($noSurat) || trim($noSurat) === '') {
            return $defaultEmpty;
        }

        $trimmed = trim($noSurat);
        if (str_starts_with($trimmed, '/')) {
            return $dotsPrefix . $trimmed;
        }

        return $noSurat;
    }
}

if (!function_exists('formatTanggalSurat')) {
    function formatTanggalSurat($tanggal, $defaultEmpty = '....................')
    {
        if (empty($tanggal) || trim((string) $tanggal) === '') {
            return $defaultEmpty;
        }

        $trimmed = trim((string) $tanggal);

        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $trimmed)) {
            try {
                return formatTimestampToOnlyDateIndonesian($trimmed);
            } catch (\Throwable $e) {
                return $trimmed;
            }
        }

        return $trimmed;
    }
}

if (!function_exists('formatNomorSuratDenganFormat')) {
    function formatNomorSuratDenganFormat($noSurat, $suffix, $surat, $defaultEmpty = '....................................................')
    {
        if (empty($noSurat) || trim((string) $noSurat) === '') {
            return $defaultEmpty;
        }

        $trimmed = trim((string) $noSurat);
        if (str_starts_with($trimmed, '/')) {
            return '........' . $trimmed;
        }

        if (!str_contains($trimmed, '/')) {
            $data = is_array($surat) ? ($surat['data'] ?? []) : ($surat->data ?? []);
            $createdAt = is_array($surat) ? ($surat['created_at'] ?? null) : ($surat->created_at ?? null);

            $tahunSurat = !empty($data['tanggal_selesai'])
                ? \Illuminate\Support\Str::of($data['tanggal_selesai'])->afterLast(' ')
                : ($createdAt ? (is_string($createdAt) ? date('Y', strtotime($createdAt)) : $createdAt->format('Y')) : date('Y'));

            $normalizedSuffix = '/' . trim($suffix, '/') . '/';
            return $trimmed . $normalizedSuffix . $tahunSurat;
        }

        return $trimmed;
    }
}

