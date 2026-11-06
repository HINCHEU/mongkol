<script setup>
import { useForm } from '@inertiajs/vue3';
import Field from '../Field.vue';
import { clone, copyIds, useAutosave } from '../../autosave.js';

const props = defineProps({ invitation: Object, presets: Array, themes: Array, lunarDate: String });

const form = useForm({ venues: clone(props.invitation.venues) });
useAutosave(form, `/app/invitations/${props.invitation.id}/venues`, {
    afterSave: (page) => copyIds(form.venues, page.props.invitation.venues),
});

const add = () => form.venues.push({ id: null, name: '', address: '', maps_url: '' });
</script>

<template>
    <div class="grid gap-4">
        <p class="text-sm text-gray-600">Where the ceremonies happen. Paste the link from Google Maps' Share button.</p>

        <div v-for="(venue, i) in form.venues" :key="i" class="grid gap-3 rounded-md border border-gray-200 bg-white p-4">
            <Field label="Name" :error="form.errors[`venues.${i}.name`]">
                <input v-model="venue.name" lang="km" class="input" />
            </Field>
            <Field label="Address" :error="form.errors[`venues.${i}.address`]">
                <input v-model="venue.address" lang="km" class="input" />
            </Field>
            <Field label="Google Maps link" :error="form.errors[`venues.${i}.maps_url`]">
                <input v-model="venue.maps_url" type="url" inputmode="url" class="input" placeholder="https://maps.app.goo.gl/…" />
            </Field>
            <button type="button" class="justify-self-start text-sm text-red-700 underline" @click="form.venues.splice(i, 1)">Remove venue</button>
        </div>

        <button v-if="form.venues.length < 5" type="button" class="justify-self-start rounded-md border border-gray-300 px-3 py-2" @click="add">
            Add venue
        </button>
    </div>
</template>
