@use('App\Services\Themes\KhmerFormat')
@php($days = collect($vm->events())->groupBy(fn ($e) => $e['starts_at']->toDateString()))
<x-base::section-title section="program" />

<div x-data="timeline" class="grid gap-8">
    @foreach ($days as $date => $events)
        @if ($days->count() > 1)
            <div class="flex items-center justify-center gap-2 text-center">
                <span class="h-px w-8 bg-accent/40"></span>
                <p class="t-label text-accent-strong font-semibold">
                    <x-base::t :km="'ថ្ងៃទី'.KhmerFormat::digits($loop->iteration).' · '.KhmerFormat::date($events[0]['starts_at'], 'km')"
                               :en="'Day '.$loop->iteration.' · '.KhmerFormat::date($events[0]['starts_at'], 'en')" />
                </p>
                <span class="h-px w-8 bg-accent/40"></span>
            </div>
        @endif

        <ol class="timeline grid gap-5">
            @foreach ($events as $event)
                <li class="group relative" data-start="{{ $event['starts_at']->toIso8601String() }}">
                    {{-- Ceremonial Golden Lotus Node Marker --}}
                    <span class="timeline__node -left-[2.15rem] top-3 grid place-items-center" aria-hidden="true">
                        <svg viewBox="0 0 16 16" class="size-3.5" fill="none" stroke="currentColor" stroke-width="1.3">
                            <path d="M8 2c-2.5 2.5-2.5 7 0 11c2.5-4 2.5-8.5 0-11ZM8 13c-3.5-.5-6-3-6.5-6.5c3 .5 5 2 6 4M8 13c3.5-.5 6-3 6.5-6.5c-3 .5-5 2-6 4" />
                        </svg>
                    </span>

                    {{-- Ceremony Milestone Card --}}
                    <div class="inv-card !p-4 transition-all duration-300 group-[.is-next]:border-accent group-[.is-next]:shadow-md">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="inline-flex items-center rounded-md bg-gold-100/80 px-2.5 py-1 text-accent-strong border border-hairline/80 font-medium">
                                <x-base::t :km="KhmerFormat::time($event['starts_at'], 'km')" :en="KhmerFormat::time($event['starts_at'], 'en')" />
                            </span>
                            <span class="chip chip--lotus t-label-sm hidden group-[.is-next]:inline-flex animate-pulse">
                                <x-base::icon name="lotus" class="size-3.5" />
                                <x-base::t key="next" />
                            </span>
                        </div>

                        <p class="t-title mt-2 text-ink font-semibold">
                            <x-base::t :km="$event['title_km']" :en="$event['title_en']" />
                        </p>

                        @if ($event['venue_name'])
                            <p class="mt-2.5 flex items-center gap-1.5 text-ink-muted">
                                <x-base::icon name="map" class="size-3.5 text-accent-strong shrink-0" />
                                <span class="t-body-sm font-medium" lang="km">{{ $event['venue_name'] }}</span>
                            </p>
                        @endif

                        @if (! empty($event['note']))
                            <p class="t-body-sm mt-2 rounded-control bg-sunken/40 px-3 py-1.5 text-ink-muted italic border-l-2 border-accent">
                                {{ $event['note'] }}
                            </p>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    @endforeach
</div>