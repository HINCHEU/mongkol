<script setup>
import { computed, inject, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import Field from '../Field.vue';
import { MAX_IMAGE_MB, MAX_MUSIC_MB, upload, useAutosave } from '../../autosave.js';

// Plain, functional markup; styling is left to the design pass.
const props = defineProps({ invitation: Object, presets: Array, themes: Array, lunarDate: String });

const base = `/app/invitations/${props.invitation.id}`;
const editor = inject('editor');
const media = computed(() => props.invitation.media);

const form = useForm({ video_url: props.invitation.video_url ?? '' });
useAutosave(form, `${base}/photos`);

const busy = ref(null); // the kind being uploaded
const errors = ref({});

async function send(kind, fileList, input) {
    const files = [...fileList];
    const maxMb = kind === 'music' ? MAX_MUSIC_MB : MAX_IMAGE_MB;
    errors.value = {};
    busy.value = kind;

    // One request per file, in order, so a failure stops the rest with its message.
    for (const file of files) {
        if (file.size > maxMb * 1024 * 1024) {
            errors.value[kind] = `${file.name}: larger than ${maxMb} MB`;
            break;
        }
        const failed = await upload(`${base}/media`, { kind, file });
        if (failed) {
            errors.value[kind] = `${file.name}: ${failed.file ?? Object.values(failed)[0]}`;
            break;
        }
        editor.saved();
    }

    busy.value = null;
    input.value = ''; // so choosing the same file again still fires change
}

const visit = { preserveScroll: true, preserveState: true, onSuccess: () => editor.saved() };
const remove = (item) => router.delete(`${base}/media/${item.id}`, visit);

function move(index, by) {
    const ids = media.value.gallery.map((m) => m.id);
    const to = index + by;
    if (to < 0 || to >= ids.length) return;
    ids.splice(to, 0, ids.splice(index, 1)[0]);
    router.put(`${base}/photos`, { video_url: form.video_url, gallery: ids }, visit);
}
</script>

<template>
    <div class="grid gap-6">
        <div class="card p-5 grid gap-3">
            <h2 class="text-base font-bold">Cover photo · រូបភាពគម្រប</h2>
            <img v-if="media.cover?.url" :src="media.cover.url" alt="" class="w-40 rounded" />
            <p v-else-if="media.cover" class="text-xs">Processing…</p>
            <div class="flex flex-wrap gap-3 items-center">
                <input type="file" accept="image/jpeg,image/png,image/webp" :disabled="busy !== null"
                       @change="send('cover', $event.target.files, $event.target)" />
                <button v-if="media.cover" type="button" class="text-xs text-red-700 underline" @click="remove(media.cover)">Remove</button>
            </div>
            <p v-if="busy === 'cover'" class="text-xs">Uploading…</p>
            <p v-if="errors.cover" class="text-xs text-red-600">{{ errors.cover }}</p>
        </div>

        <div class="card p-5 grid gap-3">
            <h2 class="text-base font-bold">Gallery · វិចិត្រសាលរូបភាព ({{ media.gallery.length }})</h2>
            <ul class="grid grid-cols-3 gap-3">
                <li v-for="(photo, i) in media.gallery" :key="photo.id" class="grid gap-1">
                    <img v-if="photo.url" :src="photo.url" alt="" class="aspect-[4/5] w-full rounded object-cover" />
                    <span v-else class="text-xs">Processing…</span>
                    <div class="flex gap-2 text-xs">
                        <button type="button" :disabled="i === 0" @click="move(i, -1)" aria-label="Move earlier">←</button>
                        <button type="button" :disabled="i === media.gallery.length - 1" @click="move(i, 1)" aria-label="Move later">→</button>
                        <button type="button" class="text-red-700 underline" @click="remove(photo)">Remove</button>
                    </div>
                </li>
            </ul>
            <input type="file" multiple accept="image/jpeg,image/png,image/webp" :disabled="busy !== null"
                   @change="send('gallery', $event.target.files, $event.target)" />
            <p v-if="busy === 'gallery'" class="text-xs">Uploading…</p>
            <p v-if="errors.gallery" class="text-xs text-red-600">{{ errors.gallery }}</p>
        </div>

        <div class="card p-5 grid gap-3">
            <h2 class="text-base font-bold">Music · តន្ត្រី (MP3, up to {{ MAX_MUSIC_MB }} MB)</h2>
            <audio v-if="media.music" :src="media.music.url" controls preload="none"></audio>
            <div class="flex flex-wrap gap-3 items-center">
                <input type="file" accept="audio/mpeg,.mp3" :disabled="busy !== null"
                       @change="send('music', $event.target.files, $event.target)" />
                <button v-if="media.music" type="button" class="text-xs text-red-700 underline" @click="remove(media.music)">Remove</button>
            </div>
            <p v-if="busy === 'music'" class="text-xs">Uploading…</p>
            <p v-if="errors.music" class="text-xs text-red-600">{{ errors.music }}</p>
        </div>

        <div class="card p-5">
            <Field label="YouTube video link (វីដេអូ)" :error="form.errors.video_url">
                <input v-model="form.video_url" type="url" class="input" placeholder="https://youtu.be/…" />
            </Field>
        </div>
    </div>
</template>
