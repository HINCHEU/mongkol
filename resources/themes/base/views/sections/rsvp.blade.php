{{-- Royal Khmer RSVP Section (បញ្ជាក់ការចូលរួម). A personal link answers as that guest (no name field). --}}
<x-base::section-title section="rsvp" />

<form class="inv-card inv-card--framed relative !p-6 sm:!p-8" x-data="rsvp(@js($formsUrl), @js($vm->guest['rsvp'] ?? null))" @submit.prevent="send()">
    <x-base::kbach class="pointer-events-none absolute top-2 left-2 size-7 text-accent opacity-50" />
    <x-base::kbach class="pointer-events-none absolute top-2 right-2 size-7 text-accent opacity-50 rotate-90" />
    <x-base::kbach class="pointer-events-none absolute bottom-2 left-2 size-7 text-accent opacity-50 -rotate-90" />
    <x-base::kbach class="pointer-events-none absolute bottom-2 right-2 size-7 text-accent opacity-50 rotate-180" />

    {{-- State 1: Thank You & Confirmation State --}}
    <template x-if="state === 'sent'">
        <div class="py-6 text-center space-y-4" role="status">
            <div class="mx-auto flex size-14 items-center justify-center rounded-full bg-primary/10 text-primary shadow-inner">
                <svg class="size-7 text-accent-strong" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
            </div>
            <div>
                <p class="t-headline-sm text-ink font-semibold"><x-base::t key="rsvp_thanks" /></p>
                <p class="t-body-sm mt-1 text-ink-muted">
                    <span x-show="attending === true"><x-base::t key="rsvp_yes" /> (<span x-text="party_size"></span>)</span>
                    <span x-show="attending === false"><x-base::t key="rsvp_no" /></span>
                </p>
            </div>
            <div class="pt-2">
                <button type="button" class="btn btn-secondary t-label mx-auto" @click="state = null">
                    <x-base::t key="rsvp_change" />
                </button>
            </div>
        </div>
    </template>

    {{-- State 2: Interactive Form --}}
    <template x-if="state !== 'sent'">
        <div class="grid gap-5">
            {{-- Guest Name Input (Only on generic link) --}}
            @unless ($vm->guest)
                <label class="grid gap-1.5 text-left">
                    <span class="t-label-sm text-accent-strong font-semibold"><x-base::t key="your_name" /></span>
                    <input x-model="name" required maxlength="100" class="input !py-3 !px-4" autocomplete="name" placeholder="{{ __('invitation.your_name', [], 'km') }}">
                </label>
            @endunless

            {{-- Attendance Choice (Yes / No cards) --}}
            <div class="grid gap-3 sm:grid-cols-2" role="radiogroup" aria-label="{{ __('invitation.sections.rsvp', [], 'en') }}">
                {{-- Coming Option --}}
                <label class="relative flex flex-col items-center justify-center gap-2 p-4 rounded-xl border-2 transition-all duration-200 cursor-pointer text-center select-none min-h-[58px]"
                       :class="attending === true
                           ? 'border-primary bg-primary-soft/70 shadow-sm text-primary font-semibold ring-1 ring-primary/40'
                           : 'border-hairline bg-surface hover:border-accent hover:bg-gold-100/30 text-ink'">
                    <input type="radio" class="sr-only" :value="true" x-model="attending" @change="attending = true" required>
                    <div class="flex items-center gap-2">
                        <svg class="size-4.5" :class="attending === true ? 'text-primary' : 'text-accent-strong'" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="t-label"><x-base::t key="rsvp_yes" /></span>
                    </div>
                </label>

                {{-- Can't Come Option --}}
                <label class="relative flex flex-col items-center justify-center gap-2 p-4 rounded-xl border-2 transition-all duration-200 cursor-pointer text-center select-none min-h-[58px]"
                       :class="attending === false
                           ? 'border-primary bg-primary-soft/70 shadow-sm text-primary font-semibold ring-1 ring-primary/40'
                           : 'border-hairline bg-surface hover:border-accent hover:bg-gold-100/30 text-ink'">
                    <input type="radio" class="sr-only" :value="false" x-model="attending" @change="attending = false">
                    <div class="flex items-center gap-2">
                        <svg class="size-4.5" :class="attending === false ? 'text-primary' : 'text-stone-400'" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span class="t-label"><x-base::t key="rsvp_no" /></span>
                    </div>
                </label>
            </div>

            {{-- Guest Count Stepper (shown only when coming) --}}
            <div class="rounded-xl border border-hairline bg-gold-100/40 p-4 text-left transition-all" x-show="attending === true" x-cloak>
                <label class="grid gap-2">
                    <span class="t-label-sm text-accent-strong font-semibold"><x-base::t key="rsvp_count" /></span>
                    <div class="flex items-center gap-3">
                        <input type="number" min="1" max="20" x-model.number="party_size" class="input !py-2.5 !px-3 font-semibold text-center text-lg max-w-[120px]" inputmode="numeric">
                        <span class="t-body-sm text-ink-muted">
                            <span lang="km">នាក់ (រួមទាំងអ្នក)</span>
                            <span lang="en">people (including you)</span>
                        </span>
                    </div>
                </label>
            </div>

            {{-- Optional Note for the Couple --}}
            <label class="grid gap-1.5 text-left">
                <span class="t-label-sm text-accent-strong font-semibold"><x-base::t key="rsvp_note" /></span>
                <textarea x-model="message" maxlength="500" rows="3" class="input !p-3" placeholder="{{ __('invitation.rsvp_note', [], 'km') }}"></textarea>
            </label>

            {{-- Honeypot: off-screen for people, tempting for bots (DO NOT REMOVE) --}}
            <input type="text" name="website" x-model="website" tabindex="-1" autocomplete="off" aria-hidden="true" class="absolute -left-[9999px]">

            {{-- Submit Action --}}
            <div class="pt-2">
                <button type="submit" class="btn btn-primary t-label w-full min-h-[48px] shadow-sm" :disabled="attending === null || state === 'sending'">
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

            {{-- Feedback Alerts --}}
            <p x-show="state === 'error'" x-cloak class="t-body-sm text-primary font-medium bg-primary-soft/80 p-2.5 rounded-lg border border-primary/20 text-center" role="alert">
                <x-base::t key="form_error" />
            </p>
            <p x-show="state === 'demo'" x-cloak class="t-body-sm text-accent-text bg-gold-100 p-2.5 rounded-lg border border-accent/20 text-center" role="status">
                <x-base::t key="form_demo" />
            </p>
        </div>
    </template>
</form>
