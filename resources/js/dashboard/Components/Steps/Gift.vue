<script setup>
import { inject, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import Field from '../Field.vue';
import { MAX_IMAGE_MB, clone, copyIds, upload, useAutosave } from '../../autosave.js';
import { t } from '../../i18n.js';

const props = defineProps({ invitation: Object, presets: Array, themes: Array, lunarDate: String });

// KHQR images upload at once, per saved account (they need its id).
const editor = inject('editor');
const base = `/app/invitations/${props.invitation.id}`;
const qrError = ref({});
async function sendQr(account, input) {
    const file = input.files[0];
    qrError.value[account.id] = null;
    if (file && file.size > MAX_IMAGE_MB * 1024 * 1024) {
        qrError.value[account.id] = t('common.too_large', { mb: MAX_IMAGE_MB });
    } else if (file) {
        const failed = await upload(`${base}/media`, { kind: 'khqr', gift_account_id: account.id, file });
        if (failed) qrError.value[account.id] = failed.file ?? Object.values(failed)[0];
        else editor.saved();
    }
    input.value = '';
}
const removeQr = (qr) => router.delete(`${base}/media/${qr.id}`, { preserveScroll: true, preserveState: true, onSuccess: () => editor.saved() });

const form = useForm({ accounts: clone(props.invitation.gift_accounts) });
useAutosave(form, `/app/invitations/${props.invitation.id}/gift`, {
    afterSave: (page) => copyIds(form.accounts, page.props.invitation.gift_accounts),
});

const popularBanks = ['ABA Bank', 'ACLEDA Bank', 'Canadia Bank', 'Wing Bank', 'Bakong'];

const add = () => form.accounts.push({ id: null, owner: 'couple', bank: '', account_name: '', account_number: '' });
</script>

<template>
    <div class="grid gap-6">
        <!-- Chong Dai Intro Card -->
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-amber-100 text-amber-800">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="8" width="18" height="13" rx="2" />
                        <path d="M3 12h18M12 8v13" />
                        <path d="M12 8C10 5.5 7.5 5.5 6 7s1 5 6 1ZM12 8C14 5.5 16.5 5.5 18 7s-1 5-6 1Z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-stone-900">{{ $t('gift.title') }}</h2>
                    <p class="text-xs text-stone-600 font-kantumruy">{{ $t('gift.intro') }}</p>
                </div>
            </div>
        </div>

        <!-- Account Cards List -->
        <div v-for="(account, i) in form.accounts" :key="i" class="card p-6 relative border-stone-200/90 shadow-xs">
            <div class="flex items-center justify-between mb-4 border-b border-stone-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="flex size-6 items-center justify-center rounded-full bg-stone-900 text-white text-xs font-bold">{{ i + 1 }}</span>
                    <h3 class="text-sm font-bold text-stone-900">
                        {{ $t('gift.number', { n: i + 1 }) }} ·
                        <span class="text-amber-800">{{ $t(`gift.owners.${account.owner}`) }}</span>
                    </h3>
                </div>
                <button
                    type="button"
                    class="text-xs font-semibold text-red-700 hover:text-red-900 hover:underline flex items-center gap-1 cursor-pointer"
                    @click="form.accounts.splice(i, 1)"
                >
                    <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ $t('gift.remove') }}</span>
                </button>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <Field :label="$t('gift.owner')" :error="form.errors[`accounts.${i}.owner`]">
                    <select v-model="account.owner" class="input">
                        <option value="groom">{{ $t('gift.owners.groom') }}</option>
                        <option value="bride">{{ $t('gift.owners.bride') }}</option>
                        <option value="couple">{{ $t('gift.owners.couple') }}</option>
                    </select>
                </Field>

                <div>
                    <Field :label="$t('gift.bank')" :error="form.errors[`accounts.${i}.bank`]">
                        <input v-model="account.bank" class="input" :placeholder="$t('create.example', { name: 'ABA Bank' })" />
                    </Field>
                    <div class="flex gap-1.5 mt-1.5 flex-wrap">
                        <button
                            v-for="b in popularBanks"
                            :key="b"
                            type="button"
                            class="rounded-full border border-stone-200 bg-stone-50 px-2 py-0.5 text-[10px] font-medium text-stone-600 hover:bg-amber-50 hover:border-amber-300 hover:text-amber-900 transition-colors"
                            @click="account.bank = b"
                        >
                            {{ b }}
                        </button>
                    </div>
                </div>

                <Field :label="$t('gift.account_name')" :error="form.errors[`accounts.${i}.account_name`]">
                    <input v-model="account.account_name" class="input" :placeholder="$t('create.example', { name: 'CHAN SOKHA' })" />
                </Field>

                <Field :label="$t('gift.account_number')" :error="form.errors[`accounts.${i}.account_number`]">
                    <input v-model="account.account_number" inputmode="numeric" class="input font-mono font-medium tracking-wide" :placeholder="$t('create.example', { name: '000 123 456' })" />
                </Field>
            </div>

            <!-- KHQR Image Section -->
            <div class="mt-5 rounded-xl border border-stone-200/90 bg-stone-50/80 p-4">
                <div class="flex items-center justify-between mb-3 border-b border-stone-200/60 pb-2.5">
                    <div class="flex items-center gap-2">
                        <div class="flex size-7 items-center justify-center rounded-lg bg-amber-100 text-amber-800">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-stone-800">{{ $t('gift.khqr') }}</span>
                            <span class="hidden sm:inline text-[11px] text-stone-500 ml-1.5 font-kantumruy">({{ $t('gift.khqr_hint', { mb: MAX_IMAGE_MB }) }})</span>
                        </div>
                    </div>
                    <span v-if="account.id && invitation.khqr[account.id]?.url" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">
                        <span class="size-1.5 rounded-full bg-emerald-600"></span>
                        Active QR
                    </span>
                </div>

                <template v-if="account.id">
                    <div v-if="invitation.khqr[account.id]?.url" class="mb-3 flex items-start gap-3.5">
                        <div class="relative group w-32 rounded-lg overflow-hidden border border-amber-300/80 bg-white p-1.5 shadow-xs">
                            <img :src="invitation.khqr[account.id].url" alt="" class="w-full aspect-square object-contain" />
                            <div class="absolute inset-0 bg-stone-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center p-1">
                                <button
                                    type="button"
                                    class="rounded-md bg-white/95 px-2 py-1 text-[11px] font-semibold text-red-700 shadow-xs hover:bg-white hover:text-red-900 transition-colors cursor-pointer"
                                    @click="removeQr(invitation.khqr[account.id])"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                        <div class="text-xs text-stone-600 space-y-1">
                            <p class="font-semibold text-stone-800">{{ $t('gift.khqr_ready') }}</p>
                            <p class="font-kantumruy text-[11px] text-stone-500">{{ $t('gift.khqr_ready_hint') }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 items-center">
                        <input
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            class="block w-full text-xs text-stone-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-stone-200 file:text-xs file:font-semibold file:bg-white file:text-stone-700 hover:file:bg-amber-50 hover:file:border-amber-300 hover:file:text-amber-900 file:transition-colors file:cursor-pointer cursor-pointer"
                            @change="sendQr(account, $event.target)"
                        />
                        <button
                            v-if="invitation.khqr[account.id]"
                            type="button"
                            class="text-xs font-semibold text-red-700 hover:text-red-900 hover:underline flex items-center gap-1 cursor-pointer"
                            @click="removeQr(invitation.khqr[account.id])"
                        >
                            <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ $t('common.remove') }}</span>
                        </button>
                    </div>

                    <p v-if="qrError[account.id]" class="mt-2 text-xs font-medium text-red-600 flex items-center gap-1.5 bg-red-50 border border-red-200/80 rounded-lg p-2.5">
                        <svg class="size-4 shrink-0 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ qrError[account.id] }}</span>
                    </p>
                </template>

                <p v-else class="text-xs text-stone-600 flex items-center gap-2">
                    <svg class="size-3.5 animate-spin text-amber-700" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ $t('gift.khqr_wait') }}</span>
                </p>
            </div>
        </div>

        <!-- Add Account Button -->
        <button
            v-if="form.accounts.length < 3"
            type="button"
            class="flex items-center justify-center gap-2 rounded-xl border-2 border-dashed border-stone-300 bg-white/60 p-4 text-sm font-semibold text-stone-700 hover:border-amber-500 hover:bg-amber-50/40 hover:text-amber-900 transition-all duration-150 cursor-pointer"
            @click="add"
        >
            <svg class="size-4 text-amber-700" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ $t('gift.add') }}</span>
        </button>
    </div>
</template>