<?php

namespace App\Services;

use Carbon\Carbon;

class DateService
{
    /**
     * Format selisih waktu menjadi hari, jam, dan menit.
     *
     * @param string $timestamp
     * @return string
     */
    public function formatTimestampToDiffDays($timestamp)
    {
        setlocale(LC_TIME, 'id_ID'); // Set locale ke bahasa Indonesia
        Carbon::setLocale('id');

        $carbonTimestamp = Carbon::parse($timestamp);
        $now = Carbon::now();

        $diff = $now->diff($carbonTimestamp);

        $days = $diff->d;
        $hours = $diff->h;
        $minutes = $diff->i;

        if ($now->greaterThan($carbonTimestamp)) {
            return '0 hari 0 jam 0 menit';
        }

        return "{$days} hari {$hours} jam {$minutes} menit";
    }
}
