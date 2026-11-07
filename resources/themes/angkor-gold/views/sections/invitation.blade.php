@php
    $parent = function (string $role) use ($vm) {
        $p = $vm->person($role);

        return $p ? ['line' => trim(($p['title'] ? $p['title'].' ' : '').$p['name_km']), 'late' => $p['is_late']] : null;
    };
    $sides = [
        'groom_parents' => array_filter([$parent('groom_father'), $parent('groom_mother')]),
        'bride_parents' => array_filter([$parent('bride_father'), $parent('bride_mother')]),
    ];
    $opening = $vm->text('opening');
    $closing = $vm->text('closing');
@endphp

<x-base::section-title section="invitation" />

{{-- Royal Angkor Decree Proclamation Folio --}}
<div class="angkor-proclamation relative !p-6 sm:!p-9">
    <x-base::kbach class="pointer-events-none absolute top-3 left-3 size-9 text-accent opacity-80" />
    <x-base::kbach class="pointer-events-none absolute top-3 right-3 size-9 text-accent opacity-80 rotate-90" />
    <x-base::kbach class="pointer-events-none absolute bottom-3 left-3 size-9 text-accent opacity-80 -rotate-90" />
    <x-base::kbach class="pointer-events-none absolute bottom-3 right-3 size-9 text-accent opacity-80 rotate-180" />

    {{-- Parental Hosts (មាតាបិតាទាំងសងខាង) --}}
    @if (array_filter($sides))
        <div class="grid gap-6 text-center sm:grid-cols-2 sm:gap-4 relative z-10">
            @foreach ($sides as $label => $parents)
                @if ($parents)
                    <div class="angkor-cartouche p-4 border-accent/60">
                        <p class="t-label-sm text-accent-strong font-bold"><x-base::t :key="$label" /></p>
                        <div class="mt-2 space-y-1">
                            @foreach ($parents as $p)
                                <p lang="km" @class(['text-base leading-relaxed', 'text-ink-muted/80 italic' => $p['late'], 'text-ink font-semibold' => ! $p['late']])>
                                    {{ $p['line'] }}@if ($p['late']) <span class="t-body-sm text-ink-muted/80"><x-base::t key="late" /></span>@endif
                                </p>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    {{-- Ceremonial Royal Proclamation Prose --}}
    <div class="my-7 text-center relative z-10">
        <p class="t-body-lg text-ink font-normal leading-relaxed">
            <x-base::t :km="$opening['km'] ?? __('invitation.opening_default', [], 'km')"
                       :en="$opening['en'] ?? __('invitation.opening_default', [], 'en')" />
        </p>
    </div>

    {{-- The Nuptial Couple Presentation --}}
    <div class="my-6 grid gap-6 text-center sm:grid-cols-2 relative z-10">
        @foreach (['groom', 'bride'] as $role)
            @php($p = $vm->person($role))
            @if ($p)
                <div class="angkor-cartouche p-4 border-accent">
                    <p class="t-label-sm text-accent-strong font-bold uppercase tracking-wider"><x-base::t :key="$role" /></p>
                    <p class="t-title mt-1.5 text-primary font-bold" lang="km">{{ $p['name_km'] }}</p>
                    @if ($p['name_en'])
                        <p class="t-body-sm text-ink-muted font-medium mt-0.5" lang="en">{{ $p['name_en'] }}</p>
                    @endif
                </div>
            @endif
        @endforeach
    </div>

    {{-- Closing Gratitude & Blessing --}}
    <p class="mt-7 text-center text-ink-muted leading-relaxed relative z-10">
        <x-base::t :km="$closing['km'] ?? __('invitation.closing_default', [], 'km')"
                   :en="$closing['en'] ?? __('invitation.closing_default', [], 'en')" />
    </p>
</div>

