{{-- Plain, functional markup; styling is the design pass's job. A personal link answers as that guest (no name field). --}}
<x-base::section-title section="rsvp" />

<form class="inv-card grid gap-4 !p-6" x-data="rsvp(@js($formsUrl), @js($vm->guest['rsvp'] ?? null))" @submit.prevent="send()">
    <template x-if="state === 'sent'">
        <div class="grid gap-3 text-center" role="status">
            <p class="t-body"><x-base::t key="rsvp_thanks" /></p>
            <button type="button" class="btn btn-secondary t-label" @click="state = null"><x-base::t key="rsvp_change" /></button>
        </div>
    </template>

    <template x-if="state !== 'sent'">
        <div class="grid gap-4">
            @unless ($vm->guest)
                <label class="grid gap-1">
                    <span class="t-label-sm"><x-base::t key="your_name" /></span>
                    <input x-model="name" required maxlength="100" class="input" autocomplete="name">
                </label>
            @endunless

            <div class="grid gap-2 sm:grid-cols-2" role="radiogroup">
                <label class="btn btn-secondary t-label" :class="attending === true && 'btn-primary'">
                    <input type="radio" class="sr-only" :value="true" x-model="attending" @change="attending = true" required>
                    <x-base::t key="rsvp_yes" />
                </label>
                <label class="btn btn-secondary t-label" :class="attending === false && 'btn-primary'">
                    <input type="radio" class="sr-only" :value="false" x-model="attending" @change="attending = false">
                    <x-base::t key="rsvp_no" />
                </label>
            </div>

            <label class="grid gap-1" x-show="attending === true">
                <span class="t-label-sm"><x-base::t key="rsvp_count" /></span>
                <input type="number" min="1" max="20" x-model.number="party_size" class="input" inputmode="numeric">
            </label>

            <label class="grid gap-1">
                <span class="t-label-sm"><x-base::t key="rsvp_note" /></span>
                <textarea x-model="message" maxlength="500" rows="2" class="input"></textarea>
            </label>

            {{-- Honeypot: off-screen for people, tempting for bots. --}}
            <input type="text" name="website" x-model="website" tabindex="-1" autocomplete="off" aria-hidden="true" class="absolute -left-[9999px]">

            <button type="submit" class="btn btn-primary t-label" :disabled="attending === null || state === 'sending'"><x-base::t key="send" /></button>
            <p x-show="state === 'error'" x-cloak class="t-body-sm text-primary" role="alert"><x-base::t key="form_error" /></p>
            <p x-show="state === 'demo'" x-cloak class="t-body-sm" role="status"><x-base::t key="form_demo" /></p>
        </div>
    </template>
</form>
