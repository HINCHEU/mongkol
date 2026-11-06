<script setup>
import { useForm } from '@inertiajs/vue3';
import Field from '../Field.vue';
import { clone, useAutosave } from '../../autosave.js';

const props = defineProps({ invitation: Object, presets: Array, themes: Array, lunarDate: String });

const form = useForm(clone(props.invitation.content));
useAutosave(form, `/app/invitations/${props.invitation.id}/wording`);

const blocks = {
    opening: {
        title: 'Opening Invitation Words · សេចក្តីផ្តើមអញ្ជើញ',
        desc: 'The opening formal invitation to honored guests.',
        placeholderKm: 'យើងខ្ញុំមានកិត្តិយសសូមគោរពអញ្ជើញ ចូលរួមជាភ្ញៀវកិត្តិយស ក្នុងពិធីអាពាហ៍ពិពាហ៍ កូនប្រុស កូនស្រី របស់យើងខ្ញុំ',
        placeholderEn: 'We have the honour of inviting you to celebrate the marriage of our children',
    },
    closing: {
        title: 'Closing Respect Words · សេចក្តីគោរពបញ្ចប់',
        desc: 'The closing sentence of honor and appreciation.',
        placeholderKm: 'វត្តមានរបស់លោកអ្នក គឺជាកិត្តិយសដ៏ខ្ពង់ខ្ពស់សម្រាប់គ្រួសារយើងខ្ញុំ',
        placeholderEn: 'Your presence will be our greatest honour',
    },
    thank_you: {
        title: 'Thank-You Line · ពាក្យថ្លែងអំណរគុណ',
        desc: 'Displayed on the folio footer and countdown end.',
        placeholderKm: 'សូមអរគុណចំពោះវត្តមាន និងពរជ័យរបស់លោកអ្នក',
        placeholderEn: 'Thank you for celebrating with us',
    },
};
</script>

<template>
    <div class="grid gap-6">
        <!-- Section Intro & Language Setting -->
        <div class="card p-6 border-stone-200/90">
            <h2 class="text-base font-bold text-stone-900 mb-1">Language Preference · ភាសាលំនាំដើម</h2>
            <p class="text-xs text-stone-600 mb-4 font-kantumruy" lang="km">ជ្រើសរើសភាសាដែលភ្ញៀវនឹងឃើញមុនគេនៅពេលបើកសំបុត្រ (ភ្ញៀវអាចប្តូរភាសាបានគ្រប់ពេល)។</p>

            <div class="max-w-xs">
                <Field label="First language shown (ភាសាបង្ហាញមុនគេ)" :error="form.errors.locale">
                    <select v-model="form.locale" class="input font-medium">
                        <option value="km">ខ្មែរ (Khmer - Default)</option>
                        <option value="en">English (អង់គ្លេស)</option>
                    </select>
                </Field>
            </div>
        </div>

        <!-- Custom Wording Textareas -->
        <div v-for="(block, key) in blocks" :key="key" class="card p-6">
            <div class="mb-4 border-b border-stone-100 pb-3">
                <h3 class="text-base font-bold text-stone-900">{{ block.title }}</h3>
                <p class="text-xs text-stone-600 mt-0.5">{{ block.desc }} <span class="text-amber-800">Leave empty to use standard theme text.</span></p>
            </div>

            <div class="grid gap-4">
                <Field label="Khmer (ភាសាខ្មែរ)" :error="form.errors[`${key}.km`]">
                    <textarea
                        v-model="form[key].km"
                        lang="km"
                        rows="3"
                        class="input font-kantumruy leading-relaxed"
                        :placeholder="block.placeholderKm"
                    ></textarea>
                </Field>

                <Field label="English (ភាសាអង់គ្លេស)" :error="form.errors[`${key}.en`]">
                    <textarea
                        v-model="form[key].en"
                        rows="2"
                        class="input leading-relaxed"
                        :placeholder="block.placeholderEn"
                    ></textarea>
                </Field>
            </div>
        </div>

        <!-- Lunar Date Custom Line -->
        <div class="card p-6">
            <h3 class="text-base font-bold text-stone-900 mb-1">Khmer Lunar Date Line · ថ្ងៃខែចន្ទគតិខ្មែរ</h3>
            <p class="text-xs text-stone-600 mb-4 font-kantumruy" lang="km">គណនាដោយស្វ័យប្រវត្តិពីថ្ងៃខែសកល។ អ្នកអាចសរសេរប្តូរដោយខ្លួនឯងបានប្រសិនបើចង់បានទម្រង់ពិសេស។</p>

            <Field label="Custom Lunar Date (កាលបរិច្ឆេទចន្ទគតិ)" :error="form.errors.lunar_date">
                <input
                    v-model="form.lunar_date"
                    lang="km"
                    class="input font-kantumruy"
                    :placeholder="lunarDate || 'ថ្ងៃសៅរ៍ ៣ កើត ខែមិគសិរ...'"
                />
            </Field>
        </div>
    </div>
</template>