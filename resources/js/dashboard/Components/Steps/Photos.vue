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
        <!-- Photos & Media Intro Card -->
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-amber-100 text-amber-800">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <polyline points="21 15 16 10 5 21" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-stone-900">Photos & Music · រូបភាព និងតន្ត្រី</h2>
                    <p class="text-xs text-stone-600 font-kantumruy" lang="km">រូបថតគម្រប វិចិត្រសាលអនុស្សាវរីយ៍ តន្ត្រីភ្លេងការ និងវីដេអូមង្គលការ។</p>
                </div>
            </div>
        </div>

        <!-- Cover Photo Section -->
        <div class="card p-6 grid gap-4">
            <div class="border-b border-stone-100 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-stone-900">Cover photo · រូបភាពគម្រប</h2>
                    <p class="text-xs text-stone-500 font-kantumruy" lang="km">រូបថតចម្បងដែលបង្ហាញលើស្រោមសំបុត្រ និងទំព័រមុខនៃសំបុត្រ (Up to {{ MAX_IMAGE_MB }} MB)</p>
                </div>
                <span class="text-[10px] font-bold text-amber-800 bg-amber-100/70 px-2 py-0.5 rounded-full">Primary</span>
            </div>

            <!-- Preview States -->
            <div v-if="media.cover?.url" class="relative group w-44 rounded-xl overflow-hidden border border-amber-200/80 shadow-xs bg-stone-100">
                <img :src="media.cover.url" alt="" class="aspect-[4/5] w-full object-cover" />
                <div class="absolute inset-0 bg-stone-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center p-2">
                    <button
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-white/95 px-3 py-1.5 text-xs font-semibold text-red-700 shadow-xs hover:bg-white hover:text-red-900 transition-all cursor-pointer"
                        @click="remove(media.cover)"
                    >
                        <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>Remove</span>
                    </button>
                </div>
            </div>

            <div v-else-if="media.cover" class="w-44 aspect-[4/5] rounded-xl border border-dashed border-amber-300 bg-amber-50/50 flex flex-col items-center justify-center gap-2 p-4 text-center">
                <svg class="size-6 animate-spin text-amber-700" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-xs font-semibold text-amber-900 font-kantumruy" lang="km">កំពុងដំណើរការរូបភាព…</span>
                <span class="text-[11px] text-stone-500">Processing…</span>
            </div>

            <!-- Upload Controls -->
            <div class="flex flex-wrap gap-3 items-center">
                <input
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    :disabled="busy !== null"
                    class="block w-full text-xs text-stone-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border file:border-stone-200 file:text-xs file:font-semibold file:bg-stone-50 file:text-stone-700 hover:file:bg-amber-50 hover:file:border-amber-300 hover:file:text-amber-900 file:transition-colors file:cursor-pointer disabled:opacity-50 cursor-pointer"
                    @change="send('cover', $event.target.files, $event.target)"
                />
                <button
                    v-if="media.cover"
                    type="button"
                    class="text-xs font-semibold text-red-700 hover:text-red-900 hover:underline flex items-center gap-1 cursor-pointer"
                    @click="remove(media.cover)"
                >
                    <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Remove</span>
                </button>
            </div>

            <p v-if="busy === 'cover'" class="text-xs font-medium text-amber-800 flex items-center gap-1.5 animate-pulse">
                <svg class="size-3.5 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Uploading…</span>
            </p>
            <p v-if="errors.cover" class="text-xs font-medium text-red-600 flex items-center gap-1.5 bg-red-50 border border-red-200/80 rounded-lg p-2.5">
                <svg class="size-4 shrink-0 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span>{{ errors.cover }}</span>
            </p>
        </div>

        <!-- Gallery Section -->
        <div class="card p-6 grid gap-4">
            <div class="border-b border-stone-100 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-stone-900">Gallery · វិចិត្រសាលរូបភាព ({{ media.gallery.length }})</h2>
                    <p class="text-xs text-stone-500 font-kantumruy" lang="km">រូបថតអនុស្សាវរីយ៍សម្រាប់បង្ហាញក្នុងសំបុត្រ (Up to {{ MAX_IMAGE_MB }} MB/photo)</p>
                </div>
                <span class="inline-flex items-center rounded-full bg-stone-100 px-2.5 py-0.5 text-xs font-bold text-stone-700">
                    {{ media.gallery.length }} photos
                </span>
            </div>

            <!-- Gallery Grid -->
            <ul v-if="media.gallery.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                <li v-for="(photo, i) in media.gallery" :key="photo.id" class="group relative rounded-xl border border-stone-200/90 bg-stone-50 overflow-hidden shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div class="relative aspect-[4/5] w-full overflow-hidden bg-stone-100">
                        <img v-if="photo.url" :src="photo.url" alt="" class="size-full object-cover group-hover:scale-105 transition-transform duration-300" />
                        <div v-else class="size-full flex flex-col items-center justify-center p-2 text-center bg-amber-50/50">
                            <svg class="size-5 animate-spin text-amber-700 mb-1" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-[11px] font-medium text-amber-900">Processing…</span>
                        </div>
                        <span class="absolute top-2 left-2 flex size-5 items-center justify-center rounded-full bg-stone-900/75 text-[10px] font-bold text-white shadow-xs backdrop-blur-xs">
                            {{ i + 1 }}
                        </span>
                    </div>

                    <!-- Photo Toolbar: Move & Remove -->
                    <div class="flex items-center justify-between p-2 gap-1 bg-white border-t border-stone-100">
                        <div class="flex items-center gap-1">
                            <button
                                type="button"
                                :disabled="i === 0"
                                class="flex size-7 items-center justify-center rounded-md border border-stone-200 bg-white text-stone-700 hover:bg-stone-100 hover:text-stone-900 disabled:opacity-25 disabled:cursor-not-allowed transition-colors"
                                @click="move(i, -1)"
                                aria-label="Move earlier"
                                title="Move earlier"
                            >
                                <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button
                                type="button"
                                :disabled="i === media.gallery.length - 1"
                                class="flex size-7 items-center justify-center rounded-md border border-stone-200 bg-white text-stone-700 hover:bg-stone-100 hover:text-stone-900 disabled:opacity-25 disabled:cursor-not-allowed transition-colors"
                                @click="move(i, 1)"
                                aria-label="Move later"
                                title="Move later"
                            >
                                <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <button
                            type="button"
                            class="text-xs font-semibold text-red-700 hover:text-red-900 hover:bg-red-50 rounded px-1.5 py-1 transition-colors cursor-pointer underline"
                            @click="remove(photo)"
                        >
                            Remove
                        </button>
                    </div>
                </li>
            </ul>

            <!-- Multiple Upload Input -->
            <div>
                <input
                    type="file"
                    multiple
                    accept="image/jpeg,image/png,image/webp"
                    :disabled="busy !== null"
                    class="block w-full text-xs text-stone-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border file:border-stone-200 file:text-xs file:font-semibold file:bg-stone-50 file:text-stone-700 hover:file:bg-amber-50 hover:file:border-amber-300 hover:file:text-amber-900 file:transition-colors file:cursor-pointer disabled:opacity-50 cursor-pointer"
                    @change="send('gallery', $event.target.files, $event.target)"
                />
            </div>

            <p v-if="busy === 'gallery'" class="text-xs font-medium text-amber-800 flex items-center gap-1.5 animate-pulse">
                <svg class="size-3.5 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Uploading…</span>
            </p>
            <p v-if="errors.gallery" class="text-xs font-medium text-red-600 flex items-center gap-1.5 bg-red-50 border border-red-200/80 rounded-lg p-2.5">
                <svg class="size-4 shrink-0 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span>{{ errors.gallery }}</span>
            </p>
        </div>

        <!-- Music Section -->
        <div class="card p-6 grid gap-4">
            <div class="border-b border-stone-100 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-stone-900">Music · តន្ត្រី (MP3, up to {{ MAX_MUSIC_MB }} MB)</h2>
                    <p class="text-xs text-stone-500 font-kantumruy" lang="km">បទភ្លេងការប្រពៃណី ឬបទចម្រៀងផ្ទៃខាងក្រោយ (MP3, up to {{ MAX_MUSIC_MB }} MB)</p>
                </div>
                <div class="flex size-7 items-center justify-center rounded-lg bg-amber-100 text-amber-800">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18V5l12-2v13" />
                        <circle cx="6" cy="18" r="3" />
                        <circle cx="18" cy="16" r="3" />
                    </svg>
                </div>
            </div>

            <!-- Audio Player Preview -->
            <div v-if="media.music" class="rounded-xl border border-amber-200/90 bg-gradient-to-r from-amber-50/70 via-stone-50 to-white p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-full bg-gradient-to-br from-[#7B1122] to-[#58111A] text-amber-200 shadow-xs shrink-0">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18V5l12-2v13" />
                            <circle cx="6" cy="18" r="3" />
                            <circle cx="18" cy="16" r="3" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-stone-900">Custom Audio Track Active</p>
                        <p class="text-[11px] text-stone-500 font-kantumruy" lang="km">បទភ្លេងត្រូវបានកំណត់ជោគជ័យ</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <audio :src="media.music.url" controls preload="none" class="h-9 w-full sm:w-64 max-w-full rounded-lg"></audio>
                    <button
                        type="button"
                        class="text-xs font-semibold text-red-700 hover:text-red-900 hover:underline flex items-center gap-1 cursor-pointer shrink-0"
                        @click="remove(media.music)"
                    >
                        <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>Remove</span>
                    </button>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <input
                    type="file"
                    accept="audio/mpeg,.mp3"
                    :disabled="busy !== null"
                    class="block w-full text-xs text-stone-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border file:border-stone-200 file:text-xs file:font-semibold file:bg-stone-50 file:text-stone-700 hover:file:bg-amber-50 hover:file:border-amber-300 hover:file:text-amber-900 file:transition-colors file:cursor-pointer disabled:opacity-50 cursor-pointer"
                    @change="send('music', $event.target.files, $event.target)"
                />
                <button
                    v-if="media.music"
                    type="button"
                    class="text-xs font-semibold text-red-700 hover:text-red-900 hover:underline flex items-center gap-1 cursor-pointer"
                    @click="remove(media.music)"
                >
                    <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Remove</span>
                </button>
            </div>

            <p v-if="busy === 'music'" class="text-xs font-medium text-amber-800 flex items-center gap-1.5 animate-pulse">
                <svg class="size-3.5 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Uploading…</span>
            </p>
            <p v-if="errors.music" class="text-xs font-medium text-red-600 flex items-center gap-1.5 bg-red-50 border border-red-200/80 rounded-lg p-2.5">
                <svg class="size-4 shrink-0 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span>{{ errors.music }}</span>
            </p>
        </div>

        <!-- YouTube Video Section -->
        <div class="card p-6">
            <div class="mb-4 border-b border-stone-100 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-stone-900">Highlights Video · វីដេអូអនុស្សាវរីយ៍</h2>
                    <p class="text-xs text-stone-500 font-kantumruy" lang="km">តំណភ្ជាប់វីដេអូ YouTube សម្រាប់ចាក់ក្នុងសំបុត្រ (ឧទាហរណ៍៖ Pre-wedding ឬ Wedding Highlights)</p>
                </div>
                <div class="flex size-7 items-center justify-center rounded-lg bg-red-100 text-red-700">
                    <svg class="size-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
                    </svg>
                </div>
            </div>
            <Field label="YouTube video link (វីដេអូ)" :error="form.errors.video_url">
                <input v-model="form.video_url" type="url" class="input font-mono text-sm" placeholder="https://youtu.be/…" />
            </Field>
        </div>
    </div>
</template>
