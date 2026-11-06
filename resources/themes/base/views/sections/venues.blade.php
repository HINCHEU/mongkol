<x-base::section-title section="venues" />

<div class="grid gap-6">
    @foreach ($vm->data['venues'] as $index => $venue)
        <article class="inv-card inv-card--framed relative !p-6">
            <div class="flex items-start gap-3">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                    <x-base::icon name="map" class="size-5" />
                </div>
                <div>
                    <h3 class="t-title text-ink font-semibold" lang="km">{{ $venue['name'] }}</h3>
                    @if ($venue['address'])
                        <p class="mt-1 text-ink-muted text-sm leading-relaxed" lang="km">{{ $venue['address'] }}</p>
                    @endif
                </div>
            </div>

            @if ($events = $vm->eventsAtVenue($index))
                <div class="mt-4 border-t border-hairline/60 pt-3">
                    <p class="t-label-sm text-accent-strong mb-2 font-semibold">
                        <span lang="km" data-l="km">កម្មវិធីប្រារព្ធនៅទីនេះ</span>
                        <span lang="en" data-l="en">Ceremonies at this venue</span>
                    </p>
                    <ul class="flex flex-wrap gap-2">
                        @foreach ($events as $event)
                            <li class="chip t-label-sm text-ink"><x-base::t :km="$event['title_km']" :en="$event['title_en']" /></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($venue['maps_url'])
                <a class="btn btn-primary t-label mt-6 w-full shadow-sm" href="{{ $venue['maps_url'] }}" target="_blank" rel="noopener">
                    <x-base::icon name="map" class="size-5" />
                    <x-base::t key="open_map" />
                </a>
            @endif
        </article>
    @endforeach
</div>