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
    <div class="grid gap-6">
        <!-- Section Introduction -->
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-amber-100 text-amber-800">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 21.5C12 21.5 5 14.5 5 9.5A7 7 0 0 1 19 9.5C19 14.5 12 21.5 12 21.5Z" />
                        <circle cx="12" cy="9.5" r="2.5" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-stone-900">Ceremony Venues · ទីតាំងប្រារព្ធពិធី</h2>
                    <p class="text-xs text-stone-600 font-kantumruy" lang="km">បញ្ចូលទីតាំងគេហដ្ឋាន ឬសាលមង្គលការ។ អ្នកអាចចម្លងតំណភ្ជាប់ពី Google Maps Share មកបិទភ្ជាប់នៅទីនេះ។</p>
                </div>
            </div>
        </div>

        <!-- Venue Cards List -->
        <div v-for="(venue, i) in form.venues" :key="i" class="card p-6 relative border-stone-200/90 shadow-xs">
            <div class="flex items-center justify-between mb-4 border-b border-stone-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="flex size-6 items-center justify-center rounded-full bg-stone-900 text-white text-xs font-bold">{{ i + 1 }}</span>
                    <h3 class="text-sm font-bold text-stone-900">Venue #{{ i + 1 }}</h3>
                </div>
                <button
                    type="button"
                    class="text-xs font-semibold text-red-700 hover:text-red-900 hover:underline flex items-center gap-1 cursor-pointer"
                    @click="form.venues.splice(i, 1)"
                >
                    <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Remove venue</span>
                </button>
            </div>

            <div class="space-y-4">
                <Field label="Venue Name (ឈ្មោះទីតាំង)" :error="form.errors[`venues.${i}.name`]">
                    <input
                        v-model="venue.name"
                        lang="km"
                        placeholder="ឧទាហរណ៍៖ គេហដ្ឋានខាងស្រី ឬ Mongkol Garden Hall"
                        class="input font-kantumruy"
                    />
                </Field>

                <Field label="Address (អាសយដ្ឋាន)" :error="form.errors[`venues.${i}.address`]">
                    <input
                        v-model="venue.address"
                        lang="km"
                        placeholder="ឧទាហរណ៍៖ ភូមិ ១, សង្កាត់ ទឹកល្អក់ ១, ខណ្ឌ ទួលគោក, ភ្នំពេញ"
                        class="input font-kantumruy"
                    />
                </Field>

                <Field label="Google Maps Link (តំណភ្ជាប់ផែនទី Google Maps)" :error="form.errors[`venues.${i}.maps_url`]">
                    <div class="relative">
                        <input
                            v-model="venue.maps_url"
                            type="url"
                            inputmode="url"
                            class="input pl-9"
                            placeholder="https://maps.app.goo.gl/…"
                        />
                        <svg class="size-4 text-stone-400 absolute left-3 top-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </Field>
            </div>
        </div>

        <!-- Add Venue Button -->
        <button
            v-if="form.venues.length < 5"
            type="button"
            class="flex items-center justify-center gap-2 rounded-xl border-2 border-dashed border-stone-300 bg-white/60 p-4 text-sm font-semibold text-stone-700 hover:border-amber-500 hover:bg-amber-50/40 hover:text-amber-900 transition-all duration-150 cursor-pointer"
            @click="add"
        >
            <svg class="size-4 text-amber-700" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            <span>Add another venue · បន្ថែមទីតាំងថ្មី</span>
        </button>
    </div>
</template>