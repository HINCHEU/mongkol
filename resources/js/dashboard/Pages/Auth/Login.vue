<template>
    <main class="flex min-h-screen items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-sm rounded-xl border border-gray-200 bg-white p-6 text-center shadow-sm">
            <h1 class="text-xl font-semibold">Mongkol</h1>
            <p class="mt-1 text-sm text-gray-600">Sign in to create your wedding invitation.</p>

            <p v-if="$page.props.flash.error" role="alert" class="mt-4 rounded-md bg-red-50 px-3 py-2 text-sm text-red-700">
                {{ $page.props.flash.error }}
            </p>

            <!-- A full page load, not an Inertia visit: this redirects off-site to Google. -->
            <a
                href="/auth/google"
                class="mt-6 flex w-full items-center justify-center gap-2 rounded-md bg-gray-900 px-4 py-2.5 font-medium text-white hover:bg-gray-800"
            >
                Continue with Google
            </a>
        </div>
    </main>
</template>
