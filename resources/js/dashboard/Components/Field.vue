<script setup>
defineProps({ label: String, error: String });
</script>

<template>
    <label class="grid gap-1.5">
        <span v-if="label" class="text-xs font-semibold text-stone-700">{{ label }}</span>
        <slot />
        <span v-if="error" class="text-xs font-medium text-red-600 flex items-center gap-1">
            <svg class="size-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ error }}</span>
        </span>
    </label>
</template>