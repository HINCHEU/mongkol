<script setup>
// A labelled field with its validation message under it. Plain on purpose: styling comes later.
defineProps({ label: String, error: String });
</script>

<template>
    <label class="grid gap-1">
        <span class="text-sm text-gray-700">{{ label }}</span>
        <slot />
        <span v-if="error" class="text-sm text-red-700">{{ error }}</span>
    </label>
</template>
