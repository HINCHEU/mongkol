{{-- Traditional Khmer Guest Book & Wishes Section (សៀវភៅពាក្យជូនពរ). Wishes are strictly plain text: x-text never renders HTML. --}}
<x-base::section-title section="wishes" />

@php($guestName = $vm->guest ? trim(implode(' ', array_filter([$vm->guest['title'], $vm->guest['name'], $vm->guest['suffix']]))) : null)
<div class="grid gap-6" x-data="wishes(@js($formsUrl), @js($vm->data['wishes'] ?? []), @js($guestName))">
    {{-- Wish Submission Form Card --}}
    <form class="inv-card inv-card--framed relative grid gap-4 !p-6 sm:!p-8" @submit.prevent="send()">
        <x-base::kbach class="pointer-events-none absolute top-2 left-2 size-7 text-accent opacity-50" />
        <x-base::kbach class="pointer-events-none absolute top-2 right-2 size-7 text-accent opacity-50 rotate-90" />
        <x-base::kbach class="pointer-events-none absolute bottom-2 left-2 size-7 text-accent opacity-50 -rotate-90" />
        <x-base::kbach class="pointer-events-none absolute bottom-2 right-2 size-7 text-accent opacity-50 rotate-180" />

        @if ($guestName)
            <div class="flex items-center gap-2 pb-1 text-accent-strong">
                <svg class="size-4 shrink-0 text-accent" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
                <p class="t-body-sm font-semibold truncate" lang="km">{{ $guestName }}</p>
            </div>
        @else
            <label class="grid gap-1.5 text-left">
                <span class="t-label-sm text-accent-strong font-semibold"><x-base::t key="your_name" /></span>
                <input x-model="name" required maxlength="100" class="input !py-3 !px-4" autocomplete="name" placeholder="{{ __('invitation.your_name', [], 'km') }}">
            </label>
        @endif

        <label class="grid gap-1.5 text-left">
            <span class="sr-only"><x-base::t key="wishes" /></span>
            <textarea x-model="message" required maxlength="500" rows="3" class="input !p-3.5 leading-relaxed"
                      :placeholder="$store.lang.current === 'km' ? @js(__('invitation.wish_placeholder', [], 'km')) : @js(__('invitation.wish_placeholder', [], 'en'))"></textarea>
        </label>

        {{-- Honeypot: off-screen for people, tempting for bots (DO NOT REMOVE) --}}
        <input type="text" name="website" x-model="website" tabindex="-1" autocomplete="off" aria-hidden="true" class="absolute -left-[9999px]">

        <div>
            <button type="submit" class="btn btn-primary t-label w-full min-h-[48px] shadow-sm" :disabled="state === 'sending' || !message.trim()">
                <span x-show="state !== 'sending'"><x-base::t key="send" /></span>
                <span x-show="state === 'sending'" x-cloak class="inline-flex items-center gap-2">
                    <svg class="size-4 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>...</span>
                </span>
            </button>
        </div>

        {{-- Status Feedback Alerts --}}
        <p x-show="state === 'sent'" x-cloak class="t-body-sm text-center text-primary font-medium bg-gold-100 p-3 rounded-xl border border-accent/30" role="status">
            <x-base::t key="wish_thanks" />
        </p>
        <p x-show="state === 'pending'" x-cloak class="t-body-sm text-center text-accent-text font-medium bg-gold-100/70 p-3 rounded-xl border border-accent/30" role="status">
            <x-base::t key="wish_pending" />
        </p>
        <p x-show="state === 'error'" x-cloak class="t-body-sm text-center text-primary font-medium bg-primary-soft/80 p-2.5 rounded-lg border border-primary/20" role="alert">
            <x-base::t key="form_error" />
        </p>
        <p x-show="state === 'demo'" x-cloak class="t-body-sm text-center text-accent-text bg-gold-100 p-2.5 rounded-lg border border-accent/20" role="status">
            <x-base::t key="form_demo" />
        </p>
    </form>

    {{-- Guest Book List of Wishes (Newest First) --}}
    <ul class="grid gap-3.5" x-show="list.length">
        <template x-for="(wish, i) in list" :key="i">
            <li class="inv-card !p-5 relative border-l-3 border-l-accent transition-all duration-200">
                <p class="t-body text-ink font-normal leading-relaxed whitespace-pre-wrap" x-text="wish.message"></p>
                <div class="mt-2.5 flex items-center justify-between gap-2 border-t border-hairline/40 pt-2">
                    <p class="t-label-sm text-accent-strong font-semibold" x-text="'— ' + wish.name"></p>
                    <span class="text-xs text-accent-text/60" aria-hidden="true">❦</span>
                </div>
            </li>
        </template>
    </ul>

    {{-- Empty State --}}
    <div x-show="!list.length" class="text-center py-6 px-4 rounded-xl border border-dashed border-hairline bg-surface/50">
        <x-base::icon name="lotus" class="mx-auto size-8 text-accent/60 mb-2" />
        <p class="t-body-sm text-ink-muted"><x-base::t key="wishes_empty" /></p>
    </div>
</div>
