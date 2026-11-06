@php($accounts = $vm->data['gift_accounts'])
<x-base::section-title section="gift" />

{{-- Royal Chong Dai Gift Card (ស្រោមសំបុត្រចំណងដៃមង្គលការ) --}}
<div class="inv-card inv-card--framed relative !p-6 sm:!p-8" x-data="{ tab: 0 }">
    <x-base::kbach class="pointer-events-none absolute top-2 left-2 size-8 text-accent opacity-60" />
    <x-base::kbach class="pointer-events-none absolute top-2 right-2 size-8 text-accent opacity-60 rotate-90" />
    <x-base::kbach class="pointer-events-none absolute bottom-2 left-2 size-8 text-accent opacity-60 -rotate-90" />
    <x-base::kbach class="pointer-events-none absolute bottom-2 right-2 size-8 text-accent opacity-60 rotate-180" />

    {{-- Auspicious Chong Dai Header Blessing --}}
    <div class="mb-6 text-center">
        <div class="mx-auto mb-2 flex size-11 items-center justify-center rounded-full bg-primary/10 text-primary">
            <x-base::icon name="gift" class="size-6" />
        </div>
        <p class="t-body-sm text-ink-muted max-w-sm mx-auto leading-relaxed">
            <span lang="km">ចំណងដៃ និងសព្វសាធុការពរជ័យរបស់លោកអ្នក គឺជាសិរីមង្គលដ៏ខ្ពង់ខ្ពស់សម្រាប់គូស្វាមីភរិយាថ្មី</span>
            <span lang="en">Your presence, prayers, and wedding gifts are our greatest blessing</span>
        </p>
    </div>

    {{-- Account Owner Switcher Tabs --}}
    @if (count($accounts) > 1)
        <div class="mb-6 flex justify-center gap-2" role="tablist">
            @foreach ($accounts as $i => $account)
                <button type="button" role="tab"
                        class="chip t-label-sm min-h-10 px-5 font-semibold transition-all duration-200 cursor-pointer"
                        :class="tab === {{ $i }} ? 'chip--gold shadow-sm scale-105' : 'bg-surface hover:bg-gold-100'"
                        :aria-selected="tab === {{ $i }}" @click="tab = {{ $i }}">
                    <x-base::t :key="'owner_'.$account['owner']" />
                </button>
            @endforeach
        </div>
    @endif

    {{-- Account Detail Panels --}}
    @foreach ($accounts as $i => $account)
        <div class="text-center" x-show="tab === {{ $i }}" @if ($i > 0) x-cloak @endif role="tabpanel">
            {{-- KHQR Display --}}
            @if ($account['khqr'])
                <div class="relative mx-auto w-full max-w-64 rounded-xl bg-white p-4 shadow-md border-2 border-accent/40">
                    {{-- QR Corner Accents --}}
                    <span class="absolute -top-1 -left-1 size-3 border-t-2 border-l-2 border-accent-strong" aria-hidden="true"></span>
                    <span class="absolute -top-1 -right-1 size-3 border-t-2 border-r-2 border-accent-strong" aria-hidden="true"></span>
                    <span class="absolute -bottom-1 -left-1 size-3 border-b-2 border-l-2 border-accent-strong" aria-hidden="true"></span>
                    <span class="absolute -bottom-1 -right-1 size-3 border-b-2 border-r-2 border-accent-strong" aria-hidden="true"></span>

                    <img src="{{ $account['khqr'] }}" alt="KHQR {{ $account['account_name'] }}" class="w-full" loading="lazy">
                </div>

                <div class="mt-4">
                    <a class="btn btn-secondary t-label shadow-sm" href="{{ $account['khqr'] }}" download>
                        <x-base::icon name="download" class="size-4 text-accent-strong" />
                        <x-base::t key="save_qr" />
                    </a>
                    <p class="t-body-sm mt-2 text-ink-muted/80 text-xs"><x-base::t key="save_qr_hint" /></p>
                </div>
            @endif

            {{-- Bank & Account Information --}}
            <div class="mt-6 rounded-control bg-gold-100/50 p-4 border border-hairline/80 max-w-md mx-auto">
                @if ($account['bank'])
                    <p class="t-label-sm text-accent-strong font-semibold tracking-wider">{{ $account['bank'] }}</p>
                @endif
                @if ($account['account_name'])
                    <p class="t-title mt-1 text-ink font-bold">{{ $account['account_name'] }}</p>
                @endif
                @if ($account['account_number'])
                    <div class="mt-3 flex items-center justify-center gap-3"
                         x-data="copy(@js($account['account_number']), @js(__('invitation.copied', [], 'km').' · '.__('invitation.copied', [], 'en')))">
                        <span class="t-body-lg font-mono font-semibold tabular-nums text-primary tracking-wide">{{ $account['account_number'] }}</span>
                        <button type="button" class="btn btn-primary t-label-sm !min-h-9 !px-3 shadow-sm cursor-pointer" @click="copy()">
                            <x-base::icon name="copy" class="size-4" />
                            <x-base::t key="copy" />
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>