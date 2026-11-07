<script setup>
import { computed, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { clone, moveRow, useAutosave } from '../../autosave.js';

const props = defineProps({ invitation: Object, presets: Array, themes: Array, lunarDate: String });

// Theme options are Premium (and the trial): without them only theme_id is sent.
const form = useForm({
    theme_id: props.invitation.theme_id,
    ...(props.invitation.can_customize ? { theme_settings: clone(props.invitation.theme_settings) } : {}),
});
useAutosave(form, `/app/invitations/${props.invitation.id}/theme`);

const current = computed(() => props.themes.find((t) => t.id === form.theme_id));
// Options come from the theme's theme.json; a choice of one (a theme's own colours) needs no picker.
const options = computed(() => Object.entries(current.value?.options ?? {}).filter(([, o]) => o.type === 'bool' || o.choices.length > 1));
const value = (key, option) => form.theme_settings[key] ?? option.default;

// Same rule as ThemeRenderer::arrange: the couple's order, unplaced sections after; the cover stays first.
const sections = computed(() => {
    const order = form.theme_settings?.order ?? [];
    const rank = (s) => (order.includes(s) ? order.indexOf(s) : Infinity);
    return (current.value?.sections ?? []).filter((s) => s !== 'cover').sort((a, b) => rank(a) - rank(b));
});
function move(i, by) {
    const list = [...sections.value];
    moveRow(list, i, by);
    form.theme_settings.order = list;
}
const isHidden = (s) => (form.theme_settings.hidden ?? []).includes(s);
const toggleHidden = (s) => (form.theme_settings.hidden = isHidden(s) ? form.theme_settings.hidden.filter((h) => h !== s) : [...(form.theme_settings.hidden ?? []), s]);

const settingsError = computed(() => Object.entries(form.errors).find(([field]) => field.startsWith('theme_settings'))?.[1]);

// Switching theme drops choices the new one doesn't offer, so the save stays valid.
watch(() => form.theme_id, () => {
    if (!form.theme_settings) return;
    for (const [key, option] of Object.entries(current.value?.options ?? {})) {
        if (option.type === 'select' && key in form.theme_settings && !option.choices.includes(form.theme_settings[key])) delete form.theme_settings[key];
    }
});
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

        <!-- Theme options (Premium) -->
        <div class="mt-8 rounded-2xl border border-stone-200/90 bg-white p-5 sm:p-6 shadow-xs grid gap-6">
            <div class="border-b border-stone-100 pb-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <div class="flex items-center gap-2">
                        <svg class="size-4.5 text-[#7B1122]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3" />
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                        </svg>
                        <h3 class="text-base font-bold text-stone-900">{{ $t('theme.options_title') }}</h3>
                    </div>
                    <p class="text-xs text-stone-500 font-kantumruy mt-0.5">{{ $t('theme.options_hint') }}</p>
                </div>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-900 bg-amber-100/80 border border-amber-300/60 px-2.5 py-0.5 rounded-full uppercase tracking-wider self-start sm:self-auto shadow-2xs">
                    Premium
                </span>
            </div>

            <!-- Basic plan: upgrade invite banner -->
            <div v-if="!invitation.can_customize" class="rounded-xl border border-amber-200/80 bg-gradient-to-r from-amber-50/80 via-white to-amber-50/40 p-4 sm:p-5 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-2.5">
                    <svg class="size-4.5 text-amber-800 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v4.59L7.3 9.24a.75.75 0 00-1.1 1.02l3.25 3.5a.75.75 0 001.1 0l3.25-3.5a.75.75 0 10-1.1-1.02l-1.95 2.1V6.75z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-xs sm:text-sm text-amber-950 font-medium font-kantumruy">
                        {{ $t('theme.options_premium') }}
                    </p>
                </div>
                <Link
                    :href="`/app/invitations/${invitation.id}/upgrade`"
                    class="inline-flex min-h-[44px] items-center gap-1.5 rounded-xl bg-gradient-to-r from-[#7B1122] to-[#58111A] px-4 py-2 text-xs sm:text-sm font-bold text-white shadow-xs hover:from-[#6B0F1D] hover:to-[#4A0B14] transition-all cursor-pointer"
                >
                    <span>{{ $t('editor.upgrade') }}</span>
                    <span aria-hidden="true">→</span>
                </Link>
            </div>

            <!-- Premium customizations -->
            <template v-else>
                <fieldset v-for="[key, option] in options" :key="key" class="grid gap-2.5">
                    <!-- Select option (e.g. palette, font) -->
                    <template v-if="option.type === 'select'">
                        <legend class="text-xs font-bold text-stone-800 uppercase tracking-wider mb-1 flex items-center gap-2">
                            <span>{{ $t(`theme.options.${key}`) }}</span>
                        </legend>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <label
                                v-for="choice in option.choices"
                                :key="choice"
                                class="relative flex items-center gap-2.5 rounded-xl border p-3 min-h-[48px] text-xs sm:text-sm font-semibold transition-all duration-150 cursor-pointer shadow-2xs select-none"
                                :class="value(key, option) === choice ? 'border-[#7B1122] bg-[#7B1122]/5 text-[#7B1122] ring-1 ring-[#7B1122]/40 shadow-xs' : 'border-stone-200 bg-white text-stone-700 hover:border-stone-300 hover:bg-stone-50/80'"
                            >
                                <input
                                    type="radio"
                                    :name="key"
                                    :value="choice"
                                    :checked="value(key, option) === choice"
                                    class="sr-only"
                                    @change="form.theme_settings[key] = choice"
                                />

                                <!-- Swatch circle for colour palettes -->
                                <span
                                    v-if="key === 'palette'"
                                    class="flex size-5 rounded-full overflow-hidden border border-stone-300 shrink-0 shadow-2xs ring-1 ring-black/5"
                                >
                                    <span
                                        class="w-1/2 h-full"
                                        :style="{
                                            backgroundColor: choice === 'crimson' ? '#7B1122' : (choice === 'jade' ? '#1B5242' : (choice === 'sapphire' ? '#1B365D' : (choice === 'lotus' ? '#A63D57' : '#8F3547')))
                                        }"
                                    ></span>
                                    <span
                                        class="w-1/2 h-full"
                                        :style="{
                                            backgroundColor: choice === 'lotus' ? '#78927A' : '#D4AF37'
                                        }"
                                    ></span>
                                </span>

                                <!-- Khmer typography sample for fonts -->
                                <span
                                    v-if="key === 'font'"
                                    class="px-2 py-0.5 rounded text-xs bg-stone-100/90 text-stone-900 border border-stone-200/60 font-normal shrink-0"
                                    :style="choice === 'classic' ? 'font-family: Moul, cursive, serif' : (choice === 'modern' ? 'font-family: Koulen, cursive, sans-serif' : 'font-family: Moulpali, cursive, serif')"
                                >
                                    មង្គលការ
                                </span>

                                <span class="truncate">{{ $t(`theme.choices.${key}.${choice}`) }}</span>

                                <svg v-if="value(key, option) === choice" class="size-4 ml-auto text-[#7B1122] shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                </svg>
                            </label>
                        </div>
                    </template>

                    <!-- Boolean option (e.g. show_countdown) -->
                    <label v-else class="flex items-center justify-between gap-3 p-3.5 rounded-xl border border-stone-200 bg-stone-50/50 min-h-[48px] cursor-pointer hover:bg-stone-50 transition-colors select-none">
                        <span class="text-xs sm:text-sm font-semibold text-stone-800">{{ $t(`theme.options.${key}`) }}</span>
                        <div class="relative inline-flex items-center min-h-[44px]">
                            <input
                                type="checkbox"
                                :checked="value(key, option)"
                                class="sr-only peer"
                                @change="form.theme_settings[key] = $event.target.checked"
                            />
                            <div class="w-11 h-6 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[12px] after:left-[2px] after:bg-white after:border-stone-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#7B1122]"></div>
                        </div>
                    </label>
                </fieldset>

                <!-- Section Reordering & Visibility -->
                <div class="grid gap-3 pt-3 border-t border-stone-100">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-stone-800">{{ $t('theme.sections_title') }}</p>
                        <p class="text-xs text-stone-500 font-kantumruy mt-0.5">{{ $t('theme.sections_hint') }}</p>
                    </div>
                    <ol class="grid gap-2">
                        <li
                            v-for="(section, i) in sections"
                            :key="section"
                            class="flex items-center gap-3 rounded-xl border px-3.5 py-1.5 min-h-[50px] transition-all duration-150"
                            :class="isHidden(section) ? 'border-dashed border-stone-300 bg-stone-50/70 opacity-60' : 'border-stone-200/90 bg-white shadow-2xs'"
                        >
                            <span class="flex size-6 items-center justify-center rounded-md bg-stone-100 text-[11px] font-bold text-stone-600 shrink-0">
                                {{ i + 1 }}
                            </span>
                            <span class="flex-1 text-xs sm:text-sm font-semibold text-stone-800 truncate" lang="km">
                                {{ $t(`theme.sections.${section}`) }}
                            </span>

                            <!-- Visibility Checkbox -->
                            <label class="inline-flex items-center gap-1.5 text-xs font-medium text-stone-600 cursor-pointer min-h-[44px] px-2 rounded-lg hover:bg-stone-100/60 transition-colors select-none">
                                <input
                                    type="checkbox"
                                    :checked="!isHidden(section)"
                                    class="size-4 rounded border-stone-300 text-[#7B1122] accent-[#7B1122] focus:ring-amber-500 cursor-pointer"
                                    @change="toggleHidden(section)"
                                />
                                <span>{{ $t('theme.show') }}</span>
                            </label>

                            <!-- Reorder Controls -->
                            <div class="flex items-center gap-1 shrink-0">
                                <button
                                    type="button"
                                    class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-700 shadow-2xs hover:bg-stone-50 hover:border-stone-300 transition-colors disabled:opacity-30 disabled:pointer-events-none cursor-pointer"
                                    :disabled="i === 0"
                                    :aria-label="$t('common.move_up')"
                                    @click="move(i, -1)"
                                >
                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-700 shadow-2xs hover:bg-stone-50 hover:border-stone-300 transition-colors disabled:opacity-30 disabled:pointer-events-none cursor-pointer"
                                    :disabled="i === sections.length - 1"
                                    :aria-label="$t('common.move_down')"
                                    @click="move(i, 1)"
                                >
                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </li>
                    </ol>
                </div>

                <p v-if="settingsError" class="text-xs font-semibold text-red-700 bg-red-50 p-2.5 rounded-lg border border-red-200" role="alert">
                    {{ settingsError }}
                </p>
            </template>
        </div>
    </div>
</template>