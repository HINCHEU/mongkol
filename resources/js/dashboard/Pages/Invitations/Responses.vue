<script setup>
import { Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import { t } from '../../i18n.js';

// Plain, functional markup; styling is left to the design pass.
const props = defineProps({ invitation: Object, settings: Object, totals: Object, rsvps: Array, wishes: Array });

const base = `/app/invitations/${props.invitation.id}`;
const keep = { preserveScroll: true, preserveState: true };

// Each switch saves at once.
const settings = useForm({ ...props.settings });
const saveSettings = () => settings.put(`${base}/responses/settings`, keep);

const moderate = (wish, hidden) => router.put(`${base}/wishes/${wish.id}`, { hidden }, keep);
const removeWish = (wish) => confirm(t('responses.confirm_delete_wish')) && router.delete(`${base}/wishes/${wish.id}`, keep);
const removeRsvp = (rsvp) => confirm(t('responses.confirm_delete_rsvp', { name: rsvp.name })) && router.delete(`${base}/rsvps/${rsvp.id}`, keep);

const when = (iso) => new Date(iso).toLocaleString();
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto grid gap-6 pb-12">
            <!-- Header Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-stone-200/80 pb-5">
                <div>
                    <Link
                        :href="`${base}/edit`"
                        class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-stone-600 hover:text-[#7B1122] transition-colors mb-2 min-h-[44px]"
                    >
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ $t('common.back_to_editor') }}</span>
                    </Link>
                    <h1 class="font-serif text-2xl sm:text-3xl font-bold tracking-tight text-stone-900">
                        {{ $t('editor.responses') }}
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        :href="`${base}/responses/export`"
                        class="inline-flex min-h-[44px] items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-xs sm:text-sm font-semibold text-stone-700 shadow-xs hover:bg-stone-50 hover:border-stone-300 transition-all cursor-pointer"
                    >
                        <svg class="size-4 text-emerald-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="7 10 12 15 17 10" />
                            <line x1="12" y1="15" x2="12" y2="3" />
                        </svg>
                        <span>{{ $t('guests.export') }}</span>
                    </a>
                </div>
            </div>

            <!-- Settings / Feature Toggles Card -->
            <div class="card p-5 sm:p-6 bg-white border border-stone-200/80 rounded-2xl shadow-xs">
                <div class="divide-y divide-stone-100">
                    <!-- RSVP Toggle -->
                    <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                        <div class="flex items-center gap-3">
                            <div class="flex size-9 items-center justify-center rounded-xl bg-amber-50 text-amber-800 border border-amber-200/60 shadow-xs shrink-0">
                                <svg class="size-4.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <label for="toggle-rsvp" class="text-sm sm:text-base font-semibold text-stone-900 cursor-pointer select-none">
                                    {{ $t('responses.rsvp_enabled') }}
                                </label>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer min-h-[44px] min-w-[44px] justify-center">
                            <input
                                id="toggle-rsvp"
                                v-model="settings.rsvp_enabled"
                                type="checkbox"
                                class="sr-only peer"
                                @change="saveSettings"
                            />
                            <div class="w-11 h-6 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[12px] after:left-[4px] after:bg-white after:border-stone-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#7B1122]"></div>
                        </label>
                    </div>

                    <!-- Wishes Guestbook Toggle -->
                    <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                        <div class="flex items-center gap-3">
                            <div class="flex size-9 items-center justify-center rounded-xl bg-amber-50 text-amber-800 border border-amber-200/60 shadow-xs shrink-0">
                                <svg class="size-4.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M3.5 2A1.5 1.5 0 002 3.5v11A1.5 1.5 0 003.5 16h6.086a1.5 1.5 0 001.06-.44l4.915-4.914A1.5 1.5 0 0016 9.586V3.5A1.5 1.5 0 0014.5 2h-11z" />
                                </svg>
                            </div>
                            <div>
                                <label for="toggle-wishes" class="text-sm sm:text-base font-semibold text-stone-900 cursor-pointer select-none">
                                    {{ $t('responses.wishes_enabled') }}
                                </label>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer min-h-[44px] min-w-[44px] justify-center">
                            <input
                                id="toggle-wishes"
                                v-model="settings.wishes_enabled"
                                type="checkbox"
                                class="sr-only peer"
                                @change="saveSettings"
                            />
                            <div class="w-11 h-6 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[12px] after:left-[4px] after:bg-white after:border-stone-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#7B1122]"></div>
                        </label>
                    </div>

                    <!-- Wishes Approval Toggle -->
                    <div
                        class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0 transition-opacity"
                        :class="!settings.wishes_enabled ? 'opacity-50' : ''"
                    >
                        <div class="flex items-center gap-3">
                            <div class="flex size-9 items-center justify-center rounded-xl bg-stone-100 text-stone-600 border border-stone-200/60 shadow-xs shrink-0">
                                <svg class="size-4.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <label
                                    for="toggle-approval"
                                    class="text-sm sm:text-base font-semibold text-stone-900 select-none"
                                    :class="!settings.wishes_enabled ? 'cursor-not-allowed' : 'cursor-pointer'"
                                >
                                    {{ $t('responses.wishes_need_approval') }}
                                </label>
                            </div>
                        </div>
                        <label
                            class="relative inline-flex items-center min-h-[44px] min-w-[44px] justify-center"
                            :class="!settings.wishes_enabled ? 'cursor-not-allowed' : 'cursor-pointer'"
                        >
                            <input
                                id="toggle-approval"
                                v-model="settings.wishes_need_approval"
                                type="checkbox"
                                class="sr-only peer"
                                :disabled="!settings.wishes_enabled"
                                @change="saveSettings"
                            />
                            <div class="w-11 h-6 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[12px] after:left-[4px] after:bg-white after:border-stone-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#7B1122]"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Totals for Table Planning -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <!-- People Coming (Highlighted for Table Planning) -->
                <div class="relative overflow-hidden rounded-2xl border-2 border-amber-400/90 bg-gradient-to-br from-amber-50/90 via-amber-100/30 to-amber-50/60 p-4 sm:p-5 shadow-xs">
                    <div class="flex items-center justify-between gap-1 text-amber-900 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-800">
                            {{ $t('responses.people') }}
                        </span>
                        <svg class="size-4 text-amber-700" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                        </svg>
                    </div>
                    <p class="font-serif text-3xl sm:text-4xl font-extrabold text-[#7B1122]">
                        {{ totals.people }}
                    </p>
                </div>

                <!-- Attending Replies -->
                <div class="card p-4 sm:p-5 bg-white border border-stone-200/80 rounded-2xl shadow-xs">
                    <div class="flex items-center justify-between gap-1 text-stone-600 mb-2">
                        <span class="text-xs font-semibold text-stone-600">
                            {{ $t('responses.attending') }}
                        </span>
                        <span class="size-2 rounded-full bg-emerald-500"></span>
                    </div>
                    <p class="font-serif text-2xl sm:text-3xl font-bold text-emerald-800">
                        {{ totals.attending }}
                    </p>
                </div>

                <!-- Declined Replies -->
                <div class="card p-4 sm:p-5 bg-white border border-stone-200/80 rounded-2xl shadow-xs">
                    <div class="flex items-center justify-between gap-1 text-stone-600 mb-2">
                        <span class="text-xs font-semibold text-stone-600">
                            {{ $t('responses.declined') }}
                        </span>
                        <span class="size-2 rounded-full bg-stone-400"></span>
                    </div>
                    <p class="font-serif text-2xl sm:text-3xl font-bold text-stone-700">
                        {{ totals.declined }}
                    </p>
                </div>

                <!-- Waiting Replies (Personal Links Unanswered) -->
                <div class="card p-4 sm:p-5 bg-white border border-stone-200/80 rounded-2xl shadow-xs">
                    <div class="flex items-center justify-between gap-1 text-stone-600 mb-2">
                        <span class="text-xs font-semibold text-stone-600">
                            {{ $t('responses.waiting') }}
                        </span>
                        <span class="size-2 rounded-full bg-amber-400"></span>
                    </div>
                    <p class="font-serif text-2xl sm:text-3xl font-bold text-amber-800">
                        {{ totals.waiting }}
                    </p>
                </div>
            </div>

            <!-- Replies List Section -->
            <div class="card p-5 sm:p-6 bg-white border border-stone-200/80 rounded-2xl shadow-xs grid gap-4">
                <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                    <h2 class="font-serif text-lg sm:text-xl font-bold text-stone-900">
                        {{ $t('responses.replies', { count: totals.replies }) }}
                    </h2>
                </div>

                <!-- Empty State -->
                <div v-if="!rsvps.length" class="text-center py-10 px-4 rounded-xl border border-dashed border-stone-200 bg-stone-50/50">
                    <svg class="mx-auto size-8 text-stone-400 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M22 13V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2h9" />
                        <polyline points="22,6 12,13 2,6" />
                        <circle cx="18" cy="18" r="3" />
                        <line x1="20.2" y1="20.2" x2="22" y2="22" />
                    </svg>
                    <p class="text-sm text-stone-500 font-medium">
                        {{ $t('responses.no_replies') }}
                    </p>
                </div>

                <!-- Replies Listing -->
                <ul v-else class="divide-y divide-stone-100">
                    <li v-for="rsvp in rsvps" :key="rsvp.id" class="py-4 first:pt-1 last:pb-1">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <strong lang="km" class="text-base font-semibold text-stone-900">
                                    {{ rsvp.name }}
                                </strong>

                                <!-- Attending Status Badge -->
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold shadow-2xs"
                                    :class="rsvp.attending
                                        ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80'
                                        : 'bg-stone-100 text-stone-600 border border-stone-200'"
                                >
                                    <svg v-if="rsvp.attending" class="size-3 text-emerald-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                    <svg v-else class="size-3 text-stone-400 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ rsvp.attending ? $t('responses.coming', { count: rsvp.party_size }) : $t('responses.not_coming') }}</span>
                                </span>

                                <!-- Generic Link Indicator Badge -->
                                <span
                                    v-if="!rsvp.personal"
                                    class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-medium text-amber-800 border border-amber-200/70"
                                >
                                    {{ $t('responses.generic_link') }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3 self-end sm:self-auto">
                                <span class="text-xs text-stone-400 flex items-center gap-1">
                                    <svg class="size-3 text-stone-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ when(rsvp.at) }}</span>
                                </span>

                                <button
                                    v-if="!rsvp.personal"
                                    type="button"
                                    class="inline-flex items-center gap-1 min-h-[44px] px-2 text-xs font-medium text-rose-700 hover:text-rose-900 hover:underline transition-colors cursor-pointer"
                                    @click="removeRsvp(rsvp)"
                                >
                                    <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ $t('common.delete') }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Note / Message from Guest -->
                        <div v-if="rsvp.message" class="mt-2.5 rounded-xl bg-stone-50 border border-stone-200/60 p-3 text-sm text-stone-700 italic">
                            “{{ rsvp.message }}”
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Guest Book (Wishes) Section -->
            <div class="card p-5 sm:p-6 bg-white border border-stone-200/80 rounded-2xl shadow-xs grid gap-4">
                <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                    <h2 class="font-serif text-lg sm:text-xl font-bold text-stone-900">
                        {{ $t('responses.wishes', { count: wishes.length }) }}
                    </h2>
                </div>

                <!-- Empty State -->
                <div v-if="!wishes.length" class="text-center py-10 px-4 rounded-xl border border-dashed border-stone-200 bg-stone-50/50">
                    <svg class="mx-auto size-8 text-stone-400 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                    </svg>
                    <p class="text-sm text-stone-500 font-medium">
                        {{ $t('responses.no_wishes') }}
                    </p>
                </div>

                <!-- Wishes Listing -->
                <ul v-else class="grid gap-3.5">
                    <li
                        v-for="wish in wishes"
                        :key="wish.id"
                        class="rounded-xl border p-4 sm:p-5 transition-all duration-200"
                        :class="wish.is_hidden ? 'border-dashed border-stone-300 bg-stone-50/70 opacity-70' : 'border-stone-200/80 bg-white shadow-2xs'"
                    >
                        <p class="text-sm sm:text-base text-stone-800 leading-relaxed font-normal whitespace-pre-wrap">
                            {{ wish.message }}
                        </p>

                        <div class="mt-3 pt-2.5 border-t border-stone-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-semibold text-stone-900" lang="km">— {{ wish.name }}</span>
                                <span class="text-stone-400 flex items-center gap-1">
                                    <svg class="size-3 text-stone-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ when(wish.at) }}</span>
                                </span>
                                <span
                                    v-if="wish.is_hidden"
                                    class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] font-semibold text-amber-900 border border-amber-300/60"
                                >
                                    <svg class="size-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.375l1.091 1.09a4 4 0 00-5.557-5.557z" clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ $t('responses.hidden') }}</span>
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="inline-flex min-h-[44px] items-center gap-1.5 rounded-lg border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 hover:border-stone-300 transition-colors cursor-pointer"
                                    @click="moderate(wish, !wish.is_hidden)"
                                >
                                    <svg v-if="wish.is_hidden" class="size-3.5 text-stone-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    <svg v-else class="size-3.5 text-stone-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.375l1.091 1.09a4 4 0 00-5.557-5.557z" clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ wish.is_hidden ? $t('responses.show') : $t('responses.hide') }}</span>
                                </button>

                                <button
                                    type="button"
                                    class="inline-flex min-h-[44px] items-center gap-1 text-xs font-medium text-rose-700 hover:text-rose-900 hover:underline px-2 transition-colors cursor-pointer"
                                    @click="removeWish(wish)"
                                >
                                    <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ $t('common.delete') }}</span>
                                </button>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
