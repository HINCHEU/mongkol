@use('App\Services\Themes\KhmerFormat')
@php($groom = $vm->person('groom'))
@php($bride = $vm->person('bride'))
<div class="pt-8 text-center">
    {{-- Delicate Botanical Greeting --}}
    <div class="mb-5 flex flex-col items-center">
        <div class="romdoul-badge">
            <svg class="size-4 text-accent" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C10 5 9 8 12 11C15 8 14 5 12 2Z" opacity="0.9"/>
                <path d="M5 14C8 13 11 14 12 11C9 10 6 11 5 14Z" opacity="0.75"/>
                <path d="M19 14C16 13 13 14 12 11C15 10 18 11 19 14Z" opacity="0.75"/>
                <circle cx="12" cy="11" r="1.5" fill="#C8A86B"/>
            </svg>
            <span class="t-label-sm font-semibold text-accent-strong" lang="km">សិរីសួស្តី អាពាហ៍ពិពាហ៍</span>
            <span class="t-label-sm font-semibold text-accent-strong tracking-widest uppercase" lang="en">Wedding Celebration</span>
        </div>
        <x-base::seal :initials="$vm->initials()" class="mx-auto mt-4 size-14 shadow-sm" />
    </div>

    {{-- Modern Circular Botanical Photo Frame --}}
    <div class="relative mx-auto my-6">
        <div class="romdoul-frame">
            <div class="romdoul-frame__inner">
                @if ($vm->data['cover'])
                    <img src="{{ $vm->data['cover'] }}" alt="{{ $vm->coupleNames('en') }}" fetchpriority="high" width="520" height="520">
                @else
                    <div class="grid h-full place-items-center bg-gradient-to-b from-primary-soft to-surface p-6">
                        <div class="text-center">
                            <svg class="mx-auto size-12 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25">
                                <path d="M12 3c-2.5 3.5-3 8 0 13c3-5 2.5-9.5 0-13Z" />
                                <path d="M12 16c-4.5-.5-8-3.5-9-8c4 .5 7 2.5 9 6" />
                                <path d="M12 16c4.5-.5 8-3.5 9-8c-4 .5-7 2.5-9 6" />
                                <circle cx="12" cy="18" r="1.5" fill="currentColor" />
                            </svg>
                            <p class="t-label-sm mt-2 text-accent-strong" lang="km">ផ្ការំដួលមង្គល</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Floating Petal Accent Pins --}}
        <div class="pointer-events-none absolute -bottom-2 left-1/2 -translate-x-1/2 flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/95 border border-accent/30 shadow-sm">
            <span class="size-1.5 rounded-full bg-primary"></span>
            <span class="size-2 rounded-full bg-accent"></span>
            <span class="size-1.5 rounded-full bg-primary"></span>
        </div>
    </div>

    {{-- Modern Couple Names --}}
    <h1 id="cover-title" class="mt-8 px-4">
        <span class="t-display block text-ink" lang="km">{{ $groom['name_km'] ?? '' }}</span>
        <div class="my-2.5 flex items-center justify-center gap-3">
            <span class="h-px w-12 bg-gradient-to-r from-transparent to-accent/50" aria-hidden="true"></span>
            <span class="font-display text-2xl italic text-primary" aria-hidden="true">&amp;</span>
            <span class="h-px w-12 bg-gradient-to-l from-transparent to-accent/50" aria-hidden="true"></span>
        </div>
        <span class="t-display block text-ink" lang="km">{{ $bride['name_km'] ?? '' }}</span>
    </h1>

    @if (($groom['name_en'] ?? null) || ($bride['name_en'] ?? null))
        <p class="t-sub mt-2 text-ink-muted" lang="en">{{ $vm->coupleNames('en') }}</p>
    @endif

    {{-- Auspicious Wedding Solar & Lunar Date --}}
    <div class="mt-6 inline-flex flex-col items-center">
        <div class="px-4 py-1.5 rounded-full bg-sunken/60 border border-hairline">
            <p class="t-label text-accent-text font-semibold">
                <x-base::t :km="KhmerFormat::date($vm->weddingDate(), 'km')" :en="KhmerFormat::date($vm->weddingDate(), 'en')" />
            </p>
        </div>
        @if (!empty($vm->data['lunar_date']))
            <p class="t-body-sm mt-1.5 text-ink-muted/80" lang="km">{{ $vm->data['lunar_date'] }}</p>
        @endif
    </div>
</div>

