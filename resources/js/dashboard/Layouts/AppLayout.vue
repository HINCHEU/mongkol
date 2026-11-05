<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);
const locales = [
    { code: 'km', label: 'ខ្មែរ' },
    { code: 'en', label: 'EN' },
];
</script>

<template>
    <div class="min-h-screen bg-gray-50 text-gray-900">
        <header class="flex items-center justify-between gap-3 border-b border-gray-200 bg-white px-4 py-3">
            <Link href="/app" class="font-semibold">Mongkol</Link>

            <div class="flex items-center gap-3">
                <div class="flex overflow-hidden rounded-md border border-gray-300 text-sm" role="group" aria-label="Language">
                    <Link
                        v-for="locale in locales"
                        :key="locale.code"
                        href="/app/locale"
                        method="put"
                        :data="{ locale: locale.code }"
                        as="button"
                        preserve-scroll
                        :aria-pressed="page.props.locale === locale.code"
                        class="px-3 py-1.5"
                        :class="page.props.locale === locale.code ? 'bg-gray-900 text-white' : 'hover:bg-gray-100'"
                    >
                        {{ locale.label }}
                    </Link>
                </div>

                <img
                    v-if="user.avatar_url"
                    :src="user.avatar_url"
                    :alt="user.name"
                    class="size-8 rounded-full"
                    referrerpolicy="no-referrer"
                />

                <Link href="/logout" method="post" as="button" class="text-sm text-gray-600 hover:text-gray-900">
                    Log out
                </Link>
            </div>
        </header>

        <main class="mx-auto max-w-3xl p-4">
            <slot />
        </main>
    </div>
</template>
