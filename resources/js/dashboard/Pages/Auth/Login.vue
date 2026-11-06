<template>
    <main class="flex min-h-screen items-center justify-center bg-[#FAF7F2] px-4 font-sans selection:bg-amber-100 selection:text-amber-900">
        <!-- Login Card -->
        <div class="w-full max-w-md rounded-2xl border border-stone-200/90 bg-white p-8 text-center shadow-md">
            <!-- Royal Seal Emblem -->
            <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-gradient-to-br from-[#7B1122] to-[#58111A] text-amber-200 shadow-md ring-2 ring-amber-500/30">
                <svg class="size-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3c-2.5 3.5-3 8 0 13c3-5 2.5-9.5 0-13Z" fill="currentColor" fill-opacity="0.2" />
                    <path d="M12 16c-4.5-.5-8-3.5-9-8c4 .5 7 2.5 9 6" />
                    <path d="M12 16c4.5-.5 8-3.5 9-8c-4 .5-7 2.5-9 6" />
                    <circle cx="12" cy="18" r="1.5" fill="currentColor" />
                </svg>
            </div>

            <h1 class="mt-4 text-2xl font-bold tracking-tight text-stone-900">Mongkol · <span class="font-kantumruy font-semibold text-amber-800" lang="km">មង្គល</span></h1>
            <p class="mt-1 text-sm text-stone-600 font-kantumruy">{{ $t('login.subtitle') }}</p>

            <!-- Flash Error Message -->
            <p v-if="$page.props.flash?.error" role="alert" class="mt-4 rounded-lg bg-red-50 p-3 text-xs font-medium text-red-700 border border-red-200 text-left">
                {{ $page.props.flash.error }}
            </p>

            <!-- Google Sign-In Action -->
            <a
                href="/auth/google"
                class="mt-6 flex w-full items-center justify-center gap-3 rounded-xl border border-stone-300 bg-white px-4 py-3 text-sm font-semibold text-stone-800 shadow-xs transition-all duration-150 hover:bg-stone-50 hover:border-stone-400 hover:shadow-sm active:scale-[0.98] cursor-pointer"
            >
                <svg class="size-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.65v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.14z"/>
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.34 24 12 24z"/>
                    <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.34 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                </svg>
                <span>{{ $t('login.google') }}</span>
            </a>

            <p class="mt-6 text-xs text-stone-600">
                {{ $t('login.secure') }}
            </p>
        </div>
    </main>
</template>