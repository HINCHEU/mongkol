<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
</script>

<template>
    <AppLayout>
        <h1 class="text-2xl font-semibold">Hello, {{ $page.props.auth.user.name }}</h1>
        <p class="mt-2 text-gray-600">Your invitations will appear here.</p>
    </AppLayout>
</template>
