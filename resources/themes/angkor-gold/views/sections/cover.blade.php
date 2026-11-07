@use('App\Services\Themes\KhmerFormat')
@php($groom = $vm->person('groom'))
@php($bride = $vm->person('bride'))
<div class="pt-8 text-center">
    {{-- Imperial Royal Court Blessing Header --}}
    <div class="mb-4 flex flex-col items-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-accent bg-gold-100/70 shadow-sm">
            <span class="size-1.5 rounded-full bg-accent-strong"></span>
            <p class="t-label text-accent-strong font-semibold" lang="km">សិរីសួស្តី ជ័យមង្គល វិបុលសុខ</p>
            <p class="t-label-sm text-accent-strong tracking-widest uppercase font-semibold" lang="en">Royal Wedding Proclamation</p>
            <span class="size-1.5 rounded-full bg-accent-strong"></span>
        </div>
        <x-base::seal :initials="$vm->initials()" class="mx-auto mt-4 size-16 drop-shadow-md" />
    </div>

    {{-- Majestic Angkor Temple Doorway Arch Photo Frame --}}
    <div class="relative mx-auto mt-6 max-w-xs">
        {{-- Ornate Angkor Pediment Top Accent --}}
        <div class="pointer-events-none absolute -top-5 left-1/2 -translate-x-1/2 z-10 flex items-center justify-center">
            <svg width="48" height="24" viewBox="0 0 48 24" fill="none" class="text-accent drop-shadow">
                <path d="M24 0L32 12H16L24 0Z" fill="currentColor"/>
                <path d="M24 6L38 20H10L24 6Z" stroke="currentColor" stroke-width="1.5" fill="none"/>
                <circle cx="24" cy="14" r="2.5" fill="#FFEAA7"/>
            </svg>
        </div>

        <x-base::kbach class="pointer-events-none absolute -top-3 -left-3 size-10 text-accent opacity-90 drop-shadow-sm" />
        <x-base::kbach class="pointer-events-none absolute -top-3 -right-3 size-10 text-accent opacity-90 rotate-90 drop-shadow-sm" />

        <div class="angkor-arch-frame">
            <div class="angkor-arch-frame__inner">
                @if ($vm->data['cover'])
                    <img src="{{ $vm->data['cover'] }}" alt="{{ $vm->coupleNames('en') }}" fetchpriority="high" width="600" height="760">
                @else
                    <div class="grid h-full place-items-center bg-gradient-to-b from-primary-soft to-surface p-6">
                        <div class="text-center">
                            <svg class="mx-auto size-16 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                                <path d="M12 3c-2.5 3.5-3 8 0 13c3-5 2.5-9.5 0-13Z" fill="currentColor" fill-opacity="0.2" />
                                <path d="M12 16c-4.5-.5-8-3.5-9-8c4 .5 7 2.5 9 6" />
                                <path d="M12 16c4.5-.5 8-3.5 9-8c-4 .5-7 2.5-9 6" />
                                <circle cx="12" cy="18" r="1.5" fill="currentColor" />
                            </svg>
                            <p class="t-label mt-3 font-semibold text-accent-strong" lang="km">មង្គលសិរីមហានគរ</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <x-base::kbach class="pointer-events-none absolute -bottom-3 -left-3 size-10 text-accent opacity-90 -rotate-90 drop-shadow-sm" />
        <x-base::kbach class="pointer-events-none absolute -bottom-3 -right-3 size-10 text-accent opacity-90 rotate-180 drop-shadow-sm" />
    </div>

    {{-- Regal Couple Names in Moul Typography --}}
    <h1 id="cover-title" class="mt-8 px-4">
        <span class="t-display block text-ink drop-shadow-sm" lang="km">{{ $groom['name_km'] ?? '' }}</span>
        <div class="my-2.5 flex items-center justify-center gap-3">
            <span class="h-0.5 w-12 bg-gradient-to-r from-transparent via-accent to-accent-strong" aria-hidden="true"></span>
            <span class="font-display text-2xl font-bold italic text-accent-strong" aria-hidden="true">&amp;</span>
            <span class="h-0.5 w-12 bg-gradient-to-l from-transparent via-accent to-accent-strong" aria-hidden="true"></span>
        </div>
        <span class="t-display block text-ink drop-shadow-sm" lang="km">{{ $bride['name_km'] ?? '' }}</span>
    </h1>

    @if (($groom['name_en'] ?? null) || ($bride['name_en'] ?? null))
        <p class="t-sub mt-2 text-ink-muted font-medium" lang="en">{{ $vm->coupleNames('en') }}</p>
    @endif

    {{-- Auspicious Wedding Solar & Lunar Date Plaque --}}
    <div class="mt-6 inline-flex flex-col items-center">
        <div class="angkor-cartouche px-5 py-2">
            <p class="t-label text-accent-text font-bold">
                <x-base::t :km="KhmerFormat::date($vm->weddingDate(), 'km')" :en="KhmerFormat::date($vm->weddingDate(), 'en')" />
            </p>
        </div>
        @if (!empty($vm->data['lunar_date']))
            <p class="t-body-sm mt-2 font-medium text-accent-strong" lang="km">{{ $vm->data['lunar_date'] }}</p>
        @endif
    </div>
</div>

