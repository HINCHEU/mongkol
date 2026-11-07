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
        <div class="grid gap-6">
            <div class="flex flex-wrap items-center gap-3">
                <Link :href="`${base}/edit`" class="text-sm underline">← {{ $t('common.back_to_editor') }}</Link>
                <h1 class="text-xl font-bold">{{ $t('editor.responses') }}</h1>
                <a :href="`${base}/responses/export`" class="text-sm underline">{{ $t('guests.export') }}</a>
            </div>

            <!-- On / off -->
            <div class="card p-5 grid gap-2">
                <label class="flex items-center gap-2 text-sm">
                    <input v-model="settings.rsvp_enabled" type="checkbox" @change="saveSettings" />
                    {{ $t('responses.rsvp_enabled') }}
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input v-model="settings.wishes_enabled" type="checkbox" @change="saveSettings" />
                    {{ $t('responses.wishes_enabled') }}
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input v-model="settings.wishes_need_approval" type="checkbox" :disabled="!settings.wishes_enabled" @change="saveSettings" />
                    {{ $t('responses.wishes_need_approval') }}
                </label>
            </div>

            <!-- Totals for table planning -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="card p-4"><p class="text-2xl font-bold">{{ totals.people }}</p><p class="text-xs">{{ $t('responses.people') }}</p></div>
                <div class="card p-4"><p class="text-2xl font-bold">{{ totals.attending }}</p><p class="text-xs">{{ $t('responses.attending') }}</p></div>
                <div class="card p-4"><p class="text-2xl font-bold">{{ totals.declined }}</p><p class="text-xs">{{ $t('responses.declined') }}</p></div>
                <div class="card p-4"><p class="text-2xl font-bold">{{ totals.waiting }}</p><p class="text-xs">{{ $t('responses.waiting') }}</p></div>
            </div>

            <!-- Replies -->
            <div class="card p-5 grid gap-3">
                <h2 class="font-bold">{{ $t('responses.replies', { count: totals.replies }) }}</h2>
                <p v-if="!rsvps.length" class="text-sm text-stone-600">{{ $t('responses.no_replies') }}</p>
                <ul v-else class="grid gap-2">
                    <li v-for="rsvp in rsvps" :key="rsvp.id" class="border-b border-stone-100 pb-2 text-sm">
                        <div class="flex flex-wrap items-center gap-2">
                            <strong lang="km">{{ rsvp.name }}</strong>
                            <span>{{ rsvp.attending ? $t('responses.coming', { count: rsvp.party_size }) : $t('responses.not_coming') }}</span>
                            <span v-if="!rsvp.personal" class="text-xs text-stone-500">({{ $t('responses.generic_link') }})</span>
                            <span class="text-xs text-stone-500">{{ when(rsvp.at) }}</span>
                            <button v-if="!rsvp.personal" type="button" class="text-xs text-red-700 underline" @click="removeRsvp(rsvp)">{{ $t('common.delete') }}</button>
                        </div>
                        <p v-if="rsvp.message" class="mt-1 text-stone-700">“{{ rsvp.message }}”</p>
                    </li>
                </ul>
            </div>

            <!-- Guest book -->
            <div class="card p-5 grid gap-3">
                <h2 class="font-bold">{{ $t('responses.wishes', { count: wishes.length }) }}</h2>
                <p v-if="!wishes.length" class="text-sm text-stone-600">{{ $t('responses.no_wishes') }}</p>
                <ul v-else class="grid gap-2">
                    <li v-for="wish in wishes" :key="wish.id" class="border-b border-stone-100 pb-2 text-sm" :class="wish.is_hidden && 'opacity-60'">
                        <p>{{ wish.message }}</p>
                        <div class="mt-1 flex flex-wrap items-center gap-2 text-xs">
                            <span>— {{ wish.name }}</span>
                            <span class="text-stone-500">{{ when(wish.at) }}</span>
                            <span v-if="wish.is_hidden" class="font-semibold">{{ $t('responses.hidden') }}</span>
                            <button type="button" class="underline" @click="moderate(wish, !wish.is_hidden)">
                                {{ wish.is_hidden ? $t('responses.show') : $t('responses.hide') }}
                            </button>
                            <button type="button" class="underline text-red-700" @click="removeWish(wish)">{{ $t('common.delete') }}</button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
