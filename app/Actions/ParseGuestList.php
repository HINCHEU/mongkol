<?php

namespace App\Actions;

use App\Models\Guest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Spatie\SimpleExcel\SimpleExcelReader;

/**
 * Turns pasted lines or an Excel/CSV file into guest rows for the import preview. Nothing is saved
 * here; the couple checks the rows, then they are validated and created like any other guests.
 */
class ParseGuestList
{
    public const MAX_ROWS = 2000;

    // Header names accepted in a spreadsheet, English (what export writes) and Khmer.
    private const HEADERS = [
        'name' => ['name', 'ឈ្មោះ'],
        'title' => ['title', 'ងារ', 'គោរមងារ'],
        'suffix' => ['suffix', 'បន្ថែម'],
        'side' => ['side', 'ខាង'],
        'group' => ['group', 'ក្រុម'],
        'phone' => ['phone', 'ទូរស័ព្ទ', 'លេខទូរស័ព្ទ'],
    ];

    /** @return list<array{title: ?string, name: string, suffix: ?string, side: ?string, group: ?string, phone: ?string}> */
    public static function fromText(string $text): array
    {
        return collect(preg_split('/\R/u', $text))
            ->map(fn ($line) => self::row(['name' => $line]))
            ->filter()->take(self::MAX_ROWS)->values()->all();
    }

    /** @return list<array{title: ?string, name: string, suffix: ?string, side: ?string, group: ?string, phone: ?string}> */
    public static function fromFile(UploadedFile $file): array
    {
        $type = strtolower($file->getClientOriginalExtension()) === 'xlsx' ? 'xlsx' : 'csv';
        $reader = fn () => SimpleExcelReader::create($file->getRealPath(), $type);

        $columns = collect($reader()->getHeaders() ?? [])->mapWithKeys(function ($header) {
            $key = collect(self::HEADERS)->search(fn ($names) => in_array(mb_strtolower(trim((string) $header)), $names, true));

            return $key ? [$header => $key] : [];
        });

        // No recognisable header: a plain list, one name per row in the first column.
        $rows = $columns->contains('name')
            ? $reader()->getRows()->map(fn ($row) => collect($row)->only($columns->keys())->mapWithKeys(fn ($v, $h) => [$columns[$h] => $v])->all())
            : $reader()->noHeaderRow()->getRows()->map(fn ($row) => ['name' => $row[0] ?? '']);

        return $rows->map(fn ($row) => self::row($row))->filter()->take(self::MAX_ROWS)->values()->all();
    }

    /** One guest from loose input: a title or suffix written into the name is moved to its own field. */
    private static function row(array $in): ?array
    {
        $clean = fn ($v, $max = 255) => ($v = Str::limit(trim(preg_replace('/\s+/u', ' ', (string) $v)), $max, '')) === '' ? null : $v;
        $name = $clean($in['name'] ?? '');
        $title = $clean($in['title'] ?? '');
        $suffix = $clean($in['suffix'] ?? '');

        if ($name === null) {
            return null;
        }

        // Longest first, so លោកស្រី wins over លោក. A title must be followed by a space to count.
        foreach (collect(Guest::TITLES)->sortByDesc(fn ($t) => mb_strlen($t)) as $known) {
            if ($title === null && str_starts_with($name, $known.' ')) {
                [$title, $name] = [$known, trim(mb_substr($name, mb_strlen($known)))];
            }
        }
        foreach (Guest::SUFFIXES as $known) {
            if ($suffix === null && str_ends_with($name, $known) && trim(mb_substr($name, 0, -mb_strlen($known))) !== '') {
                [$suffix, $name] = [$known, trim(mb_substr($name, 0, -mb_strlen($known)))];
            }
        }

        $side = mb_strtolower((string) ($in['side'] ?? ''));

        return [
            'title' => $title,
            'name' => $name,
            'suffix' => $suffix,
            'side' => match (true) {
                in_array($side, ['groom', 'ប្រុស', 'ខាងប្រុស', 'កូនកំលោះ'], true) => 'groom',
                in_array($side, ['bride', 'ស្រី', 'ខាងស្រី', 'កូនក្រមុំ'], true) => 'bride',
                default => null,
            },
            'group' => $clean($in['group'] ?? ''),
            'phone' => $clean($in['phone'] ?? '', 30),
        ];
    }
}
