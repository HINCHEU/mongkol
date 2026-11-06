<script setup>
import { computed, watchEffect } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);
// Switching language is an Inertia visit, not a page load, so keep <html lang> in step for fonts and screen readers.
watchEffect(() => (document.documentElement.lang = page.props.locale));
const locales = [
    { code: 'km', label: 'ខ្មែរ' },
    { code: 'en', label: 'EN' },
];
</script>

<template>
    <div class="min-h-screen bg-[#FAF7F2] text-stone-900 font-sans">
        <!-- Luxury Atelier Navigation Bar -->
        <header class="sticky top-0 z-30 border-b border-stone-200/80 bg-white/90 backdrop-blur-md">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
                <!-- Brand Identity -->
                <Link href="/app" class="group flex items-center gap-2.5 transition-transform active:scale-[0.98]">
                    <!-- Mini Golden Wedding Seal Motif -->
                    <div class="flex size-9 items-center justify-center rounded-lg bg-gradient-to-br from-[#7B1122] to-[#58111A] text-amber-200 shadow-xs ring-1 ring-amber-500/30">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3c-2.5 3.5-3 8 0 13c3-5 2.5-9.5 0-13Z" fill="currentColor" fill-opacity="0.2" />
                            <path d="M12 16c-4.5-.5-8-3.5-9-8c4 .5 7 2.5 9 6" />
                            <path d="M12 16c4.5-.5 8-3.5 9-8c-4 .5-7 2.5-9 6" />
                            <circle cx="12" cy="18" r="1.5" fill="currentColor" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <span class="text-lg font-bold tracking-tight text-stone-900 group-hover:text-[#7B1122] transition-colors">Mongkol</span>
                            <span class="text-xs font-semibold text-amber-700 font-kantumruy" lang="km">មង្គល</span>
                        </div>
                        <span class="text-[10px] font-medium uppercase tracking-wider text-stone-600">{{ $t('layout.tagline') }}</span>
                    </div>
                </Link>

                <!-- Right Navigation Elements -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <!-- Language Switcher Pill -->
                    <div class="flex overflow-hidden rounded-full border border-stone-200 bg-stone-100/90 p-0.5 text-xs font-semibold shadow-xs" role="group" :aria-label="$t('layout.language')">
                        <Link
                            v-for="locale in locales"
                            :key="locale.code"
                            href="/app/locale"
                            method="put"
                            :data="{ locale: locale.code }"
                            as="button"
                            preserve-scroll
                            :aria-pressed="page.props.locale === locale.code"
                            class="rounded-full px-3 py-1 transition-all duration-150 cursor-pointer"
                            :class="page.props.locale === locale.code ? 'bg-[#7B1122] text-white shadow-xs' : 'text-stone-600 hover:text-stone-900'"
                        >
                            {{ locale.label }}
                        </Link>
                    </div>

                    <!-- User Profile & Log Out -->
                    <div class="flex items-center gap-2.5 border-l border-stone-200/80 pl-3 sm:pl-4">
                        <img
                            v-if="user?.avatar_url"
                            :src="user.avatar_url"
                            :alt="user.name"
                            class="size-8 rounded-full object-cover ring-2 ring-amber-500/20"
                            referrerpolicy="no-referrer"
                        />
                        <div v-else class="flex size-8 items-center justify-center rounded-full bg-stone-200 text-xs font-bold text-stone-700">
                            {{ user?.name ? user.name.charAt(0) : 'U' }}
                        </div>

                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="text-xs font-medium text-stone-700 hover:text-[#7B1122] transition-colors cursor-pointer"
                        >
                            {{ $t('layout.logout') }}
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main View Container -->
        <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-8">
            <slot />
        </main>
    </div>
</template>