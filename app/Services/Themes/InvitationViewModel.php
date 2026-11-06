<?php

namespace App\Services\Themes;

use App\Enums\InvitationState;
use App\Enums\MediaKind;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Media;
use App\Services\LunarDate;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Everything a theme may show, as plain data. Themes read only this and never query the database.
 *
 * `data` has one shape whether it comes from an invitation or a theme's demo.json, so the
 * cached copy is that array. The guest is added per request and never cached.
 */
final class InvitationViewModel
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array{title: ?string, name: string, suffix: ?string}|null  $guest
     */
    public function __construct(
        public readonly array $data,
        public readonly ?array $guest = null,
        public readonly bool $isPreview = false,
    ) {}

    public static function fromInvitation(Invitation $invitation): self
    {
        $data = Cache::rememberForever(self::cacheKey($invitation->id), fn () => self::dataFor($invitation));

        // State depends on the clock, so it is worked out per request, outside the cache.
        return new self($data, isPreview: $invitation->state() === InvitationState::Trial);
    }

    /** @param array<string, mixed> $data the demo.json shape */
    public static function fromArray(array $data): self
    {
        $data['lunar_date'] ??= LunarDate::for(CarbonImmutable::parse($data['wedding_date'], KhmerFormat::TIMEZONE));

        return new self($data, $data['guest'] ?? null);
    }

    public static function cacheKey(int $invitationId): string
    {
        return "invitation-view:{$invitationId}";
    }

    public function withGuest(?Guest $guest): self
    {
        return new self(
            $this->data,
            $guest ? ['title' => $guest->title, 'name' => $guest->name, 'suffix' => $guest->suffix] : null,
            $this->isPreview,
        );
    }

    public function has(string $section): bool
    {
        return match ($section) {
            'cover', 'invitation', 'date' => true,
            'program' => $this->data['events'] !== [],
            'venues' => $this->data['venues'] !== [],
            'gallery' => $this->data['gallery'] !== [] || ! empty($this->data['video_url']),
            'gift' => $this->data['gift_accounts'] !== [],
            default => false,
        };
    }

    /** @return array{title: ?string, name_km: string, name_en: ?string, is_late: bool}|null */
    public function person(string $role): ?array
    {
        return $this->data['people'][$role] ?? null;
    }

    /** @return array{km: ?string, en: ?string} */
    public function text(string $key): array
    {
        return ['km' => $this->data['content'][$key]['km'] ?? null, 'en' => $this->data['content'][$key]['en'] ?? null];
    }

    public function initials(): string
    {
        // Khmer names put the family name first, so the monogram uses the given (last) name: Chan Sokha → S.
        $initial = fn (string $role) => mb_substr(Str::afterLast(trim($this->person($role)['name_en'] ?? ''), ' '), 0, 1);

        return trim($initial('groom').' & '.$initial('bride'), ' &') ?: 'ម';
    }

    public function weddingDate(): CarbonImmutable
    {
        return CarbonImmutable::parse($this->data['wedding_date'], KhmerFormat::TIMEZONE);
    }

    /** @return list<array<string, mixed>> events with Carbon times in Phnom Penh, sorted */
    public function events(): array
    {
        return collect($this->data['events'])
            ->map(fn (array $e) => [
                ...$e,
                'starts_at' => CarbonImmutable::parse($e['starts_at'])->setTimezone(KhmerFormat::TIMEZONE),
                'ends_at' => isset($e['ends_at']) ? CarbonImmutable::parse($e['ends_at'])->setTimezone(KhmerFormat::TIMEZONE) : null,
                'venue_name' => isset($e['venue']) ? ($this->data['venues'][$e['venue']]['name'] ?? null) : null,
            ])
            ->sortBy('starts_at')
            ->values()
            ->all();
    }

    /** The countdown target: the first event, or the start of the wedding day. */
    public function startsAt(): CarbonImmutable
    {
        return $this->events()[0]['starts_at'] ?? $this->weddingDate()->startOfDay();
    }

    /** @return list<array<string, mixed>> */
    public function eventsAtVenue(int $index): array
    {
        return array_values(array_filter($this->events(), fn ($e) => ($e['venue'] ?? null) === $index));
    }

    public function coupleNames(string $lang): string
    {
        $key = $lang === 'km' ? 'name_km' : 'name_en';
        $name = fn (string $role) => $this->person($role)[$key] ?? $this->person($role)['name_km'] ?? '';

        return $name('groom').' & '.$name('bride');
    }

    public function googleCalendarUrl(): string
    {
        $start = $this->startsAt()->utc();
        $end = $start->addHours(3);

        return 'https://calendar.google.com/calendar/render?'.http_build_query([
            'action' => 'TEMPLATE',
            'text' => $this->coupleNames('km'),
            'dates' => $start->format('Ymd\THis\Z').'/'.$end->format('Ymd\THis\Z'),
            'location' => $this->data['venues'][0]['name'] ?? '',
        ]);
    }

    public function ics(): string
    {
        $esc = fn (?string $s) => addcslashes((string) $s, ",;\\\n");
        $lines = ['BEGIN:VCALENDAR', 'VERSION:2.0', 'PRODID:-//Mongkol//Invitation//KM', 'CALSCALE:GREGORIAN'];

        foreach ($this->events() as $i => $event) {
            $start = $event['starts_at']->utc();
            $end = ($event['ends_at'] ?? $event['starts_at']->addHours(2))->utc();
            array_push(
                $lines,
                'BEGIN:VEVENT',
                "UID:{$this->data['slug']}-{$i}@mongkol",
                'DTSTAMP:'.$start->format('Ymd\THis\Z'),
                'DTSTART:'.$start->format('Ymd\THis\Z'),
                'DTEND:'.$end->format('Ymd\THis\Z'),
                'SUMMARY:'.$esc($event['title_km'].' · '.$this->coupleNames('km')),
                'LOCATION:'.$esc($event['venue_name']),
                'END:VEVENT',
            );
        }

        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines)."\r\n";
    }

    /** @return array<string, mixed> */
    private static function dataFor(Invitation $invitation): array
    {
        $invitation->loadMissing(['people', 'events', 'venues', 'media', 'giftAccounts.khqr', 'plan']);

        $venueIndex = $invitation->venues->pluck('id')->flip();
        // Images still processing have no URL yet and are left out.
        $url = fn (?Media $media, ?int $width = null) => $media?->url($width);
        $media = $invitation->media->groupBy(fn ($m) => $m->kind->value);

        return [
            'slug' => $invitation->slug,
            'locale' => $invitation->content['locale'] ?? 'km',
            'wedding_date' => $invitation->wedding_date->toDateString(),
            'lunar_date' => $invitation->content['lunar_date'] ?? LunarDate::for($invitation->wedding_date),
            'theme_settings' => $invitation->theme_settings ?? [],
            'people' => $invitation->people->keyBy('role')
                ->map(fn ($p) => $p->only('title', 'name_km', 'name_en', 'is_late'))->all(),
            'content' => $invitation->content ?? [],
            'events' => $invitation->events->map(fn ($e) => [
                'title_km' => $e->title_km,
                'title_en' => $e->title_en,
                'starts_at' => $e->starts_at->toIso8601String(),
                'ends_at' => $e->ends_at?->toIso8601String(),
                'venue' => $e->venue_id !== null ? $venueIndex[$e->venue_id] ?? null : null,
                'note' => $e->note,
            ])->all(),
            'venues' => $invitation->venues->map(fn ($v) => $v->only('name', 'address', 'maps_url'))->values()->all(),
            'cover' => $url($media->get(MediaKind::Cover->value)?->first()),
            // shortcut: one 960px size per photo; the 480/1600 variants exist for a srcset when a theme wants one.
            'gallery' => $media->get(MediaKind::Gallery->value, collect())->map(fn ($m) => $url($m, 960))->filter()->values()->all(),
            'music' => $url($media->get(MediaKind::Music->value)?->first()),
            'video_url' => $invitation->content['video_url'] ?? null,
            'gift_accounts' => $invitation->giftAccounts->map(fn ($g) => [
                ...$g->only('owner', 'bank', 'account_name', 'account_number'),
                'khqr' => $url($g->khqr),
            ])->all(),
            'show_credit' => ! ($invitation->plan?->features['hide_credit'] ?? false),
        ];
    }
}
