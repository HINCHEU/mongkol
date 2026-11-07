<script setup>
import { computed, provide, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { t } from '../../i18n.js';
import AppLayout from '../../Layouts/AppLayout.vue';
import Couple from '../../Components/Steps/Couple.vue';
import Venues from '../../Components/Steps/Venues.vue';
import Program from '../../Components/Steps/Program.vue';
import Photos from '../../Components/Steps/Photos.vue';
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

const components = { couple: Couple, venues: Venues, program: Program, photos: Photos, gift: Gift, wording: Wording, theme: Theme };

// Steps report their autosave state here; each save reloads the preview.
const status = ref('idle');
const previewVersion = ref(0);
provide('editor', { status, saved: () => previewVersion.value++ });

const statusText = computed(() => t(`editor.status.${status.value}`));

const showPreview = ref(false);
</script>

<template>
    <AppLayout>
        <!-- Editor Top Bar -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-stone-200/80 pb-4">
            <div class="flex items-center gap-3">
                <Link href="/app" class="flex size-8 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-600 hover:text-stone-900 hover:border-stone-300 transition-colors shadow-xs" :title="$t('common.back_to_invitations')">
                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </Link>

                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-stone-900 tracking-tight">/{{ invitation.slug }}</h1>
                        <a :href="previewUrl" target="_blank" class="inline-flex items-center gap-1 rounded-md bg-stone-100 px-2 py-0.5 text-xs font-medium text-stone-700 hover:bg-stone-200 transition-colors" :title="$t('editor.open_preview')">
                            <span>{{ $t('common.open') }}</span>
                            <svg class="size-3" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                            </svg>
                        </a>
                    </div>
                    <p class="text-xs text-stone-600 font-kantumruy" lang="km">{{ invitation.names }}</p>
                </div>
            </div>

            <!-- Autosave Indicator Badge -->
            <div class="flex items-center gap-3">
                <div class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium border"
                     :class="{
                         'bg-amber-50 text-amber-800 border-amber-200': status === 'saving',
                         'bg-emerald-50 text-emerald-800 border-emerald-200': status === 'saved',
                         'bg-red-50 text-red-800 border-red-200': status === 'error',
                         'bg-stone-50 text-stone-600 border-stone-200': status === 'idle',
                     }"
                     role="status">
                    <!-- Status Icon -->
                    <span v-if="status === 'saving'" class="size-2 rounded-full bg-amber-600 animate-ping"></span>
                    <span v-else-if="status === 'saved'" class="size-2 rounded-full bg-emerald-600"></span>
                    <span v-else-if="status === 'error'" class="size-2 rounded-full bg-red-600"></span>
                    <span v-else class="size-2 rounded-full bg-stone-400"></span>

                    <span>{{ statusText }}</span>
                </div>

                <!-- Mobile Preview Trigger Button -->
                <button
                    type="button"
                    class="btn-secondary text-xs !py-1.5 lg:hidden"
                    @click="showPreview = !showPreview"
                >
                    <svg class="size-3.5 text-stone-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="5" y="2" width="14" height="20" rx="3" />
                        <line x1="12" y1="18" x2="12.01" y2="18" stroke-width="3" />
                    </svg>
                    <span>{{ showPreview ? $t('editor.hide_phone') : $t('editor.show_phone') }}</span>
                </button>
            </div>
        </div>

        <!-- Step Navigation Tabs -->
        <nav class="mt-4 flex flex-wrap items-center gap-2" :aria-label="$t('editor.steps_label')">
            <Link
                v-for="name in steps"
                :key="name"
                :href="`/app/invitations/${invitation.id}/edit/${name}`"
                class="inline-flex min-h-[44px] items-center gap-2 rounded-lg border px-3.5 py-2 text-xs sm:text-sm font-medium transition-all duration-150 cursor-pointer"
                :class="name === step
                    ? 'border-[#7B1122] bg-[#7B1122] text-white shadow-xs font-semibold'
                    : 'border-stone-200 bg-white text-stone-700 hover:border-stone-300 hover:bg-stone-50 hover:text-stone-900'"
                :aria-current="name === step ? 'step' : undefined"
            >
                <span>{{ $t(`editor.steps.${name}`) }}</span>
            </Link>

            <!-- Divider separating autosaved steps from standalone pages -->
            <div class="hidden sm:block h-6 w-px bg-stone-300 mx-1" aria-hidden="true"></div>

            <!-- Not autosaved steps: their own dedicated pages -->
            <Link
                :href="`/app/invitations/${invitation.id}/guests`"
                class="inline-flex min-h-[44px] items-center gap-1.5 rounded-lg border border-amber-300/80 bg-amber-50/70 px-3.5 py-2 text-xs sm:text-sm font-semibold text-amber-950 shadow-xs hover:bg-amber-100 hover:border-amber-400 transition-all duration-150 cursor-pointer"
            >
                <svg class="size-4 text-amber-800 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                </svg>
                <span>{{ $t('editor.guests') }}</span>
            </Link>
            <Link
                :href="`/app/invitations/${invitation.id}/share`"
                class="inline-flex min-h-[44px] items-center gap-1.5 rounded-lg border border-amber-300/80 bg-amber-50/70 px-3.5 py-2 text-xs sm:text-sm font-semibold text-amber-950 shadow-xs hover:bg-amber-100 hover:border-amber-400 transition-all duration-150 cursor-pointer"
            >
                <svg class="size-4 text-amber-800 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                </svg>
                <span>{{ $t('editor.share') }}</span>
            </Link>
            <Link
                :href="`/app/invitations/${invitation.id}/responses`"
                class="inline-flex min-h-[44px] items-center gap-1.5 rounded-lg border border-amber-300/80 bg-amber-50/70 px-3.5 py-2 text-xs sm:text-sm font-semibold text-amber-950 shadow-xs hover:bg-amber-100 hover:border-amber-400 transition-all duration-150 cursor-pointer"
            >
                <span>{{ $t('editor.responses') }}</span>
            </Link>
            <!-- Upgrade / Plan Button -->
            <Link
                :href="`/app/invitations/${invitation.id}/upgrade`"
                class="inline-flex min-h-[44px] items-center gap-1.5 rounded-lg border transition-all duration-150 cursor-pointer shadow-xs px-3.5 py-2 text-xs sm:text-sm"
                :class="invitation.state === 'live'
                    ? 'border-stone-200 bg-white text-stone-700 hover:border-stone-300 hover:bg-stone-50 hover:text-stone-900 font-semibold'
                    : 'border-amber-400/80 bg-gradient-to-r from-[#7B1122] to-[#58111A] text-white font-bold hover:from-[#6B0F1D] hover:to-[#4A0B14] shadow-sm hover:shadow ring-1 ring-amber-400/40'"
            >
                <svg v-if="invitation.state === 'live'" class="size-4 text-emerald-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <svg v-else class="size-4 text-amber-300 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                </svg>
                <span>{{ invitation.state === 'live' ? $t('editor.plan') : $t('editor.upgrade') }}</span>
            </Link>
        </nav>

        <!-- Main Editor Workspace: Form (Left) & Phone Mockup (Right) -->
        <div class="mt-6 grid gap-8 lg:grid-cols-[1fr_390px] xl:grid-cols-[1fr_420px] items-start">
            <!-- Form Steps Component Column -->
            <section class="min-w-0">
                <component
                    :is="components[step]"
                    :key="step"
                    :invitation="invitation"
                    :presets="presets"
                    :themes="themes"
                    :lunar-date="lunarDate"
                />
            </section>

            <!-- Phone Frame Live Preview (Sticky on Desktop) -->
            <aside class="sticky top-20 z-10 flex flex-col items-center">
                <div :class="showPreview ? 'block' : 'hidden lg:block'" class="w-full">
                    <!-- Smartphone Mockup Frame -->
                    <div class="relative mx-auto w-[360px] rounded-[48px] border-[10px] border-stone-900 bg-stone-900 shadow-2xl ring-1 ring-stone-950/40">
                        <!-- Top Dynamic Island / Camera Pill -->
                        <div class="absolute top-2.5 left-1/2 z-20 -translate-x-1/2 flex items-center justify-center">
                            <div class="h-4 w-24 rounded-full bg-black shadow-inner flex items-center justify-end pr-3">
                                <span class="size-2 rounded-full bg-stone-900/80"></span>
                            </div>
                        </div>

                        <!-- Iframe Content -->
                        <div class="relative overflow-hidden rounded-[38px] bg-white">
                            <iframe
                                :src="`${previewUrl}?v=${previewVersion}`"
                                :title="$t('editor.preview_title')"
                                class="h-[700px] w-full border-none bg-white"
                            ></iframe>
                        </div>

                        <!-- Bottom Home Indicator Bar -->
                        <div class="absolute bottom-1.5 left-1/2 z-20 -translate-x-1/2">
                            <div class="h-1 w-28 rounded-full bg-stone-700/60"></div>
                        </div>
                    </div>

                    <p class="mt-3 text-center text-xs text-stone-600">
                        {{ $t('editor.live_preview') }}
                    </p>
                </div>
            </aside>
        </div>
    </AppLayout>
</template>