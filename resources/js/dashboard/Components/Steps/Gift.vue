<script setup>
import { useForm } from '@inertiajs/vue3';
import Field from '../Field.vue';
import { clone, copyIds, useAutosave } from '../../autosave.js';

const props = defineProps({ invitation: Object, presets: Array, themes: Array, lunarDate: String });

const form = useForm({ accounts: clone(props.invitation.gift_accounts) });
useAutosave(form, `/app/invitations/${props.invitation.id}/gift`, {
    afterSave: (page) => copyIds(form.accounts, page.props.invitation.gift_accounts),
});

const add = () => form.accounts.push({ id: null, owner: 'couple', bank: '', account_name: '', account_number: '' });
</script>

<template>
    <div class="grid gap-4">
        <p class="text-sm text-gray-600">Bank accounts for ចំណងដៃ, so guests abroad can send a gift. KHQR images come with photo uploads.</p>

        <div v-for="(account, i) in form.accounts" :key="i" class="grid gap-3 rounded-md border border-gray-200 bg-white p-4">
            <Field label="Whose account" :error="form.errors[`accounts.${i}.owner`]">
                <select v-model="account.owner" class="input">
                    <option value="groom">Groom</option>
                    <option value="bride">Bride</option>
                    <option value="couple">Couple</option>
                </select>
            </Field>
            <Field label="Bank" :error="form.errors[`accounts.${i}.bank`]">
                <input v-model="account.bank" class="input" placeholder="ABA Bank" />
            </Field>
            <Field label="Account name" :error="form.errors[`accounts.${i}.account_name`]">
                <input v-model="account.account_name" class="input" />
            </Field>
            <Field label="Account number" :error="form.errors[`accounts.${i}.account_number`]">
                <input v-model="account.account_number" inputmode="numeric" class="input" />
            </Field>
            <button type="button" class="justify-self-start text-sm text-red-700 underline" @click="form.accounts.splice(i, 1)">Remove account</button>
        </div>

        <button v-if="form.accounts.length < 3" type="button" class="justify-self-start rounded-md border border-gray-300 px-3 py-2" @click="add">
            Add account
        </button>
    </div>
</template>
