<script setup>
import { computed, ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import Field from '../../Components/Field.vue';
import { t } from '../../i18n.js';

// Plain, functional markup; styling is left to the design pass.
const props = defineProps({
    invitation: Object,
    plans: Array,
    pending: Object,
    rejected: Object,
    instructions: String,
    khqr: Object,
});

const base = `/app/invitations/${props.invitation.id}`;
const keep = { preserveScroll: true, preserveState: true };

const form = useForm({ plan_id: props.plans.at(-1)?.id ?? null, currency: 'USD' });
const chosen = computed(() => props.plans.find((p) => p.id === form.plan_id));
const choose = () => form.post(`${base}/upgrade`, keep);

const proofError = ref(null);
const uploading = ref(false);
function sendProof(input) {
    const file = input.files[0];
    input.value = '';
    if (!file) return;
    proofError.value = file.size > 10 * 1024 * 1024 ? t('common.too_large', { mb: 10 }) : null;
    if (proofError.value) return;
    uploading.value = true;
    router.post(`${base}/upgrade/${props.pending.id}/proof`, { file }, {
        ...keep,
        forceFormData: true,
        onError: (errors) => (proofError.value = errors.file ?? Object.values(errors)[0]),
        onFinish: () => (uploading.value = false),
    });
}
const cancel = () => confirm(t('upgrade.confirm_cancel')) && router.delete(`${base}/upgrade/${props.pending.id}`, keep);

// Templates can't reach `navigator`, so copying goes through the script.
const copied = ref(false);
async function copyReference() {
    await navigator.clipboard?.writeText(props.pending.reference);
    copied.value = true;
    setTimeout(() => (copied.value = false), 1500);
}

const features = (plan) => [
    plan.max_guests ? t('upgrade.features.links', { count: plan.max_guests }) : t('upgrade.features.unlimited_links'),
    t('upgrade.features.photos', { count: plan.features.gallery_photos }),
    plan.features.own_music ? t('upgrade.features.own_music') : null,
    plan.features.video ? t('upgrade.features.video') : null,
    plan.features.premium_themes ? t('upgrade.features.premium_themes') : t('upgrade.features.standard_themes'),
    plan.features.hide_credit ? t('upgrade.features.no_credit') : null,
].filter(Boolean);
</script>

<template>
    <AppLayout>
        <div class="grid gap-6 max-w-3xl">
            <!-- Header Bar -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-stone-200/80 pb-4">
                <div class="flex items-center gap-3">
                    <Link
                        :href="`${base}/edit`"
                        class="flex size-9 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-600 hover:text-stone-900 hover:border-stone-300 transition-colors shadow-xs"
                        :title="$t('common.back_to_editor')"
                    >
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl font-bold tracking-tight text-stone-900">{{ $t('editor.upgrade') }}</h1>
                        </div>
                        <p class="text-xs text-stone-500 font-kantumruy">{{ $t('upgrade.subtitle') }}</p>
                    </div>
                </div>
            </div>

            <!-- Current State Banner -->
            <div class="rounded-xl border border-stone-200/90 bg-white p-4 shadow-xs flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-9 items-center justify-center rounded-lg font-bold text-xs"
                        :class="invitation.state === 'live'
                            ? 'bg-emerald-100 text-emerald-800'
                            : (invitation.state === 'paused' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800')"
                    >
                        <span v-if="invitation.state === 'live'">✓</span>
                        <span v-else-if="invitation.state === 'paused'">!</span>
                        <span v-else>★</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-semibold text-stone-700">/{{ invitation.slug }}</span>
                            <span
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                                :class="invitation.state === 'live'
                                    ? 'bg-emerald-100 text-emerald-800'
                                    : (invitation.state === 'paused' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-900')"
                            >
                                {{ $t(`states.${invitation.state}`) }}
                            </span>
                            <span v-if="invitation.plan" class="text-xs font-medium text-stone-600">
                                {{ $t('upgrade.on_plan', { plan: invitation.plan }) }}
                            </span>
                        </div>
                        <p class="text-xs text-stone-500 mt-0.5">
                            <template v-if="['trial', 'paused', 'live'].includes(invitation.state)">{{ $t(`upgrade.state_hint.${invitation.state}`) }}</template>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Prominent Payment Rejected Alert -->
            <div v-if="rejected" role="alert" class="rounded-xl border-2 border-red-300 bg-red-50/90 p-4 sm:p-5 shadow-xs flex items-start gap-3">
                <div class="flex size-9 items-center justify-center rounded-full bg-red-100 text-red-700 shrink-0 mt-0.5">
                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <h2 class="text-sm font-bold text-red-950">{{ $t('upgrade.rejected_title') }}</h2>
                    <p class="text-sm font-medium text-red-800">
                        <strong class="font-mono text-red-950">{{ rejected.reference }}</strong> — {{ rejected.note }}
                    </p>
                    <p class="text-xs text-red-700 font-kantumruy">{{ $t('upgrade.rejected_hint') }}</p>
                </div>
            </div>

            <!-- State 2: Pay and send proof (v-if="pending") -->
            <div v-if="pending" class="card p-6 grid gap-5">
                <div class="border-b border-stone-100 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-stone-900">{{ $t('upgrade.pay', { amount: pending.amount, plan: pending.plan }) }}</h2>
                        <p class="text-xs text-stone-500 font-kantumruy">{{ $t('upgrade.pay_hint') }}</p>
                    </div>
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-900">
                        {{ $t('upgrade.pending') }}
                    </span>
                </div>

                <!-- Reference Code Card (Large, easy to read, tap-to-copy) -->
                <div class="rounded-xl border border-amber-300/80 bg-gradient-to-r from-amber-50/70 via-stone-50 to-amber-50/40 p-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-amber-900 block mb-1">
                        {{ $t('upgrade.reference') }}
                    </span>
                    <div class="flex items-center justify-between gap-3">
                        <strong class="font-mono text-xl sm:text-2xl font-black text-amber-950 tracking-wider select-all">
                            {{ pending.reference }}
                        </strong>
                        <button
                            type="button"
                            class="inline-flex min-h-[44px] items-center gap-1.5 rounded-lg border border-amber-300 bg-white px-3 py-1.5 text-xs font-semibold text-amber-900 shadow-xs hover:bg-amber-50 transition-colors cursor-pointer"
                            @click="copyReference"
                        >
                            <svg class="size-3.5 text-amber-700" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 2H9a3 3 0 01-3-2z" />
                            </svg>
                            <span>{{ copied ? $t('common.copied') : $t('common.copy') }}</span>
                        </button>
                    </div>
                    <p class="text-[11px] text-amber-900/80 mt-1.5 font-kantumruy">
                        * {{ $t('upgrade.reference_hint') }}
                    </p>
                </div>

                <!-- QR Code Display Area -->
                <div class="flex flex-col sm:flex-row items-center gap-6 rounded-xl border border-stone-200 bg-stone-50/50 p-5">
                    <div class="w-56 shrink-0 rounded-xl overflow-hidden border border-amber-300/80 bg-white p-3 shadow-xs">
                        <img v-if="khqr?.[pending.currency]" :src="khqr[pending.currency]" :alt="`KHQR ${pending.currency}`" class="w-full aspect-square object-contain" />
                        <p v-else class="text-xs text-stone-600 text-center py-6">{{ $t('upgrade.no_khqr', { currency: pending.currency }) }}</p>
                        <div class="mt-2 text-center">
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-red-700 uppercase tracking-widest bg-red-50 border border-red-200 px-2 py-0.5 rounded">
                                Bakong KHQR
                            </span>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs text-stone-700">
                        <p v-if="instructions" class="whitespace-pre-line leading-relaxed text-stone-800 font-medium">
                            {{ instructions }}
                        </p>
                        <div class="space-y-1 text-stone-500">
                            <p>1. {{ $t('upgrade.step1') }}</p>
                            <p>2. {{ $t('upgrade.step2') }} <strong class="text-stone-900">{{ pending.amount }}</strong></p>
                            <p>3. {{ $t('upgrade.step3') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Upload Transfer Proof Section -->
                <div class="rounded-xl border border-stone-200 bg-white p-4 shadow-xs grid gap-3">
                    <div class="flex items-center gap-2">
                        <svg class="size-4 text-amber-800 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-semibold text-stone-900">
                            {{ pending.has_proof ? $t('upgrade.proof_received') : $t('upgrade.upload_proof') }}
                        </span>
                    </div>

                    <div class="flex flex-wrap gap-3 items-center">
                        <input
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            :disabled="uploading"
                            class="block w-full text-xs text-stone-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border file:border-stone-200 file:text-xs file:font-semibold file:bg-stone-50 file:text-stone-700 hover:file:bg-amber-50 hover:file:border-amber-300 hover:file:text-amber-900 file:transition-colors file:cursor-pointer disabled:opacity-50 cursor-pointer"
                            @change="sendProof($event.target)"
                        />
                    </div>

                    <p v-if="uploading" class="text-xs font-medium text-amber-800 flex items-center gap-1.5 animate-pulse">
                        <svg class="size-3.5 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ $t('common.uploading') }}</span>
                    </p>
                    <p v-if="proofError" class="text-xs font-medium text-red-600 flex items-center gap-1.5 bg-red-50 border border-red-200 rounded-lg p-2.5">
                        <svg class="size-4 text-red-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ proofError }}</span>
                    </p>
                </div>

                <!-- Cancel Button -->
                <div class="pt-1">
                    <button
                        type="button"
                        class="min-h-[44px] text-xs font-semibold text-red-700 hover:text-red-900 underline cursor-pointer"
                        @click="cancel"
                    >
                        {{ $t('upgrade.cancel') }}
                    </button>
                </div>
            </div>

            <!-- State 1: Choose plan and currency (v-else-if="plans.length") -->
            <form v-else-if="plans.length" class="card p-6 grid gap-5" @submit.prevent="choose">
                <div class="border-b border-stone-100 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-stone-900">{{ $t('upgrade.choose') }}</h2>
                        <p class="text-xs text-stone-500 font-kantumruy">{{ $t('upgrade.choose_hint') }}</p>
                    </div>
                </div>

                <fieldset class="grid gap-3">
                    <legend class="sr-only">{{ $t('upgrade.choose') }}</legend>

                    <label
                        v-for="plan in plans"
                        :key="plan.id"
                        class="flex gap-4 items-start rounded-xl border p-4 sm:p-5 transition-all duration-150 cursor-pointer min-h-[44px]"
                        :class="form.plan_id === plan.id
                            ? 'border-amber-600 bg-amber-50/50 shadow-xs ring-1 ring-amber-500/30'
                            : 'border-stone-200 bg-white hover:border-stone-300 hover:bg-stone-50/50'"
                    >
                        <input
                            v-model="form.plan_id"
                            type="radio"
                            :value="plan.id"
                            class="size-4.5 text-[#7B1122] accent-[#7B1122] focus:ring-amber-500 mt-1 shrink-0"
                        />
                        <span class="grid gap-1.5 flex-1">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <strong class="text-base font-bold text-stone-900">{{ plan.name }}</strong>
                                <span class="text-base font-extrabold text-[#7B1122] font-mono">{{ plan.prices[form.currency] }}</span>
                            </div>

                            <span class="text-xs text-stone-600 leading-relaxed">{{ features(plan).join(' · ') }}</span>

                            <span v-if="plan.beyond.length" class="text-xs text-amber-900 bg-amber-100/70 border border-amber-200/80 rounded-lg p-2.5 mt-1 leading-relaxed">
                                <strong>{{ $t('upgrade.beyond', { plan: plan.name }) }}</strong> {{ plan.beyond.join(', ') }}. {{ $t('upgrade.beyond_hint') }}
                            </span>
                        </span>
                    </label>
                </fieldset>

                <!-- Currency Selector -->
                <Field :label="$t('upgrade.currency')" :error="form.errors.currency">
                    <select v-model="form.currency" class="input font-semibold min-h-[44px]">
                        <option value="USD">USD ($)</option>
                        <option value="KHR">KHR (៛)</option>
                    </select>
                </Field>

                <p v-if="form.errors.plan_id" class="text-xs text-red-600">{{ form.errors.plan_id }}</p>

                <div class="pt-2">
                    <button
                        type="submit"
                        class="btn-primary min-h-[44px] w-full sm:w-auto"
                        :disabled="form.processing || !chosen"
                    >
                        <span>{{ $t('upgrade.continue', { price: chosen?.prices[form.currency] }) }}</span>
                    </button>
                </div>
            </form>

            <!-- State 3: On Top Plan (v-else) -->
            <div v-else class="card p-8 text-center bg-gradient-to-br from-amber-50/70 via-stone-50 to-white border-amber-200 shadow-xs">
                <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-gradient-to-br from-[#7B1122] to-[#58111A] text-amber-200 shadow-md mb-3">
                    <svg class="size-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                </div>
                <h2 class="text-base font-bold text-stone-900">{{ $t('upgrade.top_plan') }}</h2>
            </div>
        </div>
    </AppLayout>
</template>
