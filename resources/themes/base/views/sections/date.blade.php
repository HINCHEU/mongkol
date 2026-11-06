@use('App\Services\Themes\KhmerFormat')
@php($thankYou = $vm->text('thank_you'))
<x-base::section-title section="date" />

<div class="text-center">
    {{-- Auspicious Solar and Lunar Date Plaque --}}
    <div class="inv-card relative mx-auto max-w-md !p-6 border-accent/40 bg-gradient-to-b from-surface to-gold-100/30">
        <div class="mx-auto mb-3 flex size-10 items-center justify-center rounded-full bg-primary/10 text-primary">
            <x-base::icon name="calendar" class="size-5" />
        </div>

        <p class="t-headline-md text-ink font-medium">
            <x-base::t :km="KhmerFormat::date($vm->weddingDate(), 'km')" :en="KhmerFormat::date($vm->weddingDate(), 'en')" />
        </p>

        @if (!empty($vm->data['lunar_date']))
            <div class="mt-3 inline-block rounded-full bg-gold-100 px-4 py-1 border border-accent/30">
                <p class="t-body-sm text-accent-strong font-medium" lang="km">{{ $vm->data['lunar_date'] }}</p>
            </div>
        @endif
    </div>

    {{-- Countdown to the Auspicious Hour --}}
    @if ($vm->data['theme_settings']['show_countdown'] ?? $theme['options']['show_countdown']['default'] ?? true)
        <div class="mt-8" x-data="countdown(@js($vm->startsAt()->toIso8601String()))">
            <div class="grid grid-cols-4 gap-2.5 max-w-md mx-auto" x-show="!started" aria-label="{{ __('invitation.countdown', [], 'en') }}">
                @foreach (['days', 'hours', 'minutes', 'seconds'] as $unit)
                    <div class="inv-card !px-1.5 !py-3.5 border-hairline/80 bg-surface shadow-sm">
                        <p class="t-numeral text-primary font-bold drop-shadow-sm" x-text="left && $store.lang.digits(left.{{ $unit }})">&nbsp;</p>
                        <p class="t-label-sm mt-1 text-accent-text font-semibold"><x-base::t :key="$unit" /></p>
                    </div>
                @endforeach
            </div>
            <p class="t-title text-primary font-semibold mt-4" x-show="started" x-cloak>
                <x-base::t :km="$thankYou['km'] ?? __('invitation.thank_you_line', [], 'km')"
                           :en="$thankYou['en'] ?? __('invitation.thank_you_line', [], 'en')" />
            </p>
        </div>
    @endif

    {{-- Add to Calendar Action --}}
    <div class="mt-8 max-w-xs mx-auto" x-data="{ open: false }">
        <button type="button" class="btn btn-secondary t-label w-full shadow-sm" @click="open = true">
            <x-base::icon name="calendar" class="size-5 text-accent-strong" />
            <x-base::t key="add_to_calendar" />
        </button>
        <template x-teleport="body">
            <div x-show="open" x-cloak @keydown.escape.window="open = false">
                <div class="sheet-backdrop" @click="open = false"></div>
                <div class="sheet grid gap-3" role="dialog" aria-modal="true" aria-label="{{ __('invitation.add_to_calendar', [], 'en') }}">
                    <a class="btn btn-primary t-label" href="{{ $vm->googleCalendarUrl() }}" target="_blank" rel="noopener"><x-base::t key="google_calendar" /></a>
                    @if ($calendarUrl)
                        <a class="btn btn-secondary t-label" href="{{ $calendarUrl }}"><x-base::t key="calendar_file" /></a>
                    @endif
                    <button type="button" class="btn t-label text-ink-muted" @click="open = false"><x-base::t key="close" /></button>
                </div>
            </div>
        </template>
    </div>
</div>