<script setup>
import { computed, ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import Field from '../../Components/Field.vue';

// Plain, functional markup; styling is left to the design pass.
const props = defineProps({ invitation: Object, hasCover: Boolean, message: String, guests: Array });

const base = `/app/invitations/${props.invitation.id}`;
const keep = { preserveScroll: true, preserveState: true };

const form = useForm({ message: props.message });
const saveMessage = () => form.put(`${base}/share`, keep);

const text = (name, url) => form.message.replaceAll('{name}', name).replaceAll('{link}', url);
const telegram = (name, url) => `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text(name, url).replace(url, '').trim())}`;

const copied = ref(null);
async function copy(key, name, url) {
    await navigator.clipboard?.writeText(text(name, url));
    copied.value = key;
    setTimeout(() => copied.value === key && (copied.value = null), 1500);
    return true;
}

// The phone's share sheet (Telegram, Messenger…); desktop browsers without it fall back to copying.
async function share(key, name, url) {
    if (!navigator.share) return copy(key, name, url);
    try {
        await navigator.share({ text: text(name, url) });
        return true;
    } catch {
        return false; // cancelled
    }
}

const markSent = (guest) => guest && !guest.sent && router.put(`${base}/guests/${guest.id}/sent`, { sent: true }, keep);
const toggleSent = (guest, sent) => router.put(`${base}/guests/${guest.id}/sent`, { sent }, keep);

const search = ref('');
const shown = computed(() => {
    const q = search.value.trim().toLowerCase();
    return q ? props.guests.filter((g) => g.name.toLowerCase().includes(q)) : props.guests;
});
const sentCount = computed(() => props.guests.filter((g) => g.sent).length);
</script>

<template>
    <AppLayout>
        <div class="grid gap-6">
            <div class="flex flex-wrap items-center gap-3">
                <Link :href="`${base}/edit`" class="text-sm underline">← Editor</Link>
                <h1 class="text-xl font-bold">Share · ចែករំលែក</h1>
                <Link :href="`${base}/guests`" class="text-sm underline">Guests</Link>
            </div>

            <div v-if="!hasCover" class="card p-5 grid gap-2" role="alert">
                <p class="font-semibold">Add a cover photo first · សូមដាក់រូបភាពគម្របជាមុនសិន</p>
                <p class="text-sm">Telegram keeps the first preview it sees, so links unlock once the invitation has its cover photo.</p>
                <Link :href="`${base}/edit/photos`" class="underline text-sm">Add the cover photo →</Link>
            </div>

            <template v-else>
                <p v-if="invitation.state === 'paused' || invitation.state === 'ended'" class="text-sm text-red-700">
                    This invitation is {{ invitation.state }}: guests opening a link see a notice instead of the invitation.
                </p>

                <form class="card p-5 grid gap-2" @submit.prevent="saveMessage">
                    <Field label="Message (សារ) — {name} becomes the guest's name, {link} their link" :error="form.errors.message">
                        <textarea v-model="form.message" rows="3" class="input"></textarea>
                    </Field>
                    <div><button type="submit" class="btn-secondary" :disabled="form.processing || !form.isDirty">Save message</button></div>
                </form>

                <div class="card p-5 grid gap-2">
                    <h2 class="font-bold">Generic link · តំណទូទៅ</h2>
                    <p class="text-xs text-stone-600">For group chats and Facebook. Shows “ភ្ញៀវកិត្តិយស” instead of a name.</p>
                    <a :href="invitation.url" target="_blank" class="font-mono text-sm underline break-all">{{ invitation.url }}</a>
                    <div class="flex flex-wrap gap-3 text-sm">
                        <button type="button" class="underline" @click="copy('generic', 'ភ្ញៀវកិត្តិយស', invitation.url)">{{ copied === 'generic' ? 'Copied ✓' : 'Copy' }}</button>
                        <button type="button" class="underline" @click="share('generic', 'ភ្ញៀវកិត្តិយស', invitation.url)">Share…</button>
                        <a :href="telegram('ភ្ញៀវកិត្តិយស', invitation.url)" target="_blank" rel="noopener" class="underline">Telegram</a>
                    </div>
                </div>

                <div class="card p-5 grid gap-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="font-bold">Personal links · តំណផ្ទាល់ខ្លួន</h2>
                        <span class="text-sm">{{ sentCount }} / {{ guests.length }} sent</span>
                    </div>
                    <p v-if="!guests.length" class="text-sm">No guests yet. <Link :href="`${base}/guests`" class="underline">Add guests →</Link></p>
                    <template v-else>
                        <input v-model="search" class="input" placeholder="Search guests…" />
                        <table class="w-full text-sm">
                            <thead><tr><th class="text-left">Guest</th><th class="text-left">Send</th><th>QR</th><th>Opens</th><th>Sent</th></tr></thead>
                            <tbody>
                                <tr v-for="guest in shown" :key="guest.id">
                                    <td lang="km">{{ guest.name }}</td>
                                    <td class="whitespace-nowrap">
                                        <button type="button" class="underline" @click="copy(guest.id, guest.name, guest.url).then(() => markSent(guest))">{{ copied === guest.id ? 'Copied ✓' : 'Copy' }}</button>
                                        ·
                                        <button type="button" class="underline" @click="share(guest.id, guest.name, guest.url).then((ok) => ok && markSent(guest))">Share…</button>
                                        ·
                                        <a :href="telegram(guest.name, guest.url)" target="_blank" rel="noopener" class="underline" @click="markSent(guest)">Telegram</a>
                                    </td>
                                    <td class="text-center"><a :href="guest.qr" target="_blank" class="underline">QR</a></td>
                                    <td class="text-center">{{ guest.open_count }}</td>
                                    <td class="text-center"><input type="checkbox" :checked="guest.sent" @change="toggleSent(guest, $event.target.checked)" aria-label="Sent" /></td>
                                </tr>
                            </tbody>
                        </table>
                    </template>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
