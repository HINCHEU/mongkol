<?php

use App\Services\Themes\KhmerFormat;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

it('writes dates in Khmer and English', function () {
    $date = CarbonImmutable::parse('2026-12-12', 'Asia/Phnom_Penh');

    expect(KhmerFormat::date($date, 'km'))->toBe('ថ្ងៃសៅរ៍ ទី១២ ខែធ្នូ ឆ្នាំ២០២៦')
        ->and(KhmerFormat::date($date, 'en'))->toBe('Saturday, 12 December 2026');
});

it('writes times with the Khmer part of day, in Phnom Penh time', function (string $utc, string $km, string $en) {
    $time = CarbonImmutable::parse($utc, 'UTC');

    expect(KhmerFormat::time($time, 'km'))->toBe($km)
        ->and(KhmerFormat::time($time, 'en'))->toBe($en);
})->with([
    'morning' => ['2026-12-12 00:00', 'ម៉ោង ៧:០០ ព្រឹក', '7:00 AM'],
    'afternoon' => ['2026-12-12 06:30', 'ម៉ោង ១:៣០ រសៀល', '1:30 PM'],
    'evening' => ['2026-12-12 10:00', 'ម៉ោង ៥:០០ ល្ងាច', '5:00 PM'],
    'night' => ['2026-12-12 12:30', 'ម៉ោង ៧:៣០ យប់', '7:30 PM'],
]);

it('converts digits and leaves the caller’s date alone', function () {
    $date = Carbon::parse('2026-12-11 20:00', 'UTC');

    expect(KhmerFormat::digits('2026-12'))->toBe('២០២៦-១២')
        ->and(KhmerFormat::date($date, 'km'))->toBe('ថ្ងៃសៅរ៍ ទី១២ ខែធ្នូ ឆ្នាំ២០២៦')
        ->and($date->timezoneName)->toBe('UTC');
});
