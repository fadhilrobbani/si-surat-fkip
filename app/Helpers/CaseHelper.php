<?php

//ke format ex: Minggu, 23 Oktober 2023 12:45:30
if (!function_exists('convertToTitleCase')) {
    function convertToTitleCase($variableName)
    {
        // Memisahkan kata berdasarkan huruf besar dan angka menggunakan preg_split
        $words = preg_split('/(?=[A-Z0-9])/', $variableName);

        // Menggabungkan kata-kata yang telah dipisahkan dengan spasi
        $joinedWords = implode(' ', $words);

        // Mengubah kata-kata yang telah digabungkan ke dalam title case
        $titleCase = ucwords($joinedWords);

        return $titleCase;
    }
}
