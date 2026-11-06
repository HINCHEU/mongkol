<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

defineProps({ invitations: Array });
</script>

<template>
    <AppLayout>
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold">My invitations</h1>
            <Link href="/app/invitations/create" class="rounded-md bg-gray-900 px-4 py-2 text-white">New invitation</Link>
        </div>

        <p v-if="!invitations.length" class="mt-6 text-gray-600">No invitations yet. Create one to start your free preview.</p>

        <ul v-else class="mt-6 grid gap-3">
            <li v-for="invitation in invitations" :key="invitation.id">
                <Link :href="`/app/invitations/${invitation.id}/edit`" class="block rounded-md border border-gray-200 bg-white p-4 hover:border-gray-400">
                    <p class="font-medium" lang="km">{{ invitation.names }}</p>
                    <p class="mt-1 text-sm text-gray-600">{{ invitation.wedding_date }} · /{{ invitation.slug }} · {{ invitation.state }}</p>
                </Link>
            </li>
        </ul>
    </AppLayout>
</template>
