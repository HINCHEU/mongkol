<script setup>
import { computed, ref, watch } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import Field from '../../Components/Field.vue';
import { t } from '../../i18n.js';

// Plain, functional markup; styling is left to the design pass.
const props = defineProps({
    invitation: Object,
    guests: Array,
    titles: Array,
    suffixes: Array,
    max: Number,
    preview: Array,
    errors: Object,
});

const base = `/app/invitations/${props.invitation.id}`;
const keep = { preserveScroll: true, preserveState: true };
const blank = () => ({ title: '', name: '', suffix: '', side: '', group: '', phone: '' });

// Add one guest.
const form = useForm(blank());
const add = () => form.post(`${base}/guests`, { ...keep, onSuccess: () => form.reset() });

// Edit one row at a time.
const editing = ref(null);
const editForm = useForm(blank());
function edit(guest) {
    editing.value = guest.id;
    Object.assign(editForm, { title: guest.title ?? '', name: guest.name, suffix: guest.suffix ?? '', side: guest.side ?? '', group: guest.group ?? '', phone: guest.phone ?? '' });
}
const saveEdit = (guest) => editForm.put(`${base}/guests/${guest.id}`, { ...keep, onSuccess: () => (editing.value = null) });
const remove = (guest) => confirm(t('guests.confirm_delete', { name: guest.name })) && router.delete(`${base}/guests/${guest.id}`, keep);
const toggleSent = (guest, sent) => router.put(`${base}/guests/${guest.id}/sent`, { sent }, keep);

// Bulk: paste or a file → preview (server-parsed) → import.
const pasted = ref('');
const rows = ref(null);
watch(() => props.preview, (value) => { if (value) rows.value = value.map((r) => ({ ...r })); }, { immediate: true });
const previewText = () => router.post(`${base}/guests/preview`, { text: pasted.value }, keep);
function previewFile(input) {
    if (input.files[0]) router.post(`${base}/guests/preview`, { file: input.files[0] }, { ...keep, forceFormData: true });
    input.value = '';
}
const importRows = () => router.post(`${base}/guests/import`, { rows: rows.value }, {
    ...keep,
    onSuccess: () => { rows.value = null; pasted.value = ''; },
});
const rowErrors = computed(() => Object.entries(props.errors ?? {}).filter(([key]) => key.startsWith('rows')));

const search = ref('');
const shown = computed(() => {
    const q = search.value.trim().toLowerCase();
    return q ? props.guests.filter((g) => [g.title, g.name, g.suffix, g.group, g.phone].join(' ').toLowerCase().includes(q)) : props.guests;
});

