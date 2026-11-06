<script setup>
import { useForm } from '@inertiajs/vue3';
import { useAutosave } from '../../autosave.js';

const props = defineProps({ invitation: Object, presets: Array, themes: Array, lunarDate: String });

const form = useForm({ theme_id: props.invitation.theme_id });
useAutosave(form, `/app/invitations/${props.invitation.id}/theme`);
</script>

<template>
    <div class="card p-6">
        <div class="mb-5 border-b border-stone-100 pb-3">
            <h2 class="text-base font-bold text-stone-900">{{ $t('theme.title') }}</h2>
            <p class="text-xs text-stone-600 mt-1 font-kantumruy">{{ $t('theme.hint') }}</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <label
                v-for="theme in themes"
                :key="theme.id"
                class="flex flex-col justify-between rounded-xl border p-5 transition-all duration-150 cursor-pointer"
                :class="form.theme_id === theme.id
                    ? 'border-amber-600 bg-amber-50/40 shadow-xs ring-1 ring-amber-500/30'
                    : 'border-stone-200 bg-white hover:border-stone-300 hover:shadow-xs'"
            >
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2.5">
                            <input
                                v-model="form.theme_id"
                                type="radio"
                                :value="theme.id"
                                class="size-4 text-[#7B1122] accent-[#7B1122] focus:ring-amber-500"
                            />
                            <span class="font-bold text-stone-900 text-base">{{ theme.name }}</span>
                        </div>

                        <span
                            class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                            :class="theme.tier === 'premium' ? 'bg-amber-100 text-amber-900' : 'bg-stone-100 text-stone-700'"
                        >
                            {{ theme.tier === 'premium' ? $t('common.premium') : $t('theme.standard') }}
                        </span>
                    </div>

                </div>

                <div class="mt-4 pt-3 border-t border-stone-100 flex items-center justify-between text-xs">
                    <span v-if="form.theme_id === theme.id" class="font-bold text-amber-800 flex items-center gap-1">
                        <span>{{ $t('theme.active') }}</span>
                        <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <span v-else class="text-stone-400">{{ $t('theme.select') }}</span>

                    <a
                        :href="`/themes/${theme.slug}`"
                        target="_blank"
                        class="font-semibold text-stone-700 hover:text-[#7B1122] hover:underline"
                        @click.stop
                    >
                        {{ $t('common.view_demo') }} ↗
                    </a>
                </div>
            </label>
        </div>

        <p v-if="form.errors.theme_id" class="mt-3 text-xs font-semibold text-red-700">{{ form.errors.theme_id }}</p>
    </div>
</template>