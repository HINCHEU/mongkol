<script setup>
import { inject, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import Field from '../Field.vue';
import { MAX_IMAGE_MB, clone, copyIds, upload, useAutosave } from '../../autosave.js';

const props = defineProps({ invitation: Object, presets: Array, themes: Array, lunarDate: String });

// KHQR images upload at once, per saved account (they need its id).
const editor = inject('editor');
const base = `/app/invitations/${props.invitation.id}`;
const qrError = ref({});
async function sendQr(account, input) {
    const file = input.files[0];
    qrError.value[account.id] = null;
    if (file && file.size > MAX_IMAGE_MB * 1024 * 1024) {
        qrError.value[account.id] = `Larger than ${MAX_IMAGE_MB} MB`;
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
                    <h2 class="text-base font-bold text-stone-900">Chong Dai Gift Accounts · គណនីទទួលចំណងដៃ</h2>
                    <p class="text-xs text-stone-600 font-kantumruy" lang="km">ព័ត៌មានគណនីធនាគារសម្រាប់ភ្ញៀវចូលរួមចំណងដៃពីចម្ងាយ។ ភ្ញៀវអាចចម្លងលេខគណនីដោយចុចតែម្តង។</p>
                </div>
            </div>
        </div>

        <!-- Account Cards List -->
        <div v-for="(account, i) in form.accounts" :key="i" class="card p-6 relative border-stone-200/90 shadow-xs">
            <div class="flex items-center justify-between mb-4 border-b border-stone-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="flex size-6 items-center justify-center rounded-full bg-stone-900 text-white text-xs font-bold">{{ i + 1 }}</span>
                    <h3 class="text-sm font-bold text-stone-900">
                        Account #{{ i + 1 }} ·
                        <span class="text-amber-800 capitalize">{{ account.owner }}</span>
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
                    <span>Remove account</span>
                </button>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <Field label="Whose account (ម្ចាស់គណនី)" :error="form.errors[`accounts.${i}.owner`]">
                    <select v-model="account.owner" class="input">
                        <option value="groom">Groom · ខាងកូនកំលោះ</option>
                        <option value="bride">Bride · ខាងកូនក្រមុំ</option>
                        <option value="couple">Both / Couple · ទាំងសងខាង</option>
                    </select>
                </Field>

                <div>
                    <Field label="Bank Name (ឈ្មោះធនាគារ)" :error="form.errors[`accounts.${i}.bank`]">
                        <input v-model="account.bank" class="input" placeholder="e.g. ABA Bank" />
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

                <Field label="Account Holder Name (ឈ្មោះម្ចាស់គណនី)" :error="form.errors[`accounts.${i}.account_name`]">
                    <input v-model="account.account_name" class="input" placeholder="e.g. CHAN SOKHA" />
                </Field>

                <Field label="Account Number (លេខគណនី)" :error="form.errors[`accounts.${i}.account_number`]">
                    <input v-model="account.account_number" inputmode="numeric" class="input font-mono font-medium tracking-wide" placeholder="e.g. 000 123 456" />
                </Field>
            </div>

            <div class="mt-4 grid gap-2">
                <span class="text-xs font-semibold text-stone-700">KHQR image (រូប KHQR)</span>
                <template v-if="account.id">
                    <img v-if="invitation.khqr[account.id]?.url" :src="invitation.khqr[account.id].url" alt="" class="w-32 rounded" />
                    <div class="flex flex-wrap gap-3 items-center">
                        <input type="file" accept="image/jpeg,image/png,image/webp" @change="sendQr(account, $event.target)" />
                        <button v-if="invitation.khqr[account.id]" type="button" class="text-xs text-red-700 underline" @click="removeQr(invitation.khqr[account.id])">Remove</button>
                    </div>
                    <p v-if="qrError[account.id]" class="text-xs text-red-600">{{ qrError[account.id] }}</p>
                </template>
                <p v-else class="text-xs text-stone-600">Saving the account… the KHQR upload appears once it is saved.</p>
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
            <span>Add another bank account · បន្ថែមគណនីធនាគារ</span>
        </button>
    </div>
</template>