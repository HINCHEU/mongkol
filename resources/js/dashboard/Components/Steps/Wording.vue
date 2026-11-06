<script setup>
import { useForm } from '@inertiajs/vue3';
import Field from '../Field.vue';
import { clone, useAutosave } from '../../autosave.js';

const props = defineProps({ invitation: Object, presets: Array, themes: Array, lunarDate: String });

const form = useForm(clone(props.invitation.content));
useAutosave(form, `/app/invitations/${props.invitation.id}/wording`);

// Titles and hints come from lang/*/dashboard.php (wording.{key}_title, wording.{key}_desc); the
// placeholders are sample invitation text in each language, so they stay as they are.
const blocks = {
    opening: {
        placeholderKm: 'យើងខ្ញុំមានកិត្តិយសសូមគោរពអញ្ជើញ ចូលរួមជាភ្ញៀវកិត្តិយស ក្នុងពិធីអាពាហ៍ពិពាហ៍ កូនប្រុស កូនស្រី របស់យើងខ្ញុំ',
        placeholderEn: 'We have the honour of inviting you to celebrate the marriage of our children',
    },
    closing: {
        placeholderKm: 'វត្តមានរបស់លោកអ្នក គឺជាកិត្តិយសដ៏ខ្ពង់ខ្ពស់សម្រាប់គ្រួសារយើងខ្ញុំ',
        placeholderEn: 'Your presence will be our greatest honour',
    },
    thank_you: {
        placeholderKm: 'សូមអរគុណចំពោះវត្តមាន និងពរជ័យរបស់លោកអ្នក',
        placeholderEn: 'Thank you for celebrating with us',
    },
};
</script>

<template>
    <div class="grid gap-6">
        <!-- Section Intro & Language Setting -->
        <div class="card p-6 border-stone-200/90">
            <h2 class="text-base font-bold text-stone-900 mb-1">{{ $t('wording.language_title') }}</h2>
            <p class="text-xs text-stone-600 mb-4 font-kantumruy">{{ $t('wording.language_hint') }}</p>

            <div class="max-w-xs">
                <Field :label="$t('wording.first_language')" :error="form.errors.locale">
                    <select v-model="form.locale" class="input font-medium">
                        <option value="km">ខ្មែរ</option>
                        <option value="en">English</option>
                    </select>
                </Field>
            </div>
        </div>

        <!-- Custom Wording Textareas -->
        <div v-for="(block, key) in blocks" :key="key" class="card p-6">
            <div class="mb-4 border-b border-stone-100 pb-3">
                <h3 class="text-base font-bold text-stone-900">{{ $t(`wording.${key}_title`) }}</h3>
                <p class="text-xs text-stone-600 mt-0.5">{{ $t(`wording.${key}_desc`) }} <span class="text-amber-800">{{ $t('wording.empty_hint') }}</span></p>
            </div>

            <div class="grid gap-4">
                <Field :label="$t('common.khmer')" :error="form.errors[`${key}.km`]">
                    <textarea
                        v-model="form[key].km"
                        lang="km"
                        rows="3"
                        class="input font-kantumruy leading-relaxed"
                        :placeholder="block.placeholderKm"
                    ></textarea>
                </Field>

                <Field :label="$t('common.english')" :error="form.errors[`${key}.en`]">
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
            <h3 class="text-base font-bold text-stone-900 mb-1">{{ $t('wording.lunar_title') }}</h3>
            <p class="text-xs text-stone-600 mb-4 font-kantumruy">{{ $t('wording.lunar_hint') }}</p>

            <Field :label="$t('wording.lunar_label')" :error="form.errors.lunar_date">
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