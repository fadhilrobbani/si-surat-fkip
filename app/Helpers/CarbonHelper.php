<?php

use Carbon\Carbon;

if (!function_exists('formatTimestampToIndonesian')) {
    function formatTimestampToIndonesian($timestamp)
    {
        setlocale(LC_TIME, 'id_ID'); // Set locale ke bahasa Indonesia
        $carbonTimestamp = Carbon::parse($timestamp);
        return $carbonTimestamp->formatLocalized('%d %B %Y %H:%M:%S');
    }
}
