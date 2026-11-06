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
        <!-- Wedding Date & Lunar Calendar Card -->
        <div class="card p-6 border-stone-200/90">
            <h2 class="text-base font-bold text-stone-900 mb-1">{{ $t('create.wedding_date') }}</h2>
            <p class="text-xs text-stone-600 mb-4 font-kantumruy">{{ $t('program.date_hint') }}</p>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
                <div class="w-full sm:max-w-xs">
                    <Field :label="$t('program.solar_date')" :error="form.errors.wedding_date">
                        <input v-model="form.wedding_date" type="date" class="input" />
                    </Field>
                </div>

                <div v-if="lunarDate" class="rounded-lg bg-amber-50 p-3.5 border border-amber-200/70 sm:self-end">
                    <p class="text-[11px] font-semibold text-amber-800 uppercase tracking-wider">{{ $t('program.lunar_date') }}</p>
                    <p class="text-xs font-semibold text-amber-950 font-kantumruy mt-0.5" lang="km">{{ lunarDate }}</p>
                </div>
            </div>
        </div>

        <!-- Program Ceremonies Section -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4 border-b border-stone-100 pb-3">
                <div>
                    <h2 class="text-base font-bold text-stone-900">{{ $t('program.title') }}</h2>
                    <p class="text-xs text-stone-600 font-kantumruy">{{ $t('program.intro') }}</p>
                </div>
            </div>

            <!-- Quick Traditional Presets Bar -->
            <div class="mb-6 rounded-lg bg-stone-50 p-4 border border-stone-200/80">
                <p class="text-xs font-bold text-stone-800 mb-2.5 flex items-center gap-1.5">
                    <svg class="size-4 text-amber-700" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ $t('program.presets') }}</span>
                </p>
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="preset in presets"
                        :key="preset.key"
                        type="button"
                        :disabled="used(preset.key)"
                        class="rounded-full border px-3 py-1 text-xs font-medium transition-all duration-150 cursor-pointer font-kantumruy"
                        :class="used(preset.key)
                            ? 'border-stone-200 bg-stone-100 text-stone-400 opacity-60 cursor-not-allowed'
                            : 'border-amber-300 bg-amber-50/80 text-amber-900 hover:bg-amber-100 hover:border-amber-400 active:scale-95'"
                        @click="add(preset)"
                    >
                        <span v-if="used(preset.key)">✓</span>
                        <span v-else>+</span>
                        {{ $page.props.locale === 'en' ? preset.title_en : preset.title_km }}
                    </button>
                    <button
                        type="button"
                        class="rounded-full border border-stone-300 bg-white px-3 py-1 text-xs font-medium text-stone-700 hover:bg-stone-50 hover:border-stone-400 transition-colors cursor-pointer"
                        @click="add()"
                    >
                        + {{ $t('program.custom') }}
                    </button>
                </div>
            </div>

            <!-- Empty State for Program -->
            <div v-if="!form.events.length" class="p-8 text-center border-dashed border-2 border-stone-200 rounded-lg">
                <p class="text-sm text-stone-600 font-kantumruy">{{ $t('program.empty') }}</p>
            </div>

            <!-- Ceremony Cards List -->
            <div v-else class="space-y-4">
                <div
                    v-for="(event, i) in form.events"
                    :key="i"
                    class="rounded-xl border border-stone-200/90 bg-white p-5 shadow-xs transition-all duration-150 hover:border-amber-500/40"
                >
                    <!-- Row Top Header -->
                    <div class="flex items-center justify-between mb-4 border-b border-stone-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="flex size-6 items-center justify-center rounded-full bg-amber-100 text-amber-900 text-xs font-bold">{{ i + 1 }}</span>
                            <span class="text-sm font-bold text-stone-900 font-kantumruy" lang="km">{{ event.title_km || $t('program.new_item') }}</span>
                        </div>

                        <!-- Move and Remove Tools -->
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                class="rounded p-1 text-stone-500 hover:bg-stone-100 hover:text-stone-800 disabled:opacity-30 disabled:hover:bg-transparent cursor-pointer"
                                :disabled="i === 0"
                                :title="$t('common.move_up')"
                                @click="moveRow(form.events, i, -1)"
                            >
                                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button
                                type="button"
                                class="rounded p-1 text-stone-500 hover:bg-stone-100 hover:text-stone-800 disabled:opacity-30 disabled:hover:bg-transparent cursor-pointer"
                                :disabled="i === form.events.length - 1"
                                :title="$t('common.move_down')"
                                @click="moveRow(form.events, i, 1)"
                            >
                                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button
                                type="button"
                                class="rounded p-1 text-red-600 hover:bg-red-50 hover:text-red-800 cursor-pointer ml-1"
                                :title="$t('common.remove')"
                                @click="form.events.splice(i, 1)"
                            >
                                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <div class="grid gap-3">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <Field :label="$t('program.event_date')" :error="form.errors[`events.${i}.date`]">
                                <input v-model="event.date" type="date" class="input" />
                            </Field>
                            <Field :label="$t('program.event_time')" :error="form.errors[`events.${i}.time`]">
                                <input v-model="event.time" type="time" class="input" />
                            </Field>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <Field :label="$t('program.title_km')" :error="form.errors[`events.${i}.title_km`]">
                                <input v-model="event.title_km" lang="km" :placeholder="$t('create.example', { name: 'ពិធីហែជំនូន' })" class="input font-kantumruy" />
                            </Field>
                            <Field :label="$t('program.title_en')" :error="form.errors[`events.${i}.title_en`]">
                                <input v-model="event.title_en" :placeholder="$t('create.example', { name: 'Gift procession' })" class="input" />
                            </Field>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <Field :label="$t('program.venue')" :error="form.errors[`events.${i}.venue_id`]">
                                <select v-model="event.venue_id" class="input">
                                    <option :value="null">— {{ $t('program.select_venue') }} —</option>
                                    <option v-for="venue in invitation.venues" :key="venue.id" :value="venue.id">
                                        {{ venue.name }}
                                    </option>
                                </select>
                            </Field>

                            <Field :label="$t('program.note')" :error="form.errors[`events.${i}.note`]">
                                <input v-model="event.note" :placeholder="$t('program.note_placeholder')" class="input" />
                            </Field>
                        </div>
                    </div>
                </div>
            </div>

            <p v-if="!invitation.venues.length" class="mt-4 text-xs text-amber-800 bg-amber-50 p-3 rounded-lg border border-amber-200">
                💡 {{ $t('program.venue_hint') }}
            </p>
        </div>
    </div>
</template>