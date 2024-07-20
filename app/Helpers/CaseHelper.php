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
