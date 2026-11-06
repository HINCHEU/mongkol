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

@if (array_filter($sides))
    <div class="grid gap-6 text-center min-[30rem]:grid-cols-2">
        @foreach ($sides as $label => $parents)
            @if ($parents)
                <div>
                    <p class="t-label-sm text-accent-text"><x-base::t :key="$label" /></p>
                    @foreach ($parents as $p)
                        <p lang="km" @class(['mt-1', 'text-ink-muted' => $p['late'], 'text-ink' => ! $p['late']])>
                            {{ $p['line'] }}@if ($p['late']) <x-base::t key="late" />@endif
                        </p>
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
@endif

<p class="t-body-lg mt-8 text-center text-ink">
    <x-base::t :km="$opening['km'] ?? __('invitation.opening_default', [], 'km')"
               :en="$opening['en'] ?? __('invitation.opening_default', [], 'en')" />
</p>

<div class="mt-8 grid gap-6 text-center">
    @foreach (['groom', 'bride'] as $role)
        @php($p = $vm->person($role))
        @if ($p)
            <div>
                <p class="t-label-sm text-accent-text"><x-base::t :key="$role" /></p>
                <p class="t-title mt-1" lang="km">{{ $p['name_km'] }}</p>
                @if ($p['name_en'])
                    <p class="t-body-sm text-ink-muted" lang="en">{{ $p['name_en'] }}</p>
                @endif
            </div>
        @endif
    @endforeach
</div>

<p class="mt-8 text-center text-ink-muted">
    <x-base::t :km="$closing['km'] ?? __('invitation.closing_default', [], 'km')"
               :en="$closing['en'] ?? __('invitation.closing_default', [], 'en')" />
</p>
