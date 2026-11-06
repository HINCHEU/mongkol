<script setup>
import { useForm } from '@inertiajs/vue3';
import Field from '../Field.vue';
import { clone, useAutosave } from '../../autosave.js';

const props = defineProps({ invitation: Object, presets: Array, themes: Array, lunarDate: String });

const form = useForm(clone(props.invitation.content));
useAutosave(form, `/app/invitations/${props.invitation.id}/wording`);

const blocks = { opening: 'Opening words', closing: 'Closing words', thank_you: 'Thank-you line' };
</script>

<template>
    <div class="grid gap-6">
        <Field label="Language guests see first" :error="form.errors.locale">
            <select v-model="form.locale" class="input">
                <option value="km">ខ្មែរ</option>
                <option value="en">English</option>
            </select>
        </Field>

        <fieldset v-for="(label, key) in blocks" :key="key" class="grid gap-3">
            <legend class="font-medium">{{ label }}</legend>
            <p class="text-sm text-gray-600">Leave empty to use the theme's standard wording.</p>
            <Field label="Khmer" :error="form.errors[`${key}.km`]">
                <textarea v-model="form[key].km" lang="km" rows="3" class="input"></textarea>
            </Field>
            <Field label="English" :error="form.errors[`${key}.en`]">
                <textarea v-model="form[key].en" rows="3" class="input"></textarea>
            </Field>
        </fieldset>

        <Field label="Lunar date line (leave empty to calculate it)" :error="form.errors.lunar_date">
            <input v-model="form.lunar_date" lang="km" class="input" :placeholder="lunarDate" />
        </Field>
    </div>
</template>
