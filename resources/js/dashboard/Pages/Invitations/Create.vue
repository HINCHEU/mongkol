<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';

const props = defineProps({ themes: Array });

const form = useForm({
    theme_id: props.themes[0]?.id ?? null,
    wedding_date: '',
    groom: { name_km: '', name_en: '' },
    bride: { name_km: '', name_en: '' },
});
</script>

<template>
    <AppLayout>
        <h1 class="text-2xl font-semibold">New invitation</h1>
        <p class="mt-1 text-gray-600">Your free preview starts now. Everything here can be changed later.</p>

        <form class="mt-6 grid gap-6" @submit.prevent="form.post('/app/invitations')">
            <fieldset class="grid gap-2">
                <legend class="font-medium">Theme</legend>
                <label v-for="theme in themes" :key="theme.id" class="flex items-center gap-2">
                    <input v-model="form.theme_id" type="radio" :value="theme.id" />
                    {{ theme.name }}
                    <a :href="`/themes/${theme.slug}`" target="_blank" class="text-sm text-blue-700 underline">demo</a>
                </label>
                <p v-if="form.errors.theme_id" class="text-sm text-red-700">{{ form.errors.theme_id }}</p>
            </fieldset>

            <fieldset v-for="role in ['groom', 'bride']" :key="role" class="grid gap-3">
                <legend class="font-medium capitalize">{{ role }}</legend>
                <label class="grid gap-1">
                    <span class="text-sm">Name in Khmer</span>
                    <input v-model="form[role].name_km" lang="km" class="rounded-md border border-gray-300 px-3 py-2" required />
                    <span v-if="form.errors[`${role}.name_km`]" class="text-sm text-red-700">{{ form.errors[`${role}.name_km`] }}</span>
                </label>
                <label class="grid gap-1">
                    <span class="text-sm">Name in Latin letters (used in your link)</span>
                    <input v-model="form[role].name_en" class="rounded-md border border-gray-300 px-3 py-2" placeholder="Chan Sokha" required />
                    <span v-if="form.errors[`${role}.name_en`]" class="text-sm text-red-700">{{ form.errors[`${role}.name_en`] }}</span>
                </label>
            </fieldset>

            <label class="grid gap-1">
                <span class="font-medium">Wedding date</span>
                <input v-model="form.wedding_date" type="date" class="rounded-md border border-gray-300 px-3 py-2" required />
                <span v-if="form.errors.wedding_date" class="text-sm text-red-700">{{ form.errors.wedding_date }}</span>
            </label>

            <button type="submit" class="rounded-md bg-gray-900 px-4 py-3 text-white disabled:opacity-50" :disabled="form.processing">
                Create invitation
            </button>
        </form>
    </AppLayout>
</template>