const copiedCode = ref(null);
const copy = (text, code) => {
    navigator.clipboard?.writeText(text);
    if (code) {
        copiedCode.value = code;
        setTimeout(() => { if (copiedCode.value === code) copiedCode.value = null; }, 1500);
    }
};
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
                            <h1 class="text-xl font-bold tracking-tight text-stone-900">{{ $t('editor.guests') }}</h1>
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-900">
                                {{ guests.length }} / {{ max }}
                            </span>
                        </div>
                        <p class="text-xs text-stone-500 font-kantumruy">{{ $t('guests.subtitle') }}</p>
                    </div>
                </div>

                <!-- Action Shortcuts -->
                <div class="flex flex-wrap items-center gap-2">
                    <a
                        :href="`${base}/guests/export`"
                        class="btn-secondary !py-2 !px-3.5 !text-xs"
                        :title="$t('guests.export')"
                    >
                        <svg class="size-3.5 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="7 10 12 15 17 10" />
                            <line x1="12" y1="15" x2="12" y2="3" />
                        </svg>
                        <span>{{ $t('guests.export') }}</span>
                    </a>
                    <Link
                        :href="`${base}/share`"
                        class="btn-primary !py-2 !px-3.5 !text-xs"
                    >
                        <span>{{ $t('guests.share_links') }} →</span>
                    </Link>
                </div>
            </div>

            <!-- Error Banner -->
            <div v-if="$page.props.flash?.error" role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 flex items-center gap-3">
                <svg class="size-5 text-red-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span>{{ $page.props.flash.error }}</span>
            </div>

            <!-- Add Single Guest Card -->
            <div class="card p-6">
                <div class="mb-4 border-b border-stone-100 pb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="flex size-8 items-center justify-center rounded-lg bg-amber-100 text-amber-800">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="8.5" cy="7" r="4" />
                                <line x1="20" y1="8" x2="20" y2="14" />
                                <line x1="23" y1="11" x2="17" y2="11" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-stone-900">{{ $t('guests.add_one') }}</h2>
                            <p class="text-xs text-stone-500 font-kantumruy">{{ $t('guests.add_one_hint') }}</p>
                        </div>
                    </div>
                </div>

                <form class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" @submit.prevent="add">
                    <Field :label="$t('guests.title')" :error="form.errors.title">
                        <select v-model="form.title" class="input font-kantumruy">
                            <option value="">— {{ $t('guests.no_title') }}</option>
                            <option v-for="t in titles" :key="t" :value="t">{{ t }}</option>
                        </select>
                    </Field>

                    <Field :label="`${$t('guests.name')} *`" :error="form.errors.name">
                        <input v-model="form.name" class="input font-kantumruy" required :placeholder="$t('create.example', { name: 'សុខ សំណាង' })" />
                    </Field>

                    <Field :label="$t('guests.suffix')" :error="form.errors.suffix">
                        <input v-model="form.suffix" class="input font-kantumruy" list="guest-suffixes" :placeholder="$t('create.example', { name: 'និងភរិយា' })" />
                    </Field>

                    <Field :label="$t('guests.side')" :error="form.errors.side">
                        <select v-model="form.side" class="input font-kantumruy">
                            <option value="">— {{ $t('guests.no_side') }}</option>
                            <option value="groom">{{ $t('guests.sides.groom') }}</option>
                            <option value="bride">{{ $t('guests.sides.bride') }}</option>
                        </select>
                    </Field>

                    <Field :label="$t('guests.group')" :error="form.errors.group">
                        <input v-model="form.group" class="input" :placeholder="$t('guests.group_placeholder')" />
                    </Field>

                    <Field :label="$t('guests.phone')" :error="form.errors.phone">
                        <input v-model="form.phone" class="input font-mono" inputmode="tel" :placeholder="$t('create.example', { name: '012 345 678' })" />
                    </Field>

                    <div class="sm:col-span-2 lg:col-span-3 flex justify-end pt-2">
                        <button type="submit" class="btn-primary min-h-[44px] w-full sm:w-auto" :disabled="form.processing">
                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ $t('guests.add_button') }}</span>
                        </button>
                    </div>
                </form>
                <datalist id="guest-suffixes"><option v-for="s in suffixes" :key="s" :value="s" /></datalist>
            </div>

            <!-- Add Many (Bulk Import) Card -->
            <div class="card p-6 grid gap-4">
                <div class="border-b border-stone-100 pb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="flex size-8 items-center justify-center rounded-lg bg-stone-100 text-stone-800">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                                <line x1="16" y1="13" x2="8" y2="13" />
                                <line x1="16" y1="17" x2="8" y2="17" />
                                <polyline points="10 9 9 9 8 9" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-stone-900">{{ $t('guests.add_many') }}</h2>
                            <p class="text-xs text-stone-500 font-kantumruy">{{ $t('guests.add_many_hint') }}</p>
                        </div>
                    </div>
                </div>

                <textarea
                    v-model="pasted"
                    rows="4"
                    class="input font-kantumruy text-sm"
                    placeholder="លោក ចាន់ ថា និងភរិយា&#10;កញ្ញា សុខ គន្ធា&#10;ឯកឧត្តម ហ៊ុន វិបុល"
                ></textarea>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
                    <div class="flex flex-wrap items-center gap-3">
                        <button
                            type="button"
                            class="btn-secondary min-h-[44px]"
                            :disabled="!pasted.trim()"
                            @click="previewText"
                        >
                            <span>{{ $t('guests.preview_pasted') }}</span>
                        </button>
                        <span class="text-xs text-stone-400">{{ $t('common.or') }}</span>
                        <label class="inline-flex min-h-[44px] items-center gap-2 rounded-lg border border-stone-200 bg-stone-50 px-3.5 py-2 text-xs font-semibold text-stone-700 hover:bg-amber-50 hover:border-amber-300 hover:text-amber-900 transition-colors cursor-pointer">
                            <svg class="size-4 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                            </svg>
                            <span>{{ $t('guests.choose_file') }}</span>
                            <input
                                type="file"
                                accept=".xlsx,.csv,text/csv"
                                class="hidden"
                                @change="previewFile($event.target)"
                            />
                        </label>
                    </div>
                    <p class="text-[11px] text-stone-500 font-kantumruy">{{ $t('guests.columns') }}</p>
                </div>

                <!-- Preview Confirmation Table -->
                <div v-if="rows" class="mt-4 rounded-xl border border-amber-300 bg-amber-50/50 p-4 grid gap-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-amber-950 flex items-center gap-2">
                            <span class="flex size-5 items-center justify-center rounded-full bg-amber-200 text-amber-900 text-xs">✓</span>
                            <span>{{ $t('guests.check', { count: rows.length }) }}</span>
                        </h3>
                    </div>

                    <div v-if="rowErrors.length" class="space-y-1">
                        <p v-for="[key, message] in rowErrors" :key="key" class="text-xs text-red-600 font-medium">
                            {{ $t('guests.row', { n: Number(key.split('.')[1]) + 1 || '' }) }}: {{ message }}
                        </p>
                    </div>

                    <div class="max-h-96 overflow-x-auto rounded-lg border border-amber-200 bg-white shadow-xs">
                        <table class="w-full text-xs">
                            <thead class="bg-stone-50 border-b border-stone-200 text-stone-600 font-semibold uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="py-2.5 px-3 text-left">{{ $t('guests.title') }}</th>
                                    <th class="py-2.5 px-3 text-left">{{ $t('guests.name') }}</th>
                                    <th class="py-2.5 px-3 text-left">{{ $t('guests.suffix') }}</th>
                                    <th class="py-2.5 px-3 text-left">{{ $t('guests.side') }}</th>
                                    <th class="py-2.5 px-3 text-left">{{ $t('guests.group') }}</th>
                                    <th class="py-2.5 px-3 text-right"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                <tr v-for="(row, i) in rows" :key="i" class="hover:bg-amber-50/30">
                                    <td class="p-2"><input v-model="row.title" class="input !py-1.5 !px-2 text-xs" list="guest-titles" /></td>
                                    <td class="p-2"><input v-model="row.name" class="input !py-1.5 !px-2 text-xs font-semibold" /></td>
                                    <td class="p-2"><input v-model="row.suffix" class="input !py-1.5 !px-2 text-xs" list="guest-suffixes" /></td>
                                    <td class="p-2">
                                        <select v-model="row.side" class="input !py-1.5 !px-2 text-xs">
                                            <option :value="null">—</option>
                                            <option value="groom">{{ $t('guests.sides.groom') }}</option>
                                            <option value="bride">{{ $t('guests.sides.bride') }}</option>
                                        </select>
                                    </td>
                                    <td class="p-2"><input v-model="row.group" class="input !py-1.5 !px-2 text-xs" /></td>
                                    <td class="p-2 text-right">
                                        <button
                                            type="button"
                                            class="inline-flex size-7 items-center justify-center rounded text-red-700 hover:bg-red-50 hover:text-red-900 transition-colors cursor-pointer"
                                            :title="$t('guests.drop_row')"
                                            @click="rows.splice(i, 1)"
                                        >
                                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <datalist id="guest-titles"><option v-for="t in titles" :key="t" :value="t" /></datalist>

                    <div class="flex flex-wrap gap-3 pt-2">
                        <button
                            type="button"
                            class="btn-primary min-h-[44px]"
                            :disabled="!rows.length"
                            @click="importRows"
                        >
                            <span>{{ $t('guests.import', { count: rows.length }) }}</span>
                        </button>
                        <button
                            type="button"
                            class="btn-secondary min-h-[44px]"
                            @click="rows = null"
                        >
                            <span>{{ $t('common.cancel') }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- The Guest List Section -->
            <div class="card p-6 grid gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-stone-100 pb-4">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-bold text-stone-900">{{ $t('guests.directory') }}</h2>
                        <span class="rounded-full bg-stone-100 px-2.5 py-0.5 text-xs font-bold text-stone-700">
                            {{ $t('guests.shown', { count: shown.length }) }}
                        </span>
                    </div>

                    <!-- Search Input -->
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
                </div>

                <!-- Empty State -->
                <div v-if="!guests.length" class="text-center py-12 rounded-xl border border-dashed border-stone-200 bg-stone-50/50">
                    <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-amber-100 text-amber-800 mb-3">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-stone-800">{{ $t('guests.empty') }}</p>
                    <p class="text-xs text-stone-500 mt-1 font-kantumruy">{{ $t('guests.empty_hint') }}</p>
                </div>

                <!-- Filtered Zero State -->
                <div v-else-if="!shown.length" class="text-center py-8 text-stone-500 text-sm">
                    <p>{{ $t('guests.no_match') }} “<span class="font-semibold text-stone-800">{{ search }}</span>”</p>
                    <button type="button" class="mt-2 text-xs text-amber-800 font-semibold underline" @click="search = ''">{{ $t('guests.clear_search') }}</button>
                </div>

                <!-- 1. MOBILE RESPONSIVE CARDS VIEW (Phone first: stacked cards for 300+ guests) -->
                <div v-else class="grid gap-3 md:hidden">
                    <template v-for="guest in shown" :key="guest.id">
                        <!-- Inline Edit Form (Mobile) -->
                        <div v-if="editing === guest.id" class="rounded-xl border-2 border-amber-400 bg-amber-50/40 p-4 shadow-sm">
                            <div class="mb-3 flex items-center justify-between border-b border-amber-200/60 pb-2">
                                <span class="text-xs font-bold text-amber-950">{{ $t('guests.editing', { name: guest.name }) }}</span>
                                <button type="button" class="text-xs text-stone-500 hover:text-stone-800" @click="editing = null">✕</button>
                            </div>
                            <form class="grid gap-3" @submit.prevent="saveEdit(guest)">
                                <select v-model="editForm.title" class="input font-kantumruy">
                                    <option value="">— {{ $t('guests.no_title') }}</option>
                                    <option v-for="t in titles" :key="t" :value="t">{{ t }}</option>
                                </select>
                                <input v-model="editForm.name" class="input font-kantumruy font-semibold" required :placeholder="$t('guests.name')" />
                                <input v-model="editForm.suffix" class="input font-kantumruy" list="guest-suffixes" :placeholder="$t('guests.suffix')" />
                                <select v-model="editForm.side" class="input font-kantumruy">
                                    <option value="">— {{ $t('guests.no_side') }}</option>
                                    <option value="groom">{{ $t('guests.sides.groom') }}</option>
                                    <option value="bride">{{ $t('guests.sides.bride') }}</option>
                                </select>
                                <input v-model="editForm.group" class="input" :placeholder="$t('guests.group')" />
                                <input v-model="editForm.phone" class="input font-mono" :placeholder="$t('guests.phone')" />
                                <p v-for="(message, key) in editForm.errors" :key="key" class="text-xs text-red-600">{{ message }}</p>
                                <div class="flex gap-2 pt-2">
                                    <button type="submit" class="btn-primary flex-1 min-h-[44px]">{{ $t('common.save') }}</button>
                                    <button type="button" class="btn-secondary min-h-[44px]" @click="editing = null">{{ $t('common.cancel') }}</button>
                                </div>
                            </form>
                        </div>

                        <!-- Guest Stacked Card (Mobile) -->
                        <div v-else class="rounded-xl border border-stone-200/90 bg-white p-4 shadow-xs transition-shadow hover:shadow-sm">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="font-bold text-stone-900 text-sm font-kantumruy" lang="km">
                                        {{ [guest.title, guest.name, guest.suffix].filter(Boolean).join(' ') }}
                                    </p>
                                    <div class="mt-1 flex flex-wrap items-center gap-1.5 text-xs text-stone-500">
                                        <span v-if="guest.side" class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider" :class="guest.side === 'groom' ? 'bg-sky-50 text-sky-800 border border-sky-200' : 'bg-rose-50 text-rose-800 border border-rose-200'">
                                            {{ $t(`guests.sides.${guest.side}`) }}
                                        </span>
                                        <span v-if="guest.group" class="rounded-md bg-stone-100 px-1.5 py-0.5 text-[11px] text-stone-700">
                                            {{ guest.group }}
                                        </span>
                                        <span v-if="guest.phone" class="font-mono text-[11px] text-stone-600">
                                            {{ guest.phone }}
                                        </span>
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

                            <div class="mt-3 flex items-center justify-between border-t border-stone-100 pt-3">
                                <!-- Link Code & Opens Counter -->
                                <div class="flex items-center gap-3">
                                    <button
                                        type="button"
                                        class="inline-flex min-h-[44px] items-center gap-1 rounded-lg border border-stone-200 bg-stone-50 px-2.5 py-1 text-xs font-mono font-medium text-stone-700 hover:bg-amber-50 hover:border-amber-300 transition-colors cursor-pointer"
                                        @click="copy(guest.url, guest.code)"
                                        :title="$t('guests.copy_link')"
                                    >
                                        <span>{{ copiedCode === guest.code ? $t('common.copied') : guest.code }}</span>
                                    </button>
                                    <span class="inline-flex items-center gap-1 text-xs text-stone-500" :title="$t('guests.opens')">
                                        <svg class="size-3.5 text-stone-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                        <span>{{ guest.open_count }}</span>
                                    </span>
                                </div>

                                <!-- Actions Toolbar -->
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        class="min-h-[44px] px-2 text-xs font-semibold text-stone-700 hover:text-stone-900 underline cursor-pointer"
                                        @click="edit(guest)"
                                    >
                                        {{ $t('common.edit') }}
                                    </button>
                                    <button
                                        type="button"
                                        class="min-h-[44px] px-2 text-xs font-semibold text-red-700 hover:text-red-900 underline cursor-pointer"
                                        @click="remove(guest)"
                                    >
                                        {{ $t('common.delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- 2. DESKTOP TABLE VIEW (Clean tabular layout for large screen) -->
                <div v-if="shown.length" class="hidden md:block overflow-x-auto rounded-xl border border-stone-200">
                    <table class="w-full text-sm">
                        <thead class="bg-stone-50/80 border-b border-stone-200 text-stone-600 font-semibold text-xs">
                            <tr>
                                <th class="py-3 px-4 text-left">{{ $t('guests.guest') }}</th>
                                <th class="py-3 px-4 text-left">{{ $t('guests.side_group') }}</th>
                                <th class="py-3 px-4 text-left">{{ $t('guests.link') }}</th>
                                <th class="py-3 px-4 text-center">{{ $t('guests.opens') }}</th>
                                <th class="py-3 px-4 text-center">{{ $t('guests.sent') }}</th>
                                <th class="py-3 px-4 text-right">{{ $t('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            <template v-for="guest in shown" :key="guest.id">
                                <!-- Inline Edit Row (Desktop) -->
                                <tr v-if="editing === guest.id" class="bg-amber-50/40">
                                    <td colspan="6" class="p-4">
                                        <div class="mb-2 flex items-center justify-between border-b border-amber-200/60 pb-1.5">
                                            <span class="text-xs font-bold text-amber-950">{{ $t('guests.editing', { name: guest.name }) }}</span>
                                            <button type="button" class="text-xs text-stone-500 hover:text-stone-800" @click="editing = null">✕ {{ $t('common.close') }}</button>
                                        </div>
                                        <form class="grid gap-3 sm:grid-cols-3" @submit.prevent="saveEdit(guest)">
                                            <select v-model="editForm.title" class="input font-kantumruy">
                                                <option value="">— {{ $t('guests.no_title') }}</option>
                                                <option v-for="t in titles" :key="t" :value="t">{{ t }}</option>
                                            </select>
                                            <input v-model="editForm.name" class="input font-kantumruy font-semibold" required :placeholder="$t('guests.name')" />
                                            <input v-model="editForm.suffix" class="input font-kantumruy" list="guest-suffixes" :placeholder="$t('guests.suffix')" />
                                            <select v-model="editForm.side" class="input font-kantumruy">
                                                <option value="">— {{ $t('guests.no_side') }}</option>
                                                <option value="groom">{{ $t('guests.sides.groom') }}</option>
                                                <option value="bride">{{ $t('guests.sides.bride') }}</option>
                                            </select>
                                            <input v-model="editForm.group" class="input" :placeholder="$t('guests.group')" />
                                            <input v-model="editForm.phone" class="input font-mono" :placeholder="$t('guests.phone')" />
                                            <p v-for="(message, key) in editForm.errors" :key="key" class="text-xs text-red-600 sm:col-span-3">{{ message }}</p>
                                            <div class="sm:col-span-3 flex justify-end gap-2 pt-1">
                                                <button type="button" class="btn-secondary !py-2 !px-4" @click="editing = null">{{ $t('common.cancel') }}</button>
                                                <button type="submit" class="btn-primary !py-2 !px-4">{{ $t('common.save') }}</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Regular Desktop Row -->
                                <tr v-else class="hover:bg-amber-50/20 transition-colors">
                                    <td class="py-3 px-4 font-kantumruy font-semibold text-stone-900" lang="km">
                                        {{ [guest.title, guest.name, guest.suffix].filter(Boolean).join(' ') }}
                                    </td>
                                    <td class="py-3 px-4 text-xs text-stone-600">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span v-if="guest.side" class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider" :class="guest.side === 'groom' ? 'bg-sky-50 text-sky-800 border border-sky-200' : 'bg-rose-50 text-rose-800 border border-rose-200'">
                                                {{ $t(`guests.sides.${guest.side}`) }}
                                            </span>
                                            <span v-if="guest.group" class="rounded-md bg-stone-100 px-1.5 py-0.5 text-[11px] text-stone-700">
                                                {{ guest.group }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 rounded-md border border-stone-200 bg-stone-50 px-2 py-1 font-mono text-xs text-stone-700 hover:bg-amber-50 hover:border-amber-300 transition-colors cursor-pointer"
                                            @click="copy(guest.url, guest.code)"
                                            :title="$t('guests.copy_link')"
                                        >
                                            <span>{{ copiedCode === guest.code ? $t('common.copied') : guest.code }}</span>
                                        </button>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-stone-100 px-2 py-0.5 text-xs font-medium text-stone-700">
                                            <svg class="size-3 text-stone-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span>{{ guest.open_count }}</span>
                                        </span>
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
                                    <td class="py-3 px-4 text-right whitespace-nowrap text-xs font-semibold">
                                        <button
                                            type="button"
                                            class="text-stone-700 hover:text-stone-900 hover:underline px-1 py-1 cursor-pointer"
                                            @click="edit(guest)"
                                        >
                                            {{ $t('common.edit') }}
                                        </button>
                                        <span class="text-stone-300 mx-1">·</span>
                                        <button
                                            type="button"
                                            class="text-red-700 hover:text-red-900 hover:underline px-1 py-1 cursor-pointer"
                                            @click="remove(guest)"
                                        >
                                            {{ $t('common.delete') }}
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
