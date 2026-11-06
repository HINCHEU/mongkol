<script setup>
import { useForm } from '@inertiajs/vue3';
import { useAutosave } from '../../autosave.js';

const props = defineProps({ invitation: Object, presets: Array, themes: Array, lunarDate: String });

const form = useForm({ theme_id: props.invitation.theme_id });
useAutosave(form, `/app/invitations/${props.invitation.id}/theme`);
</script>

<template>
    <fieldset class="grid gap-3">
        <legend class="font-medium">Theme</legend>
        <p class="text-sm text-gray-600">Switch any time: everything you entered stays.</p>
        <label v-for="theme in themes" :key="theme.id" class="flex items-center gap-3 rounded-md border border-gray-200 bg-white p-4">
            <input v-model="form.theme_id" type="radio" :value="theme.id" />
            <span class="flex-1">{{ theme.name }} <span v-if="theme.tier === 'premium'" class="text-sm text-amber-800">Premium</span></span>
            <a :href="`/themes/${theme.slug}`" target="_blank" class="text-sm text-blue-700 underline">Demo</a>
        </label>
        <p v-if="form.errors.theme_id" class="text-sm text-red-700">{{ form.errors.theme_id }}</p>
    </fieldset>
</template>
