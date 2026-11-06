<x-base::section-title section="venues" />

<div class="grid gap-6">
    @foreach ($vm->data['venues'] as $index => $venue)
        <article class="inv-card">
            <h3 class="t-title text-ink" lang="km">{{ $venue['name'] }}</h3>
            @if ($venue['address'])
                <p class="mt-1 text-ink-muted" lang="km">{{ $venue['address'] }}</p>
            @endif
            @if ($events = $vm->eventsAtVenue($index))
                <ul class="mt-4 flex flex-wrap gap-2">
                    @foreach ($events as $event)
                        <li class="chip t-label-sm text-ink"><x-base::t :km="$event['title_km']" :en="$event['title_en']" /></li>
                    @endforeach
                </ul>
            @endif
            @if ($venue['maps_url'])
                <a class="btn btn-primary t-label mt-6 w-full" href="{{ $venue['maps_url'] }}" target="_blank" rel="noopener">
                    <x-base::icon name="map" class="size-5" />
                    <x-base::t key="open_map" />
                </a>
            @endif
        </article>
    @endforeach
</div>
