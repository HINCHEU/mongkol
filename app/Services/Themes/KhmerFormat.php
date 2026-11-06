<?php

namespace App\Services\Themes;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

// Dates and times as DESIGN.md §11 writes them, always in Phnom Penh time.
class KhmerFormat
{
    private const DIGITS = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];

    private const DAYS = ['អាទិត្យ', 'ច័ន្ទ', 'អង្គារ', 'ពុធ', 'ព្រហស្បតិ៍', 'សុក្រ', 'សៅរ៍'];

    private const MONTHS = ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'];

    public const TIMEZONE = 'Asia/Phnom_Penh';

    public static function digits(string|int $value): string
    {
        return strtr((string) $value, self::DIGITS);
    }

    /** ថ្ងៃសៅរ៍ ទី១២ ខែធ្នូ ឆ្នាំ២០២៦ · Saturday, 12 December 2026 */
    public static function date(CarbonInterface $date, string $lang): string
    {
        $date = CarbonImmutable::instance($date)->setTimezone(self::TIMEZONE);

        if ($lang !== 'km') {
            return $date->locale('en')->isoFormat('dddd, D MMMM YYYY');
        }

        return sprintf(
            'ថ្ងៃ%s ទី%s ខែ%s ឆ្នាំ%s',
            self::DAYS[$date->dayOfWeek],
            self::digits($date->day),
            self::MONTHS[$date->month - 1],
            self::digits($date->year),
        );
    }

    /** ម៉ោង ៧:០០ ព្រឹក · 7:00 AM */
    public static function time(CarbonInterface $time, string $lang): string
    {
        $time = CarbonImmutable::instance($time)->setTimezone(self::TIMEZONE);

        if ($lang !== 'km') {
            return $time->format('g:i A');
        }

        $period = match (true) {
            $time->hour >= 5 && $time->hour < 12 => 'ព្រឹក',
            $time->hour >= 12 && $time->hour < 17 => 'រសៀល',
            $time->hour >= 17 && $time->hour < 19 => 'ល្ងាច',
            default => 'យប់',
        };

        return 'ម៉ោង '.self::digits($time->format('g:i')).' '.$period;
    }
}
