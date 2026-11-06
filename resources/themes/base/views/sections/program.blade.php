@use('App\Services\Themes\KhmerFormat')
@php($days = collect($vm->events())->groupBy(fn ($e) => $e['starts_at']->toDateString()))
<x-base::section-title section="program" />

<div x-data="timeline" class="grid gap-8">
    @foreach ($days as $date => $events)
        @if ($days->count() > 1)
            <p class="t-label text-accent-text">
                <x-base::t :km="'ថ្ងៃទី'.KhmerFormat::digits($loop->iteration).' · '.KhmerFormat::date($events[0]['starts_at'], 'km')"
                           :en="'Day '.$loop->iteration.' · '.KhmerFormat::date($events[0]['starts_at'], 'en')" />
            </p>
        @endif
        <ol class="timeline grid gap-6">
            @foreach ($events as $event)
                <li class="group relative" data-start="{{ $event['starts_at']->toIso8601String() }}">
                    <span class="timeline__node -left-[2rem] top-1.5 grid place-items-center" aria-hidden="true">
                        <svg viewBox="0 0 16 16" class="size-4" fill="none" stroke="currentColor" stroke-width="1.25">
                            <path d="M8 2c-2.5 2.5-2.5 7 0 11c2.5-4 2.5-8.5 0-11ZM8 13c-3.5-.5-6-3-6.5-6.5c3 .5 5 2 6 4M8 13c3.5-.5 6-3 6.5-6.5c-3 .5-5 2-6 4" />
                        </svg>
                    </span>
                    <p class="t-label text-accent-text">
                        <x-base::t :km="KhmerFormat::time($event['starts_at'], 'km')" :en="KhmerFormat::time($event['starts_at'], 'en')" />
                        <span class="chip chip--lotus t-label-sm ml-2 hidden group-[.is-next]:inline-flex"><x-base::t key="next" /></span>
                    </p>
                    <p class="t-title mt-1 text-ink"><x-base::t :km="$event['title_km']" :en="$event['title_en']" /></p>
                    @if ($event['venue_name'])
                        <p class="mt-2"><span class="chip t-label-sm text-ink" lang="km">{{ $event['venue_name'] }}</span></p>
                    @endif
                    @if (! empty($event['note']))
                        <p class="t-body-sm mt-2 text-ink-muted">{{ $event['note'] }}</p>
                    @endif
                </li>
            @endforeach
        </ol>
    @endforeach
</div>
