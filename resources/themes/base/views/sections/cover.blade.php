@use('App\Services\Themes\KhmerFormat')
@php($groom = $vm->person('groom'))
@php($bride = $vm->person('bride'))
<div class="pt-12 text-center">
    <x-base::seal :initials="$vm->initials()" class="mx-auto size-16" />

    <div class="relative mx-auto mt-8 max-w-80">
        <div class="arch">
            @if ($vm->data['cover'])
                <img src="{{ $vm->data['cover'] }}" alt="{{ $vm->coupleNames('en') }}" fetchpriority="high" width="640" height="800">
            @else
                <div class="grid h-full place-items-center">
                    <x-base::divider class="w-40 text-accent-strong" />
                </div>
            @endif
        </div>
        <x-base::kbach class="absolute -bottom-3 -left-3 size-12 -rotate-90" />
        <x-base::kbach class="absolute -right-3 -bottom-3 size-12 rotate-180" />
    </div>

    <h1 id="cover-title" class="mt-10">
        <span class="t-display block text-ink" lang="km">{{ $groom['name_km'] ?? '' }}</span>
        <span class="block font-display text-3xl italic text-accent-strong" aria-hidden="true">&amp;</span>
        <span class="t-display block text-ink" lang="km">{{ $bride['name_km'] ?? '' }}</span>
    </h1>
    @if (($groom['name_en'] ?? null) || ($bride['name_en'] ?? null))
        <p class="t-sub mt-3 text-ink-muted" lang="en">{{ $vm->coupleNames('en') }}</p>
    @endif

    <p class="t-label mt-6 text-accent-text">
        <x-base::t :km="KhmerFormat::date($vm->weddingDate(), 'km')" :en="KhmerFormat::date($vm->weddingDate(), 'en')" />
    </p>
</div>
