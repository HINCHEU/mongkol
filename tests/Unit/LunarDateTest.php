<?php

use App\Models\Guest;
use App\Services\LunarDate;
use Carbon\CarbonImmutable;

it('gives the Khmer lunar date for a known day', function () {
    // Visak Bochea 2024: full moon of Pisak.
    expect(LunarDate::for(CarbonImmutable::parse('2024-05-22')))
        ->toBe('ថ្ងៃពុធ ១៥ កើត ខែពិសាខ ឆ្នាំរោង ឆស័ក ពុទ្ធសករាជ ២៥៦៧');
});

it('uses the calendar day in Phnom Penh, not UTC', function () {
    // 18:00 UTC on the 21st is already 01:00 on the 22nd in Cambodia.
    expect(LunarDate::for(CarbonImmutable::parse('2024-05-21 18:00', 'UTC')))
        ->toBe(LunarDate::for(CarbonImmutable::parse('2024-05-22')));
});

it('builds guest codes from 57 characters without look-alikes', function () {
    expect(strlen(Guest::CODE_ALPHABET))->toBe(57)
        ->and(count(array_unique(str_split(Guest::CODE_ALPHABET))))->toBe(57)
        ->and(Guest::CODE_ALPHABET)->not->toContain('0')->not->toContain('O')
        ->not->toContain('1')->not->toContain('I')->not->toContain('l');
});
