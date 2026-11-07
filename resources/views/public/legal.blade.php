{{-- Privacy, terms and refunds. Calm, editorial, phone-first legal stationery. --}}
<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light">
    <title>{{ $text['title'] }} · Mongkol</title>

    <!-- Favicon & Touch Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite('resources/css/app.css')

    <style>
        :root {
            color-scheme: light;
        }
        body {
            font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', system-ui, -apple-system, sans-serif;
            background-color: #FAF6EE;
            background-image:
                radial-gradient(ellipse at 50% 0%, rgba(197, 160, 89, 0.08) 0%, transparent 65%),
                radial-gradient(ellipse at 50% 100%, rgba(123, 17, 34, 0.04) 0%, transparent 60%);
            background-attachment: fixed;
            color: #1A1412;
        }
        :lang(km) {
            letter-spacing: 0 !important;
            text-transform: none !important;
        }
        .legal-body p {
            line-height: 1.9;
        }
    </style>
</head>
<body class="min-h-dvh text-[#1A1412] antialiased">
    <!-- Top Progress / Gold Hairline -->
    <div class="h-1 w-full bg-gradient-to-r from-transparent via-[#D4AF37]/60 to-transparent"></div>

    <main class="mx-auto max-w-3xl px-4 py-6 sm:py-12">
        <!-- Top Navigation Bar -->
        <nav class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-[#C5A059]/30">
            <!-- Brand Link -->
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 min-h-[44px] group focus:outline-none focus-visible:ring-2 focus-visible:ring-[#7B1122] rounded-xl px-1">
                <img src="/icon-192.png" width="34" height="34" alt="Mongkol Logo" class="size-[34px] rounded-lg shadow-xs group-hover:scale-105 transition-transform" />
                <span class="font-bold text-base sm:text-lg text-stone-900 group-hover:text-[#7B1122] transition-colors">
                    Mongkol · <span lang="km" class="font-semibold text-[#C25E1A]">មង្គល</span>
                </span>
            </a>

            <!-- Bilingual Switcher (min 44px touch targets) -->
            <div class="inline-flex items-center p-1 rounded-full bg-white/90 border border-[#C5A059]/40 shadow-xs" role="group" aria-label="Language selection">
                <a href="?lang=km"
                   lang="km"
                   @if ($lang === 'km') aria-current="page" class="inline-flex items-center justify-center min-h-[44px] min-w-[56px] px-3.5 py-1.5 rounded-full text-xs font-bold bg-[#7B1122] text-white shadow-xs"
                   @else class="inline-flex items-center justify-center min-h-[44px] min-w-[56px] px-3.5 py-1.5 rounded-full text-xs font-medium text-stone-600 hover:text-stone-900 hover:bg-[#FAF6EE] transition-colors" @endif>
                    ខ្មែរ
                </a>
                <a href="?lang=en"
                   lang="en"
                   @if ($lang === 'en') aria-current="page" class="inline-flex items-center justify-center min-h-[44px] min-w-[56px] px-3.5 py-1.5 rounded-full text-xs font-bold bg-[#7B1122] text-white shadow-xs"
                   @else class="inline-flex items-center justify-center min-h-[44px] min-w-[56px] px-3.5 py-1.5 rounded-full text-xs font-medium text-stone-600 hover:text-stone-900 hover:bg-[#FAF6EE] transition-colors" @endif>
                    English
                </a>
            </div>
        </nav>

        <!-- Main Stationery Paper Sheet -->
        <article class="mt-6 sm:mt-8 rounded-2xl sm:rounded-3xl bg-white border border-[#C5A059]/35 p-6 sm:p-12 shadow-sm relative overflow-hidden">
            <!-- Decorative Subtle Gold Corner Accents -->
            <div class="pointer-events-none absolute top-0 left-0 w-24 h-24 bg-radial from-[#D4AF37]/10 to-transparent"></div>
            <div class="pointer-events-none absolute bottom-0 right-0 w-24 h-24 bg-radial from-[#7B1122]/5 to-transparent"></div>

            <!-- Page Header -->
            <header class="pb-6 border-b border-stone-100">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#FAF6EE] border border-[#C5A059]/30 text-[11px] font-semibold text-[#8C6D23] mb-3">
                    <span class="size-1.5 rounded-full bg-[#8C6D23]"></span>
                    <span>
                        @if ($page === 'privacy')
                            {{ $lang === 'km' ? 'ឯកជនភាព និងសុវត្ថិភាព' : 'Privacy & Data Protection' }}
                        @elseif ($page === 'terms')
                            {{ $lang === 'km' ? 'កិច្ចព្រមព្រៀងសេវាកម្ម' : 'Terms of Service' }}
                        @else
                            {{ $lang === 'km' ? 'គោលការណ៍ទូទាត់' : 'Payment & Refunds' }}
                        @endif
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-stone-900 leading-snug">
                    {{ $text['title'] }}
                </h1>

                <div class="mt-2.5 flex items-center gap-2 text-xs sm:text-sm text-stone-500 font-medium">
                    <svg class="size-4 text-[#8C6D23] shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ $common['updated'] }}</span>
                </div>
            </header>

            <!-- Document Intro (if present) -->
            @if (! empty($text['intro']))
                <div class="mt-6 rounded-xl bg-[#FAF6EE]/80 border border-[#C5A059]/25 p-4 sm:p-5 text-stone-800 text-sm sm:text-base leading-[1.85] font-medium">
                    <p>{{ $text['intro'] }}</p>
                </div>
            @endif

            <!-- Document Sections -->
            <div class="mt-8 space-y-8 legal-body">
                @foreach ($text['sections'] as $index => $section)
                    <section class="space-y-3 pt-2">
                        <div class="flex items-baseline gap-2.5">
                            <span class="inline-flex items-center justify-center size-6 rounded-full bg-[#FAF6EE] text-[#8C6D23] border border-[#C5A059]/40 text-xs font-bold shrink-0">
                                {{ $index + 1 }}
                            </span>
                            <h2 class="text-lg sm:text-xl font-bold text-stone-900 leading-snug">
                                {{ $section['heading'] }}
                            </h2>
                        </div>

                        <div class="pl-8 sm:pl-9 space-y-3.5 text-stone-700 text-sm sm:text-base">
                            @foreach ($section['body'] as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            <!-- Contact Box -->
            <div class="mt-10 rounded-2xl bg-[#FAF6EE]/70 border border-[#C5A059]/30 p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-white border border-[#C5A059]/40 flex items-center justify-center text-[#7B1122] shadow-xs shrink-0">
                        <svg class="size-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                            <path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm font-semibold text-stone-900">{{ $common['contact'] }}</p>
                        <p class="text-xs text-stone-500">{{ $lang === 'km' ? 'ឆ្លើយតបក្នុងរយៈពេល ២៤ ម៉ោង' : 'We respond within 24 hours' }}</p>
                    </div>
                </div>

                <a href="mailto:{{ config('app.contact_email') }}"
                   class="inline-flex items-center justify-center gap-2 min-h-[44px] px-4 py-2 rounded-xl bg-white border border-[#C5A059]/40 text-xs sm:text-sm font-bold text-[#7B1122] hover:bg-[#FAF6EE] hover:border-[#7B1122] transition-all shadow-xs underline">
                    {{ config('app.contact_email') }}
                </a>
            </div>

            <!-- Legal Documents Switcher / Navigation Footer -->
            <footer class="mt-10 pt-6 border-t border-stone-200">
                <p class="text-xs font-bold uppercase tracking-wider text-[#8C6D23] mb-3">
                    {{ $lang === 'km' ? 'ឯកសារច្បាប់ផ្សេងទៀត' : 'Legal Documents' }}
                </p>

                <div class="flex flex-wrap gap-2.5 sm:gap-3 text-xs sm:text-sm">
                    @foreach (\App\Http\Controllers\Public\LegalController::PAGES as $other)
                        <a href="{{ route('legal', $other) }}?lang={{ $lang }}"
                           @if ($other === $page) aria-current="page" class="inline-flex items-center min-h-[44px] px-4 py-2 rounded-xl font-bold bg-[#7B1122] text-white shadow-xs"
                           @else class="inline-flex items-center min-h-[44px] px-4 py-2 rounded-xl font-medium bg-[#FAF6EE] text-stone-700 border border-[#C5A059]/30 hover:border-[#7B1122] hover:text-[#7B1122] transition-colors" @endif>
                            {{ trans("legal.{$other}.title", [], $lang) }}
                        </a>
                    @endforeach
                </div>
            </footer>
        </article>

        <!-- Brand Footer Note -->
        <div class="mt-8 text-center text-xs text-stone-500 space-y-1 pb-8">
            <p>&copy; {{ date('Y') }} Mongkol · {{ $lang === 'km' ? 'រក្សាសិទ្ធិគ្រប់យ៉ាង' : 'All rights reserved.' }}</p>
            <p class="text-[11px] text-stone-400">
                <a href="{{ url('/') }}" class="hover:underline">{{ $lang === 'km' ? 'ត្រឡប់ទៅទំព័រដើម' : 'Back to Home' }}</a>
            </p>
        </div>
    </main>
</body>
</html>
