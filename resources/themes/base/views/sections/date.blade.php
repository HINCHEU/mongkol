@use('App\Services\Themes\KhmerFormat')
@php($thankYou = $vm->text('thank_you'))
<x-base::section-title section="date" />

<div class="text-center">
    <p class="t-headline-md text-ink">
        <x-base::t :km="KhmerFormat::date($vm->weddingDate(), 'km')" :en="KhmerFormat::date($vm->weddingDate(), 'en')" />
    </p>
    <p class="t-body-sm mt-2 text-ink-muted" lang="km">{{ $vm->data['lunar_date'] }}</p>

    @if ($vm->data['theme_settings']['show_countdown'] ?? $theme['options']['show_countdown']['default'] ?? true)
        <div class="mt-8" x-data="countdown(@js($vm->startsAt()->toIso8601String()))">
            <div class="grid grid-cols-4 gap-2" x-show="!started" aria-label="{{ __('invitation.countdown', [], 'en') }}">
                @foreach (['days', 'hours', 'minutes', 'seconds'] as $unit)
                    <div class="inv-card !px-1 !py-3">
                        <p class="t-numeral text-primary" x-text="left && $store.lang.digits(left.{{ $unit }})">&nbsp;</p>
                        <p class="t-label-sm text-accent-text"><x-base::t :key="$unit" /></p>
                    </div>
                @endforeach
            </div>
            <p class="t-title text-primary" x-show="started" x-cloak>
                <x-base::t :km="$thankYou['km'] ?? __('invitation.thank_you_line', [], 'km')"
                           :en="$thankYou['en'] ?? __('invitation.thank_you_line', [], 'en')" />
            </p>
        </div>
    @endif

    <div class="mt-8" x-data="{ open: false }">
        <button type="button" class="btn btn-secondary t-label w-full" @click="open = true">
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
