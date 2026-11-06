@php($accounts = $vm->data['gift_accounts'])
<x-base::section-title section="gift" />

{{-- Chong dai gift card (§9.11): double-ruled gold frame, kbach corners; the KHQR is never tinted or cropped. --}}
<div class="inv-card inv-card--framed !p-6" x-data="{ tab: 0 }">
    <x-base::kbach class="absolute top-1 left-1 size-8" />
    <x-base::kbach class="absolute top-1 right-1 size-8 rotate-90" />

    @if (count($accounts) > 1)
        <div class="mb-6 flex justify-center gap-2" role="tablist">
            @foreach ($accounts as $i => $account)
                <button type="button" role="tab" class="chip t-label-sm min-h-11 px-4" :class="tab === {{ $i }} && 'chip--lotus'"
                        :aria-selected="tab === {{ $i }}" @click="tab = {{ $i }}">
                    <x-base::t :key="'owner_'.$account['owner']" />
                </button>
            @endforeach
        </div>
    @endif

    @foreach ($accounts as $i => $account)
        <div class="text-center" x-show="tab === {{ $i }}" @if ($i > 0) x-cloak @endif role="tabpanel">
            @if ($account['khqr'])
                <div class="mx-auto w-full max-w-64 rounded-control bg-white p-4">
                    <img src="{{ $account['khqr'] }}" alt="KHQR {{ $account['account_name'] }}" class="w-full" loading="lazy">
                </div>
                <a class="btn btn-secondary t-label mt-4" href="{{ $account['khqr'] }}" download>
                    <x-base::icon name="download" class="size-5 text-accent-strong" /><x-base::t key="save_qr" />
                </a>
                <p class="t-body-sm mt-2 text-ink-muted"><x-base::t key="save_qr_hint" /></p>
            @endif

            @if ($account['bank'])
                <p class="t-label mt-6 text-accent-text">{{ $account['bank'] }}</p>
            @endif
            @if ($account['account_name'])
                <p class="t-title mt-1 text-ink">{{ $account['account_name'] }}</p>
            @endif
            @if ($account['account_number'])
                <div class="mt-3 flex items-center justify-center gap-3"
                     x-data="copy(@js($account['account_number']), @js(__('invitation.copied', [], 'km').' · '.__('invitation.copied', [], 'en')))">
                    <span class="t-body-lg tabular-nums text-ink">{{ $account['account_number'] }}</span>
                    <button type="button" class="btn btn-secondary t-label-sm !min-h-11 !px-3" @click="copy()">
                        <x-base::icon name="copy" class="size-4 text-accent-strong" /><x-base::t key="copy" />
                    </button>
                </div>
            @endif
        </div>
    @endforeach
</div>
