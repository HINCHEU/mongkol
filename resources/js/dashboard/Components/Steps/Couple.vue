<script setup>
import { useForm } from '@inertiajs/vue3';
import Field from '../Field.vue';
import { clone, useAutosave } from '../../autosave.js';

const props = defineProps({ invitation: Object, presets: Array, themes: Array, lunarDate: String });

const form = useForm({ people: clone(props.invitation.people) });
useAutosave(form, `/app/invitations/${props.invitation.id}/couple`);

const groups = [
    { title: 'The couple', subtitle: 'កូនកំលោះ និងកូនក្រមុំ', roles: { groom: 'Groom (កូនកំលោះ)', bride: 'Bride (កូនក្រមុំ)' } },
    { title: "Groom's parents", subtitle: 'មាតាបិតាខាងកូនប្រុស', roles: { groom_father: 'Father (ឪពុក)', groom_mother: 'Mother (ម្តាយ)' } },
    { title: "Bride's parents", subtitle: 'មាតាបិតាខាងកូនស្រី', roles: { bride_father: 'Father (ឪពុក)', bride_mother: 'Mother (ម្តាយ)' } },
];
const titles = ['លោក', 'លោកស្រី', 'កញ្ញា', 'ឯកឧត្តម', 'លោកជំទាវ', 'អ្នកឧកញ៉ា'];
const isParent = (role) => role.includes('_');
</script>

<template>
    <div class="grid gap-6">
        <datalist id="titles"><option v-for="t in titles" :key="t" :value="t" /></datalist>

        <div v-for="group in groups" :key="group.title" class="card p-6">
            <div class="mb-4 border-b border-stone-100 pb-3">
                <h2 class="text-base font-bold text-stone-900">{{ group.title }}</h2>
                <p class="text-xs text-amber-800/80 font-kantumruy" lang="km">{{ group.subtitle }}</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div
                    v-for="(label, role) in group.roles"
                    :key="role"
                    class="rounded-lg border border-stone-200/80 bg-stone-50/50 p-4 transition-all duration-150 hover:border-stone-300"
                >
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-stone-800 uppercase tracking-wide">{{ label }}</span>
                        <span v-if="!isParent(role)" class="text-[10px] font-bold text-amber-800 bg-amber-100/70 px-2 py-0.5 rounded-full">Required</span>
                    </div>

                    <div class="space-y-3">
                        <!-- Title for parents -->
                        <div v-if="isParent(role)">
                            <Field label="Honorific Title (គោរមងារ/ងារ)" :error="form.errors[`people.${role}.title`]">
                                <div class="flex gap-1.5 mb-1.5 flex-wrap">
                                    <button
                                        v-for="t in titles"
                                        :key="t"
                                        type="button"
                                        class="rounded-full border border-stone-200 bg-white px-2 py-0.5 text-[11px] font-medium text-stone-700 hover:bg-amber-50 hover:border-amber-300 transition-colors"
                                        :class="form.people[role].title === t ? 'bg-amber-100 border-amber-400 text-amber-900 font-semibold' : ''"
                                        @click="form.people[role].title = t"
                                    >
                                        {{ t }}
                                    </button>
                                </div>
                                <input v-model="form.people[role].title" list="titles" lang="km" placeholder="e.g. លោក, ឯកឧត្តម" class="input font-kantumruy" />
                            </Field>
                        </div>

                        <!-- Khmer Name -->
                        <Field label="Name in Khmer (ឈ្មោះជាភាសាខ្មែរ)" :error="form.errors[`people.${role}.name_km`]">
                            <input v-model="form.people[role].name_km" lang="km" placeholder="ឧទាហរណ៍៖ ចាន់ សុខា" class="input font-kantumruy" />
                        </Field>

                        <!-- English/Latin Name -->
                        <Field label="Name in Latin letters (ឈ្មោះអក្សរឡាតាំង)" :error="form.errors[`people.${role}.name_en`]">
                            <input v-model="form.people[role].name_en" placeholder="e.g. Chan Sokha" class="input" />
                        </Field>

                        <!-- Passed away checkbox for parents -->
                        <div v-if="isParent(role)" class="pt-1">
                            <label class="inline-flex items-center gap-2 text-xs text-stone-600 cursor-pointer select-none">
                                <input
                                    v-model="form.people[role].is_late"
                                    type="checkbox"
                                    class="size-4 rounded border-stone-300 text-[#7B1122] accent-[#7B1122] focus:ring-amber-500"
                                />
                                <span>Passed away · មរណភាព (shows respectfully as '(មរណភាព)')</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Helper Tip -->
        <div class="rounded-lg bg-amber-50/70 p-4 border border-amber-200/60 flex items-start gap-2.5 text-xs text-amber-900">
            <svg class="size-4 shrink-0 text-amber-700 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <span class="leading-relaxed font-kantumruy" lang="km">ចំណាំ៖ ប្រសិនបើទុកឈ្មោះឪពុក ឬម្តាយនៅទំនេរ ឈ្មោះរបស់គាត់នឹងមិនបង្ហាញលើសំបុត្រអញ្ជើញឡើយ។</span>
        </div>
    </div>
</template>