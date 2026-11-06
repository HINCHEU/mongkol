<div class="relative overflow-hidden rounded-xl border border-[#C5A059]/35 bg-white dark:bg-stone-900 shadow-[0_4px_24px_-2px_rgba(88,17,26,0.05)] p-5 sm:p-6 mb-2">
    <!-- Subtle Top Gold Accent Line -->
    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-[#D4AF37] to-transparent"></div>

    <!-- Background Subtle Watermark Lotus -->
    <div class="absolute -right-6 -bottom-8 opacity-[0.04] dark:opacity-[0.06] pointer-events-none">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-48 h-48 text-[#7B1122] dark:text-[#D4AF37]" fill="currentColor">
            <path d="M12 3c-2.5 3.5-3 8 0 13c3-5 2.5-9.5 0-13Z" />
            <path d="M12 16c-4.5-.5-8-3.5-9-8c4 .5 7 2.5 9 6" />
            <path d="M12 16c4.5-.5 8-3.5 9-8c-4 .5-7 2.5-9 6" />
            <circle cx="12" cy="18" r="1.5" />
        </svg>
    </div>

    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Left: Atelier Greeting & Title -->
        <div class="flex items-start sm:items-center gap-4">
            <div class="relative flex items-center justify-center w-12 h-12 shrink-0 rounded-2xl bg-gradient-to-br from-[#8B1426] via-[#7B1122] to-[#4C0A13] shadow-[0_4px_14px_rgba(123,17,34,0.35)] ring-1 ring-[#D4AF37]/50">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-7 h-7 text-[#F5DE93]" fill="currentColor">
                    <path d="M12 3c-2.5 3.5-3 8 0 13c3-5 2.5-9.5 0-13Z" fill="#F5DE93" fill-opacity="0.35" stroke="#F5DE93" stroke-width="1.5" stroke-linejoin="round" />
                    <path d="M12 16c-4.5-.5-8-3.5-9-8c4 .5 7 2.5 9 6" fill="none" stroke="#F5DE93" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M12 16c4.5-.5 8-3.5 9-8c-4 .5-7 2.5-9 6" fill="none" stroke="#F5DE93" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <circle cx="12" cy="18" r="1.4" fill="#F5DE93" />
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold uppercase tracking-[0.15em] text-[#8C6D23] dark:text-[#C5A059]">រាជរោងមង្គល · Royal Atelier</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live Operations
                    </span>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold font-['Playfair_Display',serif] text-stone-900 dark:text-stone-100 tracking-tight mt-0.5">
                    Welcome back, {{ $user?->name ?? 'Admin' }}
                </h2>
                <p class="text-xs text-stone-500 dark:text-stone-400 mt-0.5 font-['Kantumruy_Pro',sans-serif]">
                    Phnom Penh Time: <span class="font-medium text-stone-700 dark:text-stone-300">{{ $phnomPenhTime }}</span>
                </p>
            </div>
        </div>

        <!-- Right: Fast Actions & Status Pills -->
        <div class="flex flex-wrap items-center gap-2 pt-2 md:pt-0">
            @if ($pendingPayments > 0)
                <a href="{{ route('filament.admin.resources.payments.index') }}"
                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-50 dark:bg-amber-950/50 text-amber-800 dark:text-amber-200 border border-amber-300 dark:border-amber-700 hover:bg-amber-100 dark:hover:bg-amber-900/60 transition shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                    <span>{{ $pendingPayments }} KHQR {{ $pendingPayments === 1 ? 'Payment' : 'Payments' }} to Review</span>
                </a>
            @endif

            <a href="{{ route('filament.admin.resources.invitations.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-stone-700 dark:text-stone-200 bg-stone-50 dark:bg-stone-800 border border-stone-200 dark:border-stone-700 hover:bg-[#FAF6EE] dark:hover:bg-stone-700/60 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#7B1122] dark:text-[#E87280]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Invitations ({{ $liveInvitations }} Live)</span>
            </a>

            <a href="{{ route('filament.admin.pages.settings') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-stone-700 dark:text-stone-200 bg-stone-50 dark:bg-stone-800 border border-stone-200 dark:border-stone-700 hover:bg-[#FAF6EE] dark:hover:bg-stone-700/60 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#8C6D23] dark:text-[#C5A059]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Settings</span>
            </a>
        </div>
    </div>
</div>
