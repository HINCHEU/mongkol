<script setup>
import { computed, provide, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import Couple from '../../Components/Steps/Couple.vue';
import Venues from '../../Components/Steps/Venues.vue';
import Program from '../../Components/Steps/Program.vue';
import Gift from '../../Components/Steps/Gift.vue';
import Wording from '../../Components/Steps/Wording.vue';
import Theme from '../../Components/Steps/Theme.vue';

const props = defineProps({
    step: String,
    steps: Array,
    invitation: Object,
    lunarDate: String,
    presets: Array,
    themes: Array,
    previewUrl: String,
    publicUrl: String,
});

const components = { couple: Couple, venues: Venues, program: Program, gift: Gift, wording: Wording, theme: Theme };
const labels = { couple: 'Couple & parents', venues: 'Venues', program: 'Date & program', gift: 'Gift', wording: 'Wording', theme: 'Theme' };

// Steps report their autosave state here; each save reloads the preview.
const status = ref('idle');
const previewVersion = ref(0);
provide('editor', { status, saved: () => previewVersion.value++ });

const statusText = computed(() => ({ idle: '', saving: 'Saving…', saved: 'Saved', error: 'Not saved — check the fields marked in red' })[status.value]);
const showPreview = ref(false);
</script>

<template>
    <AppLayout>
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h1 class="text-xl font-semibold">/{{ invitation.slug }}</h1>
            <p class="text-sm" :class="status === 'error' ? 'text-red-700' : 'text-gray-600'" role="status">{{ statusText }}</p>
        </div>

        <nav class="mt-4 flex flex-wrap gap-2" aria-label="Editor steps">
            <Link v-for="name in steps" :key="name" :href="`/app/invitations/${invitation.id}/edit/${name}`"
                  class="rounded-md border px-3 py-1.5 text-sm" :class="name === step ? 'border-gray-900 bg-gray-900 text-white' : 'border-gray-300 bg-white'"
                  :aria-current="name === step ? 'step' : undefined">
                {{ labels[name] }}
            </Link>
        </nav>

        <div class="mt-6 grid gap-8 lg:grid-cols-[1fr_auto]">
            <section>
                <component :is="components[step]" :key="step" :invitation="invitation" :presets="presets" :themes="themes" :lunar-date="lunarDate" />
            </section>

            <aside>
                <button type="button" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm lg:hidden" @click="showPreview = !showPreview">
                    {{ showPreview ? 'Hide preview' : 'Show preview' }}
                </button>
                <div :class="showPreview ? 'block' : 'hidden lg:block'" class="mt-3 lg:mt-0">
                    <iframe :src="`${previewUrl}?v=${previewVersion}`" title="Invitation preview"
                            class="h-[740px] w-full max-w-[390px] rounded-3xl border-8 border-gray-900 bg-white"></iframe>
                </div>
            </aside>
        </div>
    </AppLayout>
</template>
