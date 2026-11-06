<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

defineProps({ invitations: Array });

const stateBadges = {
    trial: { label: 'Free Trial · សាកល្បង', class: 'bg-rose-50 text-rose-800 border-rose-200/70' },
    live: { label: 'Live · ផ្សាយផ្ទាល់', class: 'bg-emerald-50 text-emerald-800 border-emerald-200/70' },
    paused: { label: 'Paused · បានផ្អាក', class: 'bg-amber-50 text-amber-800 border-amber-200/70' },
    ended: { label: 'Ended · បានបញ្ចប់', class: 'bg-stone-100 text-stone-600 border-stone-200' },
    suspended: { label: 'Suspended · ផ្អាកបណ្តោះអាសន្ន', class: 'bg-red-50 text-red-800 border-red-200' },
};
</script>

<template>
    <AppLayout>
        <!-- Page Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-stone-200/70 pb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-stone-900 sm:text-3xl">My invitations</h1>
                <p class="mt-1 text-sm text-stone-700 font-kantumruy" lang="km">គ្រប់គ្រង និងកែសម្រួលសំបុត្រអាពាហ៍ពិពាហ៍អេឡិចត្រូនិករបស់អ្នក</p>
            </div>
            <Link
                href="/app/invitations/create"
                class="btn-primary inline-flex items-center gap-2 self-start sm:self-auto"
            >
                <svg class="size-4 text-amber-200" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                <span>New invitation · បង្កើតសំបុត្រថ្មី</span>
            </Link>
        </div>

        <!-- Empty State -->
        <div v-if="!invitations.length" class="card mt-8 p-12 text-center border-dashed border-stone-300">
            <div class="mx-auto flex size-14 items-center justify-center rounded-full bg-amber-50 text-amber-700 ring-1 ring-amber-500/20">
                <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M12 3c-2.5 3.5-3 8 0 13c3-5 2.5-9.5 0-13Z" />
                    <path d="M12 16c-4.5-.5-8-3.5-9-8c4 .5 7 2.5 9 6" />
                    <path d="M12 16c4.5-.5 8-3.5 9-8c-4 .5-7 2.5-9 6" />
                    <circle cx="12" cy="18" r="1.5" fill="currentColor" />
                </svg>
            </div>
            <h2 class="mt-4 text-lg font-semibold text-stone-900">No invitations yet</h2>
            <p class="mt-1 text-sm text-stone-600 max-w-sm mx-auto font-kantumruy" lang="km">មិនទាន់មានសំបុត្រអញ្ជើញនៅឡើយទេ។ ចាប់ផ្តើមបង្កើតសំបុត្រដំបូងរបស់អ្នក ដើម្បីមើលជាមុនដោយឥតគិតថ្លៃ។</p>
            <div class="mt-6">
                <Link href="/app/invitations/create" class="btn-primary">
                    Create your first invitation
                </Link>
            </div>
        </div>

        <!-- Invitations Cards List -->
        <div v-else class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="invitation in invitations"
                :key="invitation.id"
                class="card group relative flex flex-col justify-between border-stone-200/90 hover:border-amber-500/60 hover:shadow-md transition-all duration-200"
            >
                <div>
                    <!-- Status Badge & Link -->
                    <div class="flex items-center justify-between gap-2">
                        <span
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold border"
                            :class="stateBadges[invitation.state]?.class ?? 'bg-stone-100 text-stone-600 border-stone-200'"
                        >
                            {{ stateBadges[invitation.state]?.label ?? invitation.state }}
                        </span>

                        <span class="text-xs font-mono font-medium text-stone-600">/{{ invitation.slug }}</span>
                    </div>

                    <!-- Couple Names in Khmer -->
                    <h2 class="mt-4 text-xl font-bold text-stone-900 font-kantumruy group-hover:text-[#7B1122] transition-colors" lang="km">
                        {{ invitation.names }}
                    </h2>

                    <!-- Wedding Date -->
                    <div class="mt-2.5 flex items-center gap-2 text-sm text-stone-700">
                        <svg class="size-4 text-amber-700 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ invitation.wedding_date }}</span>
                    </div>
                </div>

                <!-- Action Links -->
                <div class="mt-6 flex items-center gap-2 border-t border-stone-100 pt-4">
                    <Link
                        :href="`/app/invitations/${invitation.id}/edit`"
                        class="btn-primary w-full text-center text-xs py-2"
                    >
                        Edit · កែសម្រួល
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>