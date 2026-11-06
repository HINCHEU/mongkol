{{-- Shown on a paused (trial over, not paid) or ended invitation. Neutral by rule: no names, dates or photos. --}}
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#7B1122">
    <meta name="robots" content="noindex, nofollow">
    <title>មង្គល · Mongkol</title>

    <!-- Favicon & Touch Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,400..700;1,400..700&family=Moul&family=Playfair+Display:ital,wght@0,500..700;1,400..600&display=swap">

    @vite('resources/css/app.css')

    <style>
        .font-moul { font-family: 'Moul', cursive, serif; }
        .font-kantumruy { font-family: 'Kantumruy Pro', system-ui, sans-serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        :lang(km) { letter-spacing: 0 !important; }
    </style>
</head>
<body class="min-h-dvh bg-[#FAF7F2] text-stone-900 font-sans antialiased grid place-items-center p-4 sm:p-6 selection:bg-amber-100 selection:text-amber-900 relative">

    <!-- Top Royal Khmer Decorative Strip -->
    <div class="fixed top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#7B1122] via-[#D4AF37] to-[#7B1122]"></div>

    <!-- Main Dignified Card -->
    <main class="w-full max-w-md rounded-2xl border border-stone-200/90 bg-white/95 p-6 sm:p-8 text-center shadow-lg backdrop-blur-xs relative overflow-hidden">
        <!-- Subtle Inner Gold Rim -->
        <div class="absolute inset-0 rounded-2xl border border-amber-300/30 pointer-events-none"></div>

        <!-- Royal Lotus Seal Emblem -->
        <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-gradient-to-br from-[#7B1122] to-[#58111A] text-amber-200 shadow-md ring-1 ring-amber-500/30 mb-5">
            <svg class="size-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 3c-2.5 3.5-3 8 0 13c3-5 2.5-9.5 0-13Z" fill="currentColor" fill-opacity="0.25" />
                <path d="M12 16c-4.5-.5-8-3.5-9-8c4 .5 7 2.5 9 6" />
                <path d="M12 16c4.5-.5 8-3.5 9-8c-4 .5-7 2.5-9 6" />
                <circle cx="12" cy="18" r="1.5" fill="currentColor" />
            </svg>
        </div>

        <!-- Neutral heading: the couple's names, date and photos never show here -->
        <h1 class="text-lg sm:text-xl font-normal text-stone-900 font-moul leading-relaxed" lang="km">
            ធៀបការ
        </h1>

        <!-- Delicate Gold Lotus Divider -->
        <div class="flex items-center justify-center gap-2 text-amber-700/60 my-4" aria-hidden="true">
            <div class="h-px w-12 bg-gradient-to-r from-transparent to-amber-400"></div>
            <svg class="size-3.5 text-amber-600" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L14.5 9.5L22 12L14.5 14.5L12 22L9.5 14.5L2 12L9.5 9.5L12 2Z" />
            </svg>
            <div class="h-px w-12 bg-gradient-to-l from-transparent to-amber-400"></div>
        </div>

        <!-- State-Specific Royal Notification -->
        @if ($state === \App\Enums\InvitationState::Ended)
            <div class="space-y-2">
                <p class="text-sm font-semibold text-stone-800 font-kantumruy leading-relaxed" lang="km">
                    ពិធីមង្គលការបានបញ្ចប់ហើយ។ សូមអរគុណចំពោះការចូលរួម!
                </p>
                <p class="text-xs text-stone-500 font-playfair italic leading-relaxed" lang="en">
                    The wedding has passed. Thank you for celebrating with us!
                </p>
            </div>
        @else
            <div class="space-y-2">
                <p class="text-sm font-semibold text-stone-800 font-kantumruy leading-relaxed" lang="km">
                    ធៀបការនេះមិនទាន់អាចមើលបាននៅឡើយទេ។ សូមព្យាយាមម្ដងទៀតនៅពេលក្រោយ។
                </p>
                <p class="text-xs text-stone-500 font-playfair italic leading-relaxed" lang="en">
                    This invitation isn’t available right now. Please try again later.
                </p>
            </div>
        @endif

        <!-- Footer Seal Watermark -->
        <div class="mt-6 pt-5 border-t border-stone-100 flex items-center justify-center gap-1.5 text-stone-400 text-[11px]">
            <span class="font-playfair font-semibold">Mongkol</span>
            <span class="font-kantumruy" lang="km">· មង្គល</span>
        </div>
    </main>

</body>
</html>
