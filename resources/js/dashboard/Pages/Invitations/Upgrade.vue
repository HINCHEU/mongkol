<script setup>
import { computed, ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import Field from '../../Components/Field.vue';

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
    proofError.value = file.size > 10 * 1024 * 1024 ? 'Larger than 10 MB' : null;
    if (proofError.value) return;
    uploading.value = true;
    router.post(`${base}/upgrade/${props.pending.id}/proof`, { file }, {
        ...keep,
        forceFormData: true,
        onError: (errors) => (proofError.value = errors.file ?? Object.values(errors)[0]),
        onFinish: () => (uploading.value = false),
    });
}
const cancel = () => confirm('Cancel this payment request?') && router.delete(`${base}/upgrade/${props.pending.id}`, keep);

const features = (plan) => [
    plan.max_guests ? `Up to ${plan.max_guests} personal links` : 'Unlimited personal links',
    `Up to ${plan.features.gallery_photos} gallery photos`,
    plan.features.own_music ? 'Your own music' : null,
    plan.features.video ? 'YouTube video' : null,
    plan.features.premium_themes ? 'Premium themes' : 'Standard themes',
    plan.features.hide_credit ? 'No footer credit' : null,
].filter(Boolean);
</script>

<template>
    <AppLayout>
        <div class="grid gap-6 max-w-3xl">
            <div class="flex flex-wrap items-center gap-3">
                <Link :href="`${base}/edit`" class="text-sm underline">← Editor</Link>
                <h1 class="text-xl font-bold">Upgrade · ដំឡើងកញ្ចប់</h1>
            </div>

            <p class="text-sm">
                /{{ invitation.slug }} is <strong>{{ invitation.state }}</strong><template v-if="invitation.plan"> on {{ invitation.plan }}</template>.
                <template v-if="invitation.state === 'trial'">Guests see a Preview ribbon until you upgrade.</template>
                <template v-if="invitation.state === 'paused'">The free preview is over: guests see a notice until you upgrade.</template>
            </p>

            <p v-if="rejected" role="alert" class="text-sm text-red-700">
                Payment {{ rejected.reference }} was not approved: {{ rejected.note }}
            </p>

            <!-- Step 2-3: pay and send proof -->
            <div v-if="pending" class="card p-5 grid gap-3">
                <h2 class="font-bold">Pay {{ pending.amount }} for {{ pending.plan }}</h2>
                <p class="text-sm">Reference · លេខយោង: <strong class="font-mono text-lg">{{ pending.reference }}</strong></p>
                <img v-if="khqr?.[pending.currency]" :src="khqr[pending.currency]" :alt="`KHQR ${pending.currency}`" class="w-56" />
                <p v-else class="text-sm text-stone-600">The {{ pending.currency }} KHQR isn't set up yet; contact us to pay.</p>
                <p v-if="instructions" class="text-sm whitespace-pre-line">{{ instructions }}</p>

                <div class="grid gap-1">
                    <span class="text-sm font-semibold">{{ pending.has_proof ? 'Proof received ✓ — we will review it soon. You can replace it:' : 'Upload the transfer screenshot · ផ្ញើរូបថតអេក្រង់' }}</span>
                    <input type="file" accept="image/jpeg,image/png,image/webp" :disabled="uploading" @change="sendProof($event.target)" />
                    <p v-if="uploading" class="text-xs">Uploading…</p>
                    <p v-if="proofError" class="text-xs text-red-600">{{ proofError }}</p>
                </div>
                <div><button type="button" class="text-sm underline text-red-700" @click="cancel">Cancel and choose another plan</button></div>
            </div>

            <!-- Step 1: plan and currency -->
            <form v-else-if="plans.length" class="card p-5 grid gap-4" @submit.prevent="choose">
                <fieldset class="grid gap-3">
                    <legend class="font-bold mb-2">Plan · កញ្ចប់</legend>
                    <label v-for="plan in plans" :key="plan.id" class="flex gap-3 items-start border rounded p-3">
                        <input v-model="form.plan_id" type="radio" :value="plan.id" />
                        <span class="grid gap-1">
                            <strong>{{ plan.name }} · {{ plan.prices[form.currency] }}</strong>
                            <span class="text-xs">{{ features(plan).join(' · ') }}</span>
                            <span v-if="plan.beyond.length" class="text-xs text-amber-800">
                                Not shown on {{ plan.name }}: {{ plan.beyond.join(', ') }}. They stay saved and come back if you upgrade later.
                            </span>
                        </span>
                    </label>
                </fieldset>
                <Field label="Currency · រូបិយប័ណ្ណ" :error="form.errors.currency">
                    <select v-model="form.currency" class="input">
                        <option value="USD">USD ($)</option>
                        <option value="KHR">KHR (៛)</option>
                    </select>
                </Field>
                <p v-if="form.errors.plan_id" class="text-xs text-red-600">{{ form.errors.plan_id }}</p>
                <div>
                    <button type="submit" class="btn-primary" :disabled="form.processing || !chosen">
                        Continue to pay {{ chosen?.prices[form.currency] }}
                    </button>
                </div>
            </form>

            <p v-else class="text-sm">You're on the top plan. Thank you!</p>
        </div>
    </AppLayout>
</template>
