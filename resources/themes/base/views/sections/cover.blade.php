@use('App\Services\Themes\KhmerFormat')
@php($groom = $vm->person('groom'))
@php($bride = $vm->person('bride'))
<div class="pt-8 text-center">
    {{-- Traditional Auspicious Blessing Header --}}
    <div class="mb-4 flex flex-col items-center">
        <p class="t-label text-accent-strong" lang="km">សិរីសួស្តី ពិធីអាពាហ៍ពិពាហ៍</p>
        <p class="t-label-sm text-accent-text/80 tracking-widest uppercase" lang="en">The Wedding Celebration of</p>
        <x-base::seal :initials="$vm->initials()" class="mx-auto mt-4 size-16" />
    </div>

    {{-- Royal Arch Photo Frame with Authentic Kbach Corners --}}
    <div class="relative mx-auto mt-6 max-w-80">
        <x-base::kbach class="pointer-events-none absolute -top-3 -left-3 size-10 text-accent opacity-75" />
        <x-base::kbach class="pointer-events-none absolute -top-3 -right-3 size-10 text-accent opacity-75 rotate-90" />

        <div class="arch">
            @if ($vm->data['cover'])
                <img src="{{ $vm->data['cover'] }}" alt="{{ $vm->coupleNames('en') }}" fetchpriority="high" width="640" height="800">
            @else
                <div class="grid h-full place-items-center bg-gradient-to-b from-primary-soft to-surface p-6">
                    <div class="text-center">
                        <svg class="mx-auto size-14 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25">
                            <path d="M12 3c-2.5 3.5-3 8 0 13c3-5 2.5-9.5 0-13Z" />
                            <path d="M12 16c-4.5-.5-8-3.5-9-8c4 .5 7 2.5 9 6" />
                            <path d="M12 16c4.5-.5 8-3.5 9-8c-4 .5-7 2.5-9 6" />
                            <circle cx="12" cy="18" r="1.5" fill="currentColor" />
                        </svg>
                        <p class="t-label-sm mt-3 text-accent-strong" lang="km">សិរីមង្គល</p>
                    </div>
                </div>
            @endif
        </div>

        <x-base::kbach class="pointer-events-none absolute -bottom-3 -left-3 size-10 text-accent opacity-75 -rotate-90" />
        <x-base::kbach class="pointer-events-none absolute -bottom-3 -right-3 size-10 text-accent opacity-75 rotate-180" />
    </div>

    {{-- Couple Names in Regal Moul Typography --}}
    <h1 id="cover-title" class="mt-8">
        <span class="t-display block text-ink" lang="km">{{ $groom['name_km'] ?? '' }}</span>
        <div class="my-2 flex items-center justify-center gap-3">
            <span class="h-px w-10 bg-gradient-to-r from-transparent to-accent/60" aria-hidden="true"></span>
            <span class="font-display text-2xl italic text-accent-strong" aria-hidden="true">&amp;</span>
            <span class="h-px w-10 bg-gradient-to-l from-transparent to-accent/60" aria-hidden="true"></span>
        </div>
        <span class="t-display block text-ink" lang="km">{{ $bride['name_km'] ?? '' }}</span>
    </h1>

    @if (($groom['name_en'] ?? null) || ($bride['name_en'] ?? null))
        <p class="t-sub mt-2 text-ink-muted" lang="en">{{ $vm->coupleNames('en') }}</p>
    @endif

    {{-- Auspicious Wedding Solar & Lunar Date --}}
    <div class="mt-6 inline-flex flex-col items-center">
        <p class="t-label text-accent-text font-semibold">
            <x-base::t :km="KhmerFormat::date($vm->weddingDate(), 'km')" :en="KhmerFormat::date($vm->weddingDate(), 'en')" />
        </p>
        @if (!empty($vm->data['lunar_date']))
            <p class="t-body-sm mt-1 text-ink-muted/90" lang="km">{{ $vm->data['lunar_date'] }}</p>
        @endif
    </div>
</div>