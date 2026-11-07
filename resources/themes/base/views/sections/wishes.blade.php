{{-- Plain, functional markup; styling is the design pass's job. Wishes are plain text: x-text never renders HTML. --}}
<x-base::section-title section="wishes" />

@php($guestName = $vm->guest ? trim(implode(' ', array_filter([$vm->guest['title'], $vm->guest['name'], $vm->guest['suffix']]))) : null)
<div class="grid gap-6" x-data="wishes(@js($formsUrl), @js($vm->data['wishes'] ?? []), @js($guestName))">
    <form class="inv-card grid gap-3 !p-6" @submit.prevent="send()">
        @unless ($vm->guest)
            <label class="grid gap-1">
                <span class="t-label-sm"><x-base::t key="your_name" /></span>
                <input x-model="name" required maxlength="100" class="input" autocomplete="name">
            </label>
        @endunless
        <textarea x-model="message" required maxlength="500" rows="3" class="input"
                  :placeholder="$store.lang.current === 'km' ? @js(__('invitation.wish_placeholder', [], 'km')) : @js(__('invitation.wish_placeholder', [], 'en'))"></textarea>
        <input type="text" name="website" x-model="website" tabindex="-1" autocomplete="off" aria-hidden="true" class="absolute -left-[9999px]">
        <button type="submit" class="btn btn-primary t-label" :disabled="state === 'sending' || !message.trim()"><x-base::t key="send" /></button>

        <p x-show="state === 'sent'" x-cloak class="t-body-sm" role="status"><x-base::t key="wish_thanks" /></p>
        <p x-show="state === 'pending'" x-cloak class="t-body-sm" role="status"><x-base::t key="wish_pending" /></p>
        <p x-show="state === 'error'" x-cloak class="t-body-sm text-primary" role="alert"><x-base::t key="form_error" /></p>
        <p x-show="state === 'demo'" x-cloak class="t-body-sm" role="status"><x-base::t key="form_demo" /></p>
    </form>

    <ul class="grid gap-3">
        <template x-for="(wish, i) in list" :key="i">
            <li class="inv-card !p-4">
                <p class="t-body" x-text="wish.message"></p>
                <p class="t-label-sm mt-1 text-accent-text" x-text="'— ' + wish.name"></p>
            </li>
        </template>
    </ul>
    <p x-show="!list.length" class="t-body-sm text-center text-ink-muted"><x-base::t key="wishes_empty" /></p>
</div>
