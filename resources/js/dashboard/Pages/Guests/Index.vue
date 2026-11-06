<script setup>
import { computed, ref, watch } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import Field from '../../Components/Field.vue';

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
const remove = (guest) => confirm(`Delete ${guest.name}? Their link stops working.`) && router.delete(`${base}/guests/${guest.id}`, keep);
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

const copy = (text) => navigator.clipboard?.writeText(text);
</script>

<template>
    <AppLayout>
        <div class="grid gap-6">
            <div class="flex flex-wrap items-center gap-3">
                <Link :href="`${base}/edit`" class="text-sm underline">← Editor</Link>
                <h1 class="text-xl font-bold">Guests · ភ្ញៀវ ({{ guests.length }} / {{ max }})</h1>
                <Link :href="`${base}/share`" class="text-sm underline">Share links →</Link>
                <a :href="`${base}/guests/export`" class="text-sm underline">Export Excel</a>
            </div>

            <p v-if="$page.props.flash?.error" role="alert" class="text-sm text-red-600">{{ $page.props.flash.error }}</p>

            <!-- Add one -->
            <form class="card p-5 grid gap-3 sm:grid-cols-3" @submit.prevent="add">
                <Field label="Title (គោរមងារ)" :error="form.errors.title">
                    <select v-model="form.title" class="input">
                        <option value="">—</option>
                        <option v-for="t in titles" :key="t" :value="t">{{ t }}</option>
                    </select>
                </Field>
                <Field label="Name (ឈ្មោះ)" :error="form.errors.name">
                    <input v-model="form.name" class="input" required />
                </Field>
                <Field label="Suffix (បន្ថែម)" :error="form.errors.suffix">
                    <input v-model="form.suffix" class="input" list="guest-suffixes" />
                </Field>
                <Field label="Side (ខាង)" :error="form.errors.side">
                    <select v-model="form.side" class="input">
                        <option value="">—</option>
                        <option value="groom">Groom · ខាងប្រុស</option>
                        <option value="bride">Bride · ខាងស្រី</option>
                    </select>
                </Field>
                <Field label="Group (ក្រុម)" :error="form.errors.group">
                    <input v-model="form.group" class="input" />
                </Field>
                <Field label="Phone (ទូរស័ព្ទ)" :error="form.errors.phone">
                    <input v-model="form.phone" class="input" inputmode="tel" />
                </Field>
                <div><button type="submit" class="btn-primary" :disabled="form.processing">Add guest</button></div>
            </form>
            <datalist id="guest-suffixes"><option v-for="s in suffixes" :key="s" :value="s" /></datalist>

            <!-- Many at once -->
            <div class="card p-5 grid gap-3">
                <h2 class="font-bold">Add many · បន្ថែមច្រើន</h2>
                <p class="text-xs text-stone-600">One name per line. A title or suffix written with the name (លោក ចាន់ ថា និងភរិយា) is split out for you.</p>
                <textarea v-model="pasted" rows="6" class="input" placeholder="លោក ចាន់ ថា និងភរិយា"></textarea>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" class="btn-secondary" :disabled="!pasted.trim()" @click="previewText">Preview pasted names</button>
                    <label class="text-sm">or Excel / CSV:
                        <input type="file" accept=".xlsx,.csv,text/csv" @change="previewFile($event.target)" />
                    </label>
                </div>
                <p class="text-xs text-stone-600">Sheet columns: name, title, suffix, side, group, phone (as in the export). Without headers, the first column is the name.</p>

                <div v-if="rows" class="grid gap-2">
                    <h3 class="font-semibold">Check {{ rows.length }} guests before importing</h3>
                    <p v-for="[key, message] in rowErrors" :key="key" class="text-xs text-red-600">Row {{ Number(key.split('.')[1]) + 1 || '' }}: {{ message }}</p>
                    <div class="max-h-96 overflow-auto">
                        <table class="w-full text-sm">
                            <thead><tr><th class="text-left">Title</th><th class="text-left">Name</th><th class="text-left">Suffix</th><th class="text-left">Side</th><th class="text-left">Group</th><th></th></tr></thead>
                            <tbody>
                                <tr v-for="(row, i) in rows" :key="i">
                                    <td><input v-model="row.title" class="input" list="guest-titles" /></td>
                                    <td><input v-model="row.name" class="input" /></td>
                                    <td><input v-model="row.suffix" class="input" list="guest-suffixes" /></td>
                                    <td>
                                        <select v-model="row.side" class="input">
                                            <option :value="null">—</option>
                                            <option value="groom">Groom</option>
                                            <option value="bride">Bride</option>
                                        </select>
                                    </td>
                                    <td><input v-model="row.group" class="input" /></td>
                                    <td><button type="button" class="text-red-700 underline text-xs" @click="rows.splice(i, 1)">Drop</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <datalist id="guest-titles"><option v-for="t in titles" :key="t" :value="t" /></datalist>
                    <div class="flex gap-3">
                        <button type="button" class="btn-primary" :disabled="!rows.length" @click="importRows">Import {{ rows.length }} guests</button>
                        <button type="button" class="btn-secondary" @click="rows = null">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- The list -->
            <div class="card p-5 grid gap-3">
                <input v-model="search" class="input" placeholder="Search guests…" />
                <p v-if="!guests.length" class="text-sm text-stone-600">No guests yet.</p>
                <table v-else class="w-full text-sm">
                    <thead>
                        <tr><th class="text-left">Guest</th><th class="text-left">Side / group</th><th class="text-left">Link</th><th>Opens</th><th>Sent</th><th></th></tr>
                    </thead>
                    <tbody>
                        <template v-for="guest in shown" :key="guest.id">
                            <tr v-if="editing === guest.id">
                                <td colspan="6">
                                    <form class="grid gap-2 sm:grid-cols-3 py-2" @submit.prevent="saveEdit(guest)">
                                        <select v-model="editForm.title" class="input">
                                            <option value="">—</option>
                                            <option v-for="t in titles" :key="t" :value="t">{{ t }}</option>
                                        </select>
                                        <input v-model="editForm.name" class="input" required />
                                        <input v-model="editForm.suffix" class="input" list="guest-suffixes" />
                                        <select v-model="editForm.side" class="input">
                                            <option value="">—</option>
                                            <option value="groom">Groom</option>
                                            <option value="bride">Bride</option>
                                        </select>
                                        <input v-model="editForm.group" class="input" placeholder="Group" />
                                        <input v-model="editForm.phone" class="input" placeholder="Phone" />
                                        <p v-for="(message, key) in editForm.errors" :key="key" class="text-xs text-red-600">{{ message }}</p>
                                        <div class="flex gap-2">
                                            <button type="submit" class="btn-primary">Save</button>
                                            <button type="button" class="btn-secondary" @click="editing = null">Cancel</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            <tr v-else>
                                <td lang="km">{{ [guest.title, guest.name, guest.suffix].filter(Boolean).join(' ') }}</td>
                                <td>{{ guest.side ?? '' }} {{ guest.group ? `· ${guest.group}` : '' }}</td>
                                <td><button type="button" class="font-mono text-xs underline" @click="copy(guest.url)" title="Copy link">{{ guest.code }}</button></td>
                                <td class="text-center">{{ guest.open_count }}</td>
                                <td class="text-center"><input type="checkbox" :checked="guest.sent" @change="toggleSent(guest, $event.target.checked)" aria-label="Sent" /></td>
                                <td class="whitespace-nowrap text-xs">
                                    <button type="button" class="underline" @click="edit(guest)">Edit</button>
                                    ·
                                    <button type="button" class="underline text-red-700" @click="remove(guest)">Delete</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
