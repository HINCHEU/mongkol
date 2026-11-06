<script setup>
import { useForm } from '@inertiajs/vue3';
import Field from '../Field.vue';
import { clone, copyIds, moveRow, useAutosave } from '../../autosave.js';

const props = defineProps({ invitation: Object, presets: Array, themes: Array, lunarDate: String });

const form = useForm({
    wedding_date: props.invitation.wedding_date,
    events: clone(props.invitation.events),
});
useAutosave(form, `/app/invitations/${props.invitation.id}/program`, {
    afterSave: (page) => copyIds(form.events, page.props.invitation.events),
});

const add = (preset = null) => form.events.push({
    id: null,
    preset_key: preset?.key ?? null,
    title_km: preset?.title_km ?? '',
    title_en: preset?.title_en ?? '',
    date: form.wedding_date,
    time: '09:00',
    venue_id: props.invitation.venues[0]?.id ?? null,
    note: '',
});
const used = (key) => form.events.some((e) => e.preset_key === key);
</script>

<template>
    <div class="grid gap-6">
        <Field label="Wedding date" :error="form.errors.wedding_date">
            <input v-model="form.wedding_date" type="date" class="input" />
        </Field>
        <p class="text-sm text-gray-600" lang="km">{{ lunarDate }}</p>

        <div class="grid gap-3">
            <p class="font-medium">Program</p>
            <p v-if="!form.events.length" class="text-sm text-gray-600">Add the ceremonies below. Times are Cambodia time.</p>

            <div v-for="(event, i) in form.events" :key="i" class="grid gap-3 rounded-md border border-gray-200 bg-white p-4">
                <div class="grid gap-3 sm:grid-cols-2">
                    <Field label="Date" :error="form.errors[`events.${i}.date`]">
                        <input v-model="event.date" type="date" class="input" />
                    </Field>
                    <Field label="Time" :error="form.errors[`events.${i}.time`]">
                        <input v-model="event.time" type="time" class="input" />
                    </Field>
                </div>
                <Field label="Title in Khmer" :error="form.errors[`events.${i}.title_km`]">
                    <input v-model="event.title_km" lang="km" class="input" />
                </Field>
                <Field label="Title in English" :error="form.errors[`events.${i}.title_en`]">
                    <input v-model="event.title_en" class="input" />
                </Field>
                <Field label="Venue" :error="form.errors[`events.${i}.venue_id`]">
                    <select v-model="event.venue_id" class="input">
                        <option :value="null">—</option>
                        <option v-for="venue in invitation.venues" :key="venue.id" :value="venue.id">{{ venue.name }}</option>
                    </select>
                </Field>
                <Field label="Note (optional)" :error="form.errors[`events.${i}.note`]">
                    <input v-model="event.note" class="input" />
                </Field>
                <div class="flex gap-3 text-sm">
                    <button type="button" class="underline disabled:opacity-40" :disabled="i === 0" @click="moveRow(form.events, i, -1)">Move up</button>
                    <button type="button" class="underline disabled:opacity-40" :disabled="i === form.events.length - 1" @click="moveRow(form.events, i, 1)">Move down</button>
                    <button type="button" class="text-red-700 underline" @click="form.events.splice(i, 1)">Remove</button>
                </div>
            </div>

            <div v-if="form.events.length < 20" class="flex flex-wrap gap-2">
                <button v-for="preset in presets" :key="preset.key" type="button" :disabled="used(preset.key)"
                        class="rounded-md border border-gray-300 px-3 py-1.5 text-sm disabled:opacity-40" lang="km" @click="add(preset)">
                    + {{ preset.title_km }}
                </button>
                <button type="button" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm" @click="add()">+ Custom item</button>
            </div>
            <p v-if="!invitation.venues.length" class="text-sm text-gray-600">Add venues first to place each ceremony.</p>
        </div>
    </div>
</template>
