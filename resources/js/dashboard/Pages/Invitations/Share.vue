<script setup>
import { computed, ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import Field from '../../Components/Field.vue';

// Plain, functional markup; styling is left to the design pass.
const props = defineProps({ invitation: Object, hasCover: Boolean, message: String, guests: Array });

const base = `/app/invitations/${props.invitation.id}`;
const keep = { preserveScroll: true, preserveState: true };

const form = useForm({ message: props.message });
const saveMessage = () => form.put(`${base}/share`, keep);

const text = (name, url) => form.message.replaceAll('{name}', name).replaceAll('{link}', url);
const telegram = (name, url) => `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text(name, url).replace(url, '').trim())}`;

const copied = ref(null);
async function copy(key, name, url) {
    await navigator.clipboard?.writeText(text(name, url));
    copied.value = key;
    setTimeout(() => copied.value === key && (copied.value = null), 1500);
    return true;
}

// The phone's share sheet (Telegram, Messenger…); desktop browsers without it fall back to copying.
async function share(key, name, url) {
    if (!navigator.share) return copy(key, name, url);
    try {
        await navigator.share({ text: text(name, url) });
        return true;
    } catch {
        return false; // cancelled
    }
}

const markSent = (guest) => guest && !guest.sent && router.put(`${base}/guests/${guest.id}/sent`, { sent: true }, keep);
const toggleSent = (guest, sent) => router.put(`${base}/guests/${guest.id}/sent`, { sent }, keep);

const search = ref('');
// "Sent, not opened" is who to nudge again.
const filters = {
    all: () => true,
    opened: (g) => g.open_count > 0,
    unopened: (g) => g.open_count === 0,
    sent_unopened: (g) => g.sent && g.open_count === 0,
};
const filter = ref('all');
const filterCounts = computed(() => Object.fromEntries(Object.entries(filters).map(([key, test]) => [key, props.guests.filter(test).length])));
const openedCount = computed(() => filterCounts.value.opened);
// list (the guest list's order) | recent (last opened first, never opened at the end)
const sort = ref('list');
const shown = computed(() => {
    const q = search.value.trim().toLowerCase();
    const list = props.guests.filter((g) => filters[filter.value](g) && (!q || g.name.toLowerCase().includes(q)));
    return sort.value === 'recent' ? list.sort((a, b) => (b.opened_at ?? 0) - (a.opened_at ?? 0)) : list;
});
const sentCount = computed(() => props.guests.filter((g) => g.sent).length);
</script>

<template>
    <AppLayout>
        <div class="grid gap-6">
            <!-- Header Bar -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-stone-200/80 pb-4">
                <div class="flex items-center gap-3">
                    <Link
                        :href="`${base}/edit`"
                        class="flex size-9 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-600 hover:text-stone-900 hover:border-stone-300 transition-colors shadow-xs"
                        :title="$t('common.back_to_editor')"
                    >
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl font-bold tracking-tight text-stone-900">{{ $t('editor.share') }}</h1>
                        </div>
                        <p class="text-xs text-stone-500 font-kantumruy">{{ $t('share.subtitle') }}</p>
                    </div>
                </div>

                <!-- Link to Guest List -->
                <div class="flex items-center gap-2">
                    <Link
                        :href="`${base}/guests`"
                        class="btn-secondary !py-2 !px-3.5 !text-xs"
                    >
                        <svg class="size-3.5 text-stone-600" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                        </svg>
                        <span>{{ $t('guests.directory') }}</span>
                    </Link>
                </div>
            </div>

            <!-- Notice: Add cover photo first -->
            <div v-if="!hasCover" class="card p-6 border-amber-300 bg-amber-50/60 grid gap-3" role="alert">
                <div class="flex items-start gap-3">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-amber-200 text-amber-900 shrink-0">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <polyline points="21 15 16 10 5 21" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h2 class="text-base font-bold text-amber-950">{{ $t('share.cover_first') }}</h2>
                        <p class="text-xs text-amber-900 font-kantumruy">
                            {{ $t('share.cover_first_hint') }}
                        </p>
                    </div>
                </div>
                <div class="pt-2">
                    <Link :href="`${base}/edit/photos`" class="btn-primary !text-xs !py-2 !px-4">
                        <span>{{ $t('share.add_cover') }} →</span>
                    </Link>
                </div>
            </div>

            <template v-else>
                <!-- Paused or Ended Status Callout -->
                <div v-if="invitation.state === 'paused' || invitation.state === 'ended'" class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs font-medium text-rose-800 flex items-center gap-2.5">
                    <svg class="size-4 text-rose-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>
                        {{ $t('share.unavailable', { state: $t(`states.${invitation.state}`) }) }}
                    </span>
                </div>

                <!-- Custom Share Message Form -->
                <form class="card p-6 grid gap-4" @submit.prevent="saveMessage">
                    <div class="border-b border-stone-100 pb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-amber-100 text-amber-800">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                </svg>
                            </div>
                            <h2 class="text-base font-bold text-stone-900">{{ $t('share.message_title') }}</h2>
                        </div>
                        <span class="text-[11px] text-stone-500 font-mono">{name} · {link}</span>
                    </div>

                    <Field :label="$t('share.message_label')" :error="form.errors.message">
                        <textarea
                            v-model="form.message"
                            rows="3"
                            class="input font-kantumruy text-sm"
                            placeholder="សូមគោរពអញ្ជើញ {name} ចូលរួមពិធីមង្គលការរបស់យើងខ្ញុំ តាមរយៈតំណភ្ជាប់នេះ៖ {link}"
                        ></textarea>
                    </Field>

                    <div class="flex justify-end pt-1">
                        <button
                            type="submit"
                            class="btn-secondary min-h-[44px]"
                            :disabled="form.processing || !form.isDirty"
                        >
                            <span>{{ $t('share.save_message') }}</span>
                        </button>
                    </div>
                </form>

                <!-- Generic Invitation Link Card -->
                <div class="card p-6 grid gap-3">
                    <div class="border-b border-stone-100 pb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-stone-100 text-stone-800">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-stone-900">{{ $t('share.generic') }}</h2>
                                <p class="text-xs text-stone-500 font-kantumruy">{{ $t('share.generic_hint') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- URL Box -->
                    <div class="rounded-lg border border-stone-200 bg-stone-50/80 p-3 flex items-center justify-between gap-3 overflow-hidden">
                        <a
                            :href="invitation.url"
                            target="_blank"
                            class="font-mono text-xs sm:text-sm text-stone-800 underline truncate hover:text-[#7B1122] transition-colors"
                        >
                            {{ invitation.url }}
                        </a>
                        <svg class="size-4 text-stone-400 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                            <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                        </svg>
                    </div>

                    <!-- Generic Action Buttons -->
                    <div class="flex flex-wrap gap-2 pt-1">
                        <button
                            type="button"
                            class="inline-flex min-h-[44px] items-center gap-1.5 rounded-lg border border-stone-200 bg-white px-4 py-2 text-xs font-semibold text-stone-700 shadow-xs hover:bg-stone-50 hover:border-stone-300 transition-colors cursor-pointer"
                            @click="copy('generic', 'ភ្ញៀវកិត្តិយស', invitation.url)"
                        >
                            <svg class="size-4 text-stone-500" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 2H9a3 3 0 01-3-2z" />
                            </svg>
                            <span>{{ copied === 'generic' ? $t('common.copied') : $t('common.copy') }}</span>
                        </button>

                        <button
                            type="button"
                            class="inline-flex min-h-[44px] items-center gap-1.5 rounded-lg border border-stone-200 bg-white px-4 py-2 text-xs font-semibold text-stone-700 shadow-xs hover:bg-stone-50 hover:border-stone-300 transition-colors cursor-pointer"
                            @click="share('generic', 'ភ្ញៀវកិត្តិយស', invitation.url)"
                        >
                            <svg class="size-4 text-stone-500" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                            </svg>
                            <span>{{ $t('share.share') }}</span>
                        </button>

                        <a
                            :href="telegram('ភ្ញៀវកិត្តិយស', invitation.url)"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex min-h-[44px] items-center gap-1.5 rounded-lg border border-sky-200 bg-sky-50 px-4 py-2 text-xs font-semibold text-sky-800 shadow-xs hover:bg-sky-100 hover:border-sky-300 transition-colors"
                        >
                            <svg class="size-4 text-sky-600" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/>
                            </svg>
                            <span>Telegram</span>
                        </a>
                    </div>
                </div>

                <!-- Personal Links List Section -->
                <div class="card p-6 grid gap-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-stone-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-amber-100 text-amber-800">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="8.5" cy="7" r="4" />
                                    <line x1="20" y1="8" x2="20" y2="14" />
                                    <line x1="23" y1="11" x2="17" y2="11" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-stone-900">{{ $t('share.personal') }}</h2>
                                <p class="text-xs text-stone-500 font-kantumruy">{{ $t('share.personal_hint') }}</p>
                            </div>
                        </div>

                        <!-- Sent Progress Counter -->
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-stone-200 bg-stone-50 px-3 py-1 text-xs font-bold text-stone-700">
                                <span class="size-2 rounded-full" :class="sentCount === guests.length && guests.length > 0 ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                                <span>{{ $t('share.sent_count', { sent: sentCount, total: guests.length }) }}</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-stone-200 bg-stone-50 px-3 py-1 text-xs font-bold text-stone-700">
                                <span>{{ $t('share.opened_count', { opened: openedCount, total: guests.length }) }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- When no guests yet -->
                    <div v-if="!guests.length" class="text-center py-10 rounded-xl border border-dashed border-stone-200 bg-stone-50/50">
                        <p class="text-sm font-semibold text-stone-800">{{ $t('guests.empty') }}</p>
                        <p class="text-xs text-stone-500 mt-1 mb-3">{{ $t('share.no_guests_hint') }}</p>
                        <Link :href="`${base}/guests`" class="btn-primary !text-xs !py-2 !px-4">
                            <span>{{ $t('share.add_guests') }} →</span>
                        </Link>
                    </div>

                    <template v-else>
                        <!-- Search Box and sort -->
                        <div class="flex flex-col sm:flex-row gap-2">
                        <div class="relative w-full sm:w-72">
                            <svg class="size-4 text-stone-400 absolute left-3 top-3 pointer-events-none" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                            <input
                                v-model="search"
                                class="input !pl-9 min-h-[44px]"
                                :placeholder="$t('guests.search')"
                            />
                        </div>
                        <select v-model="sort" class="input min-h-[44px] w-full sm:w-auto" :aria-label="$t('share.sort')">
                            <option value="list">{{ $t('share.sort_list') }}</option>
                            <option value="recent">{{ $t('share.sort_recent') }}</option>
                        </select>
                        </div>

                        <!-- Opened / not opened filter -->
                        <div class="flex flex-wrap gap-2" role="group" :aria-label="$t('share.filter')">
                            <button
                                v-for="key in Object.keys(filters)"
                                :key="key"
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-full border px-3 min-h-[36px] text-xs font-bold transition-colors"
                                :class="filter === key ? 'border-stone-900 bg-stone-900 text-white' : 'border-stone-200 bg-white text-stone-700 hover:bg-stone-50'"
                                :aria-pressed="filter === key"
                                @click="filter = key"
                            >
                                <span>{{ $t(`share.filter_${key}`) }}</span>
                                <span class="opacity-70">{{ filterCounts[key] }}</span>
                            </button>
                        </div>

                        <p v-if="!shown.length" class="text-sm text-stone-500 text-center py-6">{{ $t('share.no_match') }}</p>

                        <!-- 1. MOBILE RESPONSIVE CARDS (Phone first) -->
                        <div class="grid gap-3 md:hidden">
                            <div
                                v-for="guest in shown"
                                :key="guest.id"
                                class="rounded-xl border border-stone-200/90 bg-white p-4 shadow-xs space-y-3"
                            >
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="font-bold text-stone-900 text-sm font-kantumruy" lang="km">{{ guest.name }}</p>
                                        <div class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-stone-500">
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="size-3.5 text-stone-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                </svg>
                                                <span>{{ $t('share.opens', { count: guest.open_count }) }}</span>
                                            </span>
                                            <template v-if="guest.last_opened">
                                                <span>·</span>
                                                <span>{{ $t('share.last_opened', { time: guest.last_opened }) }}</span>
                                            </template>
                                            <span>·</span>
                                            <a :href="guest.qr" target="_blank" class="text-stone-700 underline font-semibold">{{ $t('share.view_qr') }}</a>
                                        </div>
                                    </div>

                                    <!-- Sent Checkbox -->
                                    <label class="flex items-center gap-1.5 min-h-[44px] min-w-[44px] justify-end cursor-pointer">
                                        <input
                                            type="checkbox"
                                            :checked="guest.sent"
                                            class="size-4.5 rounded text-[#7B1122] accent-[#7B1122] focus:ring-amber-500 cursor-pointer"
                                            @change="toggleSent(guest, $event.target.checked)"
                                            :aria-label="$t('guests.sent')"
                                        />
                                        <span class="text-xs font-semibold" :class="guest.sent ? 'text-emerald-700' : 'text-stone-400'">
                                            {{ guest.sent ? $t('guests.sent') : $t('guests.unsent') }}
                                        </span>
                                    </label>
                                </div>

                                <!-- Action Buttons on Mobile -->
                                <div class="flex items-center gap-2 border-t border-stone-100 pt-2.5">
                                    <button
                                        type="button"
                                        class="flex-1 inline-flex min-h-[44px] items-center justify-center rounded-lg border border-stone-200 bg-stone-50 px-3 py-1.5 text-xs font-semibold text-stone-700 hover:bg-amber-50 hover:border-amber-300 transition-colors cursor-pointer"
                                        @click="copy(guest.id, guest.name, guest.url).then(() => markSent(guest))"
                                    >
                                        {{ copied === guest.id ? $t('common.copied') : $t('common.copy') }}
                                    </button>

                                    <button
                                        type="button"
                                        class="flex-1 inline-flex min-h-[44px] items-center justify-center rounded-lg border border-stone-200 bg-stone-50 px-3 py-1.5 text-xs font-semibold text-stone-700 hover:bg-amber-50 hover:border-amber-300 transition-colors cursor-pointer"
                                        @click="share(guest.id, guest.name, guest.url).then((ok) => ok && markSent(guest))"
                                    >
                                        {{ $t('share.share') }}
                                    </button>

                                    <a
                                        :href="telegram(guest.name, guest.url)"
                                        target="_blank"
                                        rel="noopener"
                                        class="inline-flex min-h-[44px] items-center justify-center rounded-lg border border-sky-200 bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-800 hover:bg-sky-100 transition-colors"
                                        @click="markSent(guest)"
                                    >
                                        Telegram
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- 2. DESKTOP TABULAR VIEW -->
                        <div class="hidden md:block overflow-x-auto rounded-xl border border-stone-200">
                            <table class="w-full text-sm">
                                <thead class="bg-stone-50/80 border-b border-stone-200 text-stone-600 font-semibold text-xs">
                                    <tr>
                                        <th class="py-3 px-4 text-left">{{ $t('guests.guest') }}</th>
                                        <th class="py-3 px-4 text-left">{{ $t('share.send') }}</th>
                                        <th class="py-3 px-4 text-center">QR</th>
                                        <th class="py-3 px-4 text-center">{{ $t('guests.opens') }}</th>
                                        <th class="py-3 px-4 text-center">{{ $t('guests.sent') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-100">
                                    <tr v-for="guest in shown" :key="guest.id" class="hover:bg-amber-50/20 transition-colors">
                                        <td class="py-3 px-4 font-kantumruy font-semibold text-stone-900" lang="km">
                                            {{ guest.name }}
                                        </td>
                                        <td class="py-3 px-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center gap-1 rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1 text-xs font-semibold text-stone-700 hover:bg-amber-50 hover:border-amber-300 transition-colors cursor-pointer"
                                                    @click="copy(guest.id, guest.name, guest.url).then(() => markSent(guest))"
                                                >
                                                    {{ copied === guest.id ? $t('common.copied') : $t('common.copy') }}
                                                </button>

                                                <button
                                                    type="button"
                                                    class="inline-flex items-center gap-1 rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1 text-xs font-semibold text-stone-700 hover:bg-amber-50 hover:border-amber-300 transition-colors cursor-pointer"
                                                    @click="share(guest.id, guest.name, guest.url).then((ok) => ok && markSent(guest))"
                                                >
                                                    {{ $t('share.share') }}
                                                </button>

                                                <a
                                                    :href="telegram(guest.name, guest.url)"
                                                    target="_blank"
                                                    rel="noopener"
                                                    class="inline-flex items-center gap-1 rounded-md border border-sky-200 bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-800 hover:bg-sky-100 transition-colors"
                                                    @click="markSent(guest)"
                                                >
                                                    <svg class="size-3.5 text-sky-600" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/>
                                                    </svg>
                                                    <span>Telegram</span>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <a
                                                :href="guest.qr"
                                                target="_blank"
                                                class="inline-flex items-center gap-1 text-xs font-semibold text-stone-700 hover:text-stone-900 underline"
                                            >
                                                <svg class="size-3.5 text-stone-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <rect x="3" y="3" width="7" height="7"></rect>
                                                    <rect x="14" y="3" width="7" height="7"></rect>
                                                    <rect x="14" y="14" width="7" height="7"></rect>
                                                    <rect x="3" y="14" width="7" height="7"></rect>
                                                </svg>
                                                <span>QR</span>
                                            </a>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="inline-flex items-center gap-1 rounded-full bg-stone-100 px-2 py-0.5 text-xs font-medium text-stone-700">
                                                <svg class="size-3 text-stone-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                </svg>
                                                <span>{{ guest.open_count }}</span>
                                            </span>
                                            <p v-if="guest.last_opened" class="mt-1 text-[11px] text-stone-500">{{ guest.last_opened }}</p>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <input
                                                type="checkbox"
                                                :checked="guest.sent"
                                                class="size-4.5 rounded text-[#7B1122] accent-[#7B1122] focus:ring-amber-500 cursor-pointer"
                                                @change="toggleSent(guest, $event.target.checked)"
                                                :aria-label="$t('guests.sent')"
                                            />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
