<?php

use Carbon\Carbon;


//ke format ex: Minggu, 23 Oktober 2023 12:45:30
if (!function_exists('formatTimestampToIndonesian')) {
    function formatTimestampToIndonesian($timestamp)
    {
        setlocale(LC_TIME, 'id_ID'); // Set locale ke bahasa Indonesia
        Carbon::setLocale('id');
        $carbonTimestamp = Carbon::parse($timestamp);
        return $carbonTimestamp->isoFormat('dddd, D MMMM H:mm:ss');
    }
}

//ke format ex: Minggu, 23 Oktober 2023
if (!function_exists('formatTimestampToDateIndonesian')) {
    function formatTimestampToDateIndonesian($timestamp)
    {
        setlocale(LC_TIME, 'id_ID'); // Set locale ke bahasa Indonesia
        Carbon::setLocale('id');
        $carbonTimestamp = Carbon::parse($timestamp);
        return $carbonTimestamp->isoFormat('dddd, D MMMM Y');
    }
}

//ke format ex: 23 Oktober 2023
if (!function_exists('formatTimestampToOnlyDateIndonesian')) {
    function formatTimestampToOnlyDateIndonesian($timestamp)
    {
        setlocale(LC_TIME, 'id_ID'); // Set locale ke bahasa Indonesia
        Carbon::setLocale('id');
        $carbonTimestamp = Carbon::parse($timestamp);
        return $carbonTimestamp->isoFormat('D MMMM Y');
    }
}

//ke format ex: 2 hari yang lalu
if (!function_exists('formatTimestampToDiff')) {
    function formatTimestampToDiff($timestamp)
    {
        setlocale(LC_TIME, 'id_ID'); // Set locale ke bahasa Indonesia
        Carbon::setLocale('id');
        $carbonTimestamp = Carbon::parse($timestamp);
        return $carbonTimestamp->diffForHumans();
    }
}

if (!function_exists('formatTimestampToDiffDays')) {
    function formatTimestampToDiffDays($timestamp)
    {
        setlocale(LC_TIME, 'id_ID'); // Set locale ke bahasa Indonesia
        Carbon::setLocale('id');
        $carbonTimestamp = Carbon::parse($timestamp);
        $now = Carbon::now();
        return $now->diffInDays($carbonTimestamp);
    }
}


//dari format yyyy:mm:dd ke misalnya Minggu, 28 Oktober 2023
if (!function_exists('formatDateToText')) {
    function formatDateToText($date)
    {
        setlocale(LC_TIME, 'id_ID'); // Set locale ke bahasa Indonesia
        Carbon::setLocale('id');
        list($year, $month, $day) = explode(':', $date);
        $carbonDate = Carbon::create($year, $month, $day);
        $formattedDate = $carbonDate->isoFormat('dddd, D MMMM YYYY');
        return $formattedDate;
    }
}
