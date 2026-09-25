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

