<script setup>
import { useForm } from '@inertiajs/vue3';
import Field from '../Field.vue';
import { clone, useAutosave } from '../../autosave.js';

const props = defineProps({ invitation: Object, presets: Array, themes: Array, lunarDate: String });

const form = useForm({ people: clone(props.invitation.people) });
useAutosave(form, `/app/invitations/${props.invitation.id}/couple`);

const groups = [
    { title: 'The couple', roles: { groom: 'Groom', bride: 'Bride' } },
    { title: "Groom's parents", roles: { groom_father: 'Father', groom_mother: 'Mother' } },
    { title: "Bride's parents", roles: { bride_father: 'Father', bride_mother: 'Mother' } },
];
const titles = ['លោក', 'លោកស្រី', 'កញ្ញា', 'ឯកឧត្តម', 'លោកជំទាវ', 'អ្នកឧកញ៉ា'];
const isParent = (role) => role.includes('_');
</script>

<template>
    <div class="grid gap-8">
        <datalist id="titles"><option v-for="t in titles" :key="t" :value="t" /></datalist>

        <fieldset v-for="group in groups" :key="group.title" class="grid gap-4">
            <legend class="font-medium">{{ group.title }}</legend>
            <div v-for="(label, role) in group.roles" :key="role" class="grid gap-3 rounded-md border border-gray-200 bg-white p-4">
                <p class="text-sm font-medium">{{ label }}</p>
                <Field v-if="isParent(role)" label="Title" :error="form.errors[`people.${role}.title`]">
                    <input v-model="form.people[role].title" list="titles" lang="km" class="input" />
                </Field>
                <Field label="Name in Khmer" :error="form.errors[`people.${role}.name_km`]">
                    <input v-model="form.people[role].name_km" lang="km" class="input" />
                </Field>
                <Field label="Name in Latin letters" :error="form.errors[`people.${role}.name_en`]">
                    <input v-model="form.people[role].name_en" class="input" />
                </Field>
                <label v-if="isParent(role)" class="flex items-center gap-2 text-sm">
                    <input v-model="form.people[role].is_late" type="checkbox" /> Passed away
                </label>
            </div>
        </fieldset>
        <p class="text-sm text-gray-600">Leave a parent's Khmer name empty to leave them off the invitation.</p>
    </div>
</template>
