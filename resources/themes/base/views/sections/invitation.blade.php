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

{{-- Formal Royal Wedding Letter Card --}}
<div class="inv-card inv-card--framed relative !p-6 sm:!p-8">
    <x-base::kbach class="pointer-events-none absolute top-2 left-2 size-8 text-accent opacity-60" />
    <x-base::kbach class="pointer-events-none absolute top-2 right-2 size-8 text-accent opacity-60 rotate-90" />
    <x-base::kbach class="pointer-events-none absolute bottom-2 left-2 size-8 text-accent opacity-60 -rotate-90" />
    <x-base::kbach class="pointer-events-none absolute bottom-2 right-2 size-8 text-accent opacity-60 rotate-180" />

    {{-- Parental Hosts (មាតាបិតាទាំងសងខាង) --}}
    @if (array_filter($sides))
        <div class="grid gap-6 text-center sm:grid-cols-2 sm:gap-4">
            @foreach ($sides as $label => $parents)
                @if ($parents)
                    <div class="rounded-control bg-gold-100/40 p-4 border border-hairline/60">
                        <p class="t-label-sm text-accent-strong font-semibold"><x-base::t :key="$label" /></p>
                        <div class="mt-2 space-y-1">
                            @foreach ($parents as $p)
                                <p lang="km" @class(['text-base leading-relaxed', 'text-ink-muted/80 italic' => $p['late'], 'text-ink font-medium' => ! $p['late']])>
                                    {{ $p['line'] }}@if ($p['late']) <span class="t-body-sm text-ink-muted/80"><x-base::t key="late" /></span>@endif
                                </p>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    {{-- Ceremonial Invitation Prose --}}
    <div class="my-6 text-center">
        <p class="t-body-lg text-ink font-normal leading-relaxed">
            <x-base::t :km="$opening['km'] ?? __('invitation.opening_default', [], 'km')"
                       :en="$opening['en'] ?? __('invitation.opening_default', [], 'en')" />
        </p>
    </div>

    {{-- The Nuptial Couple Presentation --}}
    <div class="my-6 grid gap-6 text-center sm:grid-cols-2">
        @foreach (['groom', 'bride'] as $role)
            @php($p = $vm->person($role))
            @if ($p)
                <div class="rounded-control bg-surface p-4 border border-hairline">
                    <p class="t-label-sm text-accent-strong"><x-base::t :key="$role" /></p>
                    <p class="t-title mt-1.5 text-primary" lang="km">{{ $p['name_km'] }}</p>
                    @if ($p['name_en'])
                        <p class="t-body-sm text-ink-muted mt-0.5" lang="en">{{ $p['name_en'] }}</p>
                    @endif
                </div>
            @endif
        @endforeach
    </div>

    {{-- Closing Gratitude & Blessing --}}
    <p class="mt-6 text-center text-ink-muted leading-relaxed">
        <x-base::t :km="$closing['km'] ?? __('invitation.closing_default', [], 'km')"
                   :en="$closing['en'] ?? __('invitation.closing_default', [], 'en')" />
    </p>
</div>