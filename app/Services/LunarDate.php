<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

// The Khmer lunar date line for a wedding, e.g. ថ្ងៃសៅរ៍ ៣ កើត ខែមិគសិរ ឆ្នាំមមី អដ្ឋស័ក ពុទ្ធសករាជ ២៥៧០.
class LunarDate
{
    public static function for(CarbonInterface $date): string
    {
        // Always the calendar day in Cambodia: a UTC time after 17:00 is already the next day there.
        return toLunarDate(CarbonImmutable::instance($date)->setTimezone('Asia/Phnom_Penh'))->toString();
    }
}
