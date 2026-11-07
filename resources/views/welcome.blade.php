<!DOCTYPE html>
<html lang="km" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#7B1122">
    <title>Mongkol · មង្គល — សំបុត្រអាពាហ៍ពិពាហ៍បែបប្រពៃណីខ្មែរ និងទំនើបកម្ម</title>
    <meta name="description" content="វេទិកាសំបុត្រអាពាហ៍ពិពាហ៍អេឡិចត្រូនិកបែបប្រពៃណីខ្មែរដំបូងគេបង្អស់។ ស្រោមសំបុត្រ 3D បែបប្រណិត កាលបរិច្ឆេទចន្ទគតិខ្មែរ កាលវិភាគពិធីទាំង ៦ និងប្រព័ន្ធចំណងដៃ KHQR ងាយស្រួលស្កេនពីគ្រប់ទីកន្លែង។">

    <!-- Favicon & Touch Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,300..700;1,300..700&family=Moul&family=Playfair+Display:ital,wght@0,500..700;1,400..600&family=Plus+Jakarta+Sans:ital,wght@0,400..700;1,400..700&display=swap">

    @vite(['resources/css/app.css', 'resources/js/welcome.js'])

    <style>
        :root {
            --gold-leaf: linear-gradient(135deg, #FFE8A3 0%, #D4AF37 35%, #B38622 70%, #F5DE93 100%);
            --crimson-royal: linear-gradient(145deg, #7B1122 0%, #58111A 60%, #38070F 100%);
        }
        .font-moul { font-family: 'Moul', cursive, serif; }
        .font-kantumruy { font-family: 'Kantumruy Pro', system-ui, sans-serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        
        .gold-shimmer {
            background: linear-gradient(135deg, #F5DE93 0%, #D4AF37 40%, #A88B4A 70%, #F5DE93 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gold-border-gradient {
            border-image: linear-gradient(135deg, #D4AF37, #F5DE93, #8C6D23) 1;
        }
        .shadow-royal {
            box-shadow: 0 16px 36px -6px rgba(123, 17, 34, 0.15), 0 4px 16px -2px rgba(140, 109, 35, 0.1);
        }
        .shadow-seal-glow {
            box-shadow: 0 0 25px rgba(212, 175, 55, 0.45);
        }
        
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.15); opacity: 0.2; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
        .pulse-gold {
            animation: pulse-ring 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(1.5deg); }
        }
        .animate-float {
            animation: float-slow 6s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-[#FAF7F2] text-stone-900 font-sans antialiased selection:bg-amber-100 selection:text-amber-900 overflow-x-hidden relative">

    <!-- Falling Petals Interactive Canvas -->
    <canvas id="petals-canvas" class="fixed inset-0 pointer-events-none z-20"></canvas>

    <!-- Top Decorative Khmer Royal Border Strip -->
    <div class="h-1.5 w-full bg-gradient-to-r from-[#7B1122] via-[#D4AF37] to-[#7B1122]"></div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-30 border-b border-stone-200/80 bg-[#FAF7F2]/90 backdrop-blur-md transition-all duration-200">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3.5 sm:px-6 lg:px-8">
            <!-- Brand Logo -->
            <a href="/" class="group flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-[#7B1122] to-[#58111A] text-amber-200 shadow-md ring-1 ring-amber-500/30 group-hover:scale-105 transition-transform">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3c-2.5 3.5-3 8 0 13c3-5 2.5-9.5 0-13Z" fill="currentColor" fill-opacity="0.25" />
                        <path d="M12 16c-4.5-.5-8-3.5-9-8c4 .5 7 2.5 9 6" />
                        <path d="M12 16c4.5-.5 8-3.5 9-8c-4 .5-7 2.5-9 6" />
                        <circle cx="12" cy="18" r="1.5" fill="currentColor" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <div class="flex items-center gap-1.5">
                        <span class="text-xl font-bold tracking-tight text-stone-900 font-playfair group-hover:text-[#7B1122] transition-colors">Mongkol</span>
                        <span class="text-sm font-semibold text-amber-800 font-kantumruy" lang="km">មង្គល</span>
                    </div>
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-stone-500">Royal Khmer E-Invitation</span>
                </div>
            </a>

            <!-- Navigation Links (Desktop) -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-stone-700 font-kantumruy">
                <a href="#features" class="hover:text-[#7B1122] transition-colors">លក្ខណៈពិសេស</a>
                <a href="#ceremonies" class="hover:text-[#7B1122] transition-colors">ពិធីការទាំង ៦</a>
                <a href="#themes" class="hover:text-[#7B1122] transition-colors">គំរូសំបុត្រ (Themes)</a>
                <a href="#how-it-works" class="hover:text-[#7B1122] transition-colors">របៀបបង្កើត</a>
                <a href="#faq" class="hover:text-[#7B1122] transition-colors">សំណួរញឹកញាប់</a>
            </nav>

            <!-- Auth Buttons -->
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary text-xs !py-2 !px-4">
                        <span>ផ្ទាំងគ្រប់គ្រង (Dashboard)</span>
                        <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-semibold text-stone-700 hover:text-[#7B1122] px-3 py-2 transition-colors">
                        ចូលគណនី (Log In)
                    </a>
                    <a href="{{ route('login') }}" class="btn-primary text-xs !py-2 !px-4 shadow-sm">
                        <span>បង្កើតសំបុត្រឥតគិតថ្លៃ</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="relative pt-12 pb-20 sm:pt-16 sm:pb-28 overflow-hidden">
        <!-- Background Radial Luxury Glows -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[500px] bg-gradient-to-b from-amber-200/20 via-[#7B1122]/5 to-transparent rounded-full blur-3xl pointer-events-none -z-10"></div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                <!-- Left Content: Hero Text -->
                <div class="lg:col-span-7 text-center lg:text-left space-y-6">
                    <!-- Auspicious Ceremonial Crest -->
                    <div class="inline-flex items-center gap-2 rounded-full border border-amber-300 bg-amber-50/80 px-4 py-1.5 shadow-xs">
                        <span class="flex size-2 rounded-full bg-amber-600 animate-pulse"></span>
                        <span class="text-xs font-bold text-amber-900 font-kantumruy" lang="km">សិរីសួស្តី ជ័យមង្គល វិបុលសុខ មហាប្រសើរ</span>
                        <span class="text-amber-400">·</span>
                        <span class="text-[11px] font-semibold text-amber-800 tracking-wide">ROYAL KHMER WEDDING STATIONERY</span>
                    </div>

                    <!-- Grand Headline in Moul Script -->
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-stone-900 leading-[1.3] sm:leading-[1.25]">
                        <span class="font-moul block text-stone-900 leading-relaxed sm:leading-[1.4]" lang="km">
                            ប្រណិតភាពនៃពិធីមង្គលការខ្មែរ ក្នុងយុគសម័យឌីជីថល
                        </span>
                        <span class="font-playfair italic font-medium text-2xl sm:text-3xl lg:text-4xl text-[#7B1122] block mt-2">
                            Where Sacred Tradition Meets Modern Luxury
                        </span>
                    </h1>

                    <!-- Description in Kantumruy Pro -->
                    <p class="text-base sm:text-lg text-stone-700 font-kantumruy leading-relaxed max-w-2xl mx-auto lg:mx-0" lang="km">
                        ផ្ញើសេចក្តីអញ្ជើញដ៏ថ្លៃថ្នូរទៅកាន់ភ្ញៀវកិត្តិយសគ្រប់ទិសទី។ បទពិសោធន៍បើកស្រោមសំបុត្រ 3D បែបប្រពៃណី ត្រាមង្គលមាស កាលបរិច្ឆេទចន្ទគតិខ្មែរ កាលវិភាគពិធីទាំង ៦ ផែនទី និងប្រព័ន្ធចំណងដៃ KHQR ងាយស្រួលស្កេនពីគ្រប់ទីកន្លែង។
                    </p>

                    <!-- CTAs & Trust Points -->
                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="{{ route('login') }}" class="btn-primary text-sm !px-7 !py-3.5 shadow-lg shadow-[#7B1122]/20 w-full sm:w-auto text-center group">
                            <span class="font-semibold font-kantumruy" lang="km">បង្កើតសំបុត្រអញ្ជើញឥតគិតថ្លៃ</span>
                            <span class="text-amber-200">· Start Free</span>
                            <svg class="size-4 group-hover:translate-x-1 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>

                        <a href="/themes/base" target="_blank" class="btn-secondary text-sm !px-6 !py-3.5 w-full sm:w-auto text-center flex items-center justify-center gap-2 group">
                            <svg class="size-4 text-amber-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="5 3 19 12 5 21 5 3" />
                            </svg>
                            <span class="font-medium font-kantumruy" lang="km">មើលគំរូសំបុត្រផ្ទាល់ (Live Demo)</span>
                        </a>
                    </div>

                    <!-- Cultural Guarantee Badges -->
                    <div class="pt-4 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs text-stone-600 font-medium">
                        <div class="flex items-center gap-1.5">
                            <svg class="size-4 text-amber-700" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>100% គន្លងប្រពៃណីខ្មែរ (Khmer Etiquette)</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="size-4 text-amber-700" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>ទូរស័ព្ទដៃ និង Telegram Friendly</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="size-4 text-amber-700" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>មិនទាមទារកាតឥណទាន (No Card Required)</span>
                        </div>
                    </div>
                </div>

                <!-- Right Visual: Interactive Luxury Smartphone Mockup -->
                <div class="lg:col-span-5 flex justify-center relative">
                    <!-- Floating Traditional Motifs Background -->
                    <div class="relative w-full max-w-[340px] sm:max-w-[360px] animate-float">
                        <!-- Luxury Smartphone Device -->
                        <div class="relative rounded-[50px] border-[10px] border-stone-900 bg-stone-900 shadow-2xl shadow-stone-900/40 ring-2 ring-amber-500/30 overflow-hidden">
                            <!-- Dynamic Island / Top Camera Pill -->
                            <div class="absolute top-2.5 left-1/2 -translate-x-1/2 z-20 flex items-center justify-center">
                                <div class="h-4 w-24 rounded-full bg-black shadow-inner flex items-center justify-end pr-2.5">
                                    <span class="size-2 rounded-full bg-stone-900"></span>
                                </div>
                            </div>

                            <!-- Live Demo Iframe / Preview Screen -->
                            <div class="relative h-[650px] w-full bg-[#FAF6EE] overflow-hidden rounded-[38px]">
                                <iframe src="/themes/base" title="Mongkol Invitation Demo" class="size-full border-none"></iframe>
                            </div>

                            <!-- Bottom Home Bar -->
                            <div class="absolute bottom-1.5 left-1/2 -translate-x-1/2 z-20">
                                <div class="h-1 w-28 rounded-full bg-stone-700/60"></div>
                            </div>
                        </div>

                        <!-- Floating Feature Badges -->
                        <div class="absolute -top-4 -left-6 bg-white/95 backdrop-blur-md rounded-2xl border border-amber-300/80 p-3 shadow-lg flex items-center gap-2.5 z-20 hidden sm:flex">
                            <div class="flex size-8 items-center justify-center rounded-xl bg-gradient-to-br from-[#7B1122] to-[#58111A] text-amber-200">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="16" rx="2" />
                                    <path d="M3 10h18M8 2v4M16 2v4" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold text-stone-900 font-kantumruy" lang="km">កាលបរិច្ឆេទចន្ទគតិ</p>
                                <p class="text-[9px] text-amber-800 font-medium">Automatic Lunar Calendar</p>
                            </div>
                        </div>

                        <div class="absolute -bottom-4 -right-6 bg-white/95 backdrop-blur-md rounded-2xl border border-amber-300/80 p-3 shadow-lg flex items-center gap-2.5 z-20 hidden sm:flex">
                            <div class="flex size-8 items-center justify-center rounded-xl bg-gradient-to-br from-[#7B1122] to-[#58111A] text-amber-200">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="8" width="18" height="13" rx="2" />
                                    <path d="M3 12h18M12 8v13" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold text-stone-900 font-kantumruy" lang="km">ចំណងដៃ KHQR 1-Tap</p>
                                <p class="text-[9px] text-amber-800 font-medium">ABA, ACLEDA & Bakong</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TRADITIONAL ORNAMENTAL DIVIDER -->
    <div class="py-6 flex items-center justify-center gap-4 text-amber-700/60 max-w-lg mx-auto px-4" aria-hidden="true">
        <span class="h-px flex-1 bg-gradient-to-r from-transparent to-amber-400"></span>
        <svg class="size-6 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M12 3c-2.5 3.5-3 8 0 13c3-5 2.5-9.5 0-13Z" />
            <path d="M12 16c-4.5-.5-8-3.5-9-8c4 .5 7 2.5 9 6" />
            <path d="M12 16c4.5-.5 8-3.5 9-8c-4 .5-7 2.5-9 6" />
            <circle cx="12" cy="18" r="1.5" fill="currentColor" />
        </svg>
        <span class="h-px flex-1 bg-gradient-to-l from-transparent to-amber-400"></span>
    </div>

    <!-- CORE FEATURES SECTION (លក្ខណៈពិសេស) -->
    <section id="features" class="py-20 bg-white border-y border-stone-200/80 relative">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-3">
                <p class="text-xs font-bold uppercase tracking-widest text-amber-800">CRAFTED FOR ROYAL DIGNITY</p>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-stone-900 font-moul leading-snug" lang="km">
                    លក្ខណៈពិសេសលេចធ្លោនៃ Mongkol
                </h2>
                <p class="text-sm sm:text-base text-stone-600 font-kantumruy leading-relaxed" lang="km">
                    រាល់ការរចនា និងមុខងារទាំងអស់ ត្រូវបានផ្ចិតផ្ចង់យ៉ាងម៉ត់ចត់ ដើម្បីធានាបាននូវភាពថ្លៃថ្នូរ គោរពកិត្តិយសមាតាបិតា និងផ្តល់ភាពងាយស្រួលដល់ភ្ញៀវគ្រប់វ័យ។
                </p>
            </div>

            <!-- Features Grid (6 Key Pillars) -->
            <div class="mt-14 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <!-- 1. 3D Wax Seal Envelope -->
                <div class="card p-7 rounded-2xl border-stone-200/90 hover:border-amber-500/50 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex size-12 items-center justify-center rounded-xl bg-gradient-to-br from-[#7B1122] to-[#58111A] text-amber-200 shadow-sm mb-5 group-hover:scale-110 transition-transform">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <circle cx="12" cy="12" r="9" />
                            <circle cx="12" cy="12" r="6" stroke-dasharray="2 2" />
                            <text x="12" y="15" text-anchor="middle" font-size="8" fill="currentColor" font-weight="bold">ម</text>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 font-kantumruy" lang="km">ស្រោមសំបុត្រ 3D និងត្រាមង្គល</h3>
                    <p class="text-xs font-semibold text-amber-800 uppercase tracking-wider mt-0.5">3D Royal Wax Seal Envelope</p>
                    <p class="text-xs text-stone-600 font-kantumruy mt-3 leading-relaxed" lang="km">
                        ភ្ញៀវទទួលសំបុត្រដែលមានឈ្មោះរបស់ខ្លួនយ៉ាងគួរសម ជាមួយត្រាមង្គលមាស 3D ដែលអាចចុចបើក (Unboxing) ប្រៀបដូចជាការហែកស្រោមសំបុត្រការប្រណិតពិតៗ។
                    </p>
                </div>

                <!-- 2. Astrological Lunar Date -->
                <div class="card p-7 rounded-2xl border-stone-200/90 hover:border-amber-500/50 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex size-12 items-center justify-center rounded-xl bg-gradient-to-br from-[#7B1122] to-[#58111A] text-amber-200 shadow-sm mb-5 group-hover:scale-110 transition-transform">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M12 3a9 9 0 109 9c0-.46-.04-.92-.1-1.36a5.389 5.389 0 01-4.4 2.26 5.403 5.403 0 01-3.14-9.8c-.44-.06-.9-.1-1.36-.1z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 font-kantumruy" lang="km">កាលបរិច្ឆេទចន្ទគតិខ្មែរត្រឹមត្រូវ</h3>
                    <p class="text-xs font-semibold text-amber-800 uppercase tracking-wider mt-0.5">Accurate Khmer Lunar Date</p>
                    <p class="text-xs text-stone-600 font-kantumruy mt-3 leading-relaxed" lang="km">
                        គណនាដោយស្វ័យប្រវត្តិពីកាលបរិច្ឆេទសកល ទៅជាថ្ងៃខែចន្ទគតិខ្មែរ (ថ្ងៃ... កើត/រោច ខែ... ឆ្នាំ... ពុទ្ធសករាជ) តាមពេលវេលាជាក់ស្តែងនៅភ្នំពេញ មិនបាច់ស្វែងរកប្រតិទិន។
                    </p>
                </div>

                <!-- 3. The 6 Ceremonies Timeline -->
                <div class="card p-7 rounded-2xl border-stone-200/90 hover:border-amber-500/50 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex size-12 items-center justify-center rounded-xl bg-gradient-to-br from-[#7B1122] to-[#58111A] text-amber-200 shadow-sm mb-5 group-hover:scale-110 transition-transform">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 font-kantumruy" lang="km">កាលវិភាគពិធីការប្រពៃណីទាំង ៦</h3>
                    <p class="text-xs font-semibold text-amber-800 uppercase tracking-wider mt-0.5">Full Ceremonies Itinerary</p>
                    <p class="text-xs text-stone-600 font-kantumruy mt-3 leading-relaxed" lang="km">
                        មានស្រាប់នូវពុម្ពពិធីការប្រពៃណី៖ ពិធីហែជំនូន ពិធីសូត្រមន្ត ពិធីកាត់សក់ ពិធីសំពះផ្ទឹម ពិធីចងដៃ និងពិធីជប់លៀង ជាមួយសញ្ញា «បន្ទាប់ (Next)» ភ្លឺដោយស្វ័យប្រវត្តិតាមម៉ោងជាក់ស្តែង។
                    </p>
                </div>

                <!-- 4. Cashless Digital Chong Dai -->
                <div class="card p-7 rounded-2xl border-stone-200/90 hover:border-amber-500/50 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex size-12 items-center justify-center rounded-xl bg-gradient-to-br from-[#7B1122] to-[#58111A] text-amber-200 shadow-sm mb-5 group-hover:scale-110 transition-transform">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <rect x="3" y="8" width="18" height="13" rx="2" />
                            <path d="M3 12h18M12 8v13" />
                            <path d="M12 8C10 5.5 7.5 5.5 6 7s1 5 6 1ZM12 8C14 5.5 16.5 5.5 18 7s-1 5-6 1Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 font-kantumruy" lang="km">ចំណងដៃ KHQR ឌីជីថល</h3>
                    <p class="text-xs font-semibold text-amber-800 uppercase tracking-wider mt-0.5">1-Tap Chong Dai & KHQR</p>
                    <p class="text-xs text-stone-600 font-kantumruy mt-3 leading-relaxed" lang="km">
                        ស្រោមសំបុត្រចំណងដៃប្រណិត គាំទ្រ KHQR (ABA, ACLEDA, Bakong) អាចចម្លងលេខគណនីបានដោយចុចតែម្តង និងអាចទាញយក QR code ទុកស្កេនយ៉ាងងាយស្រួល។
                    </p>
                </div>

                <!-- 5. Smart Maps & Calendar Sync -->
                <div class="card p-7 rounded-2xl border-stone-200/90 hover:border-amber-500/50 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex size-12 items-center justify-center rounded-xl bg-gradient-to-br from-[#7B1122] to-[#58111A] text-amber-200 shadow-sm mb-5 group-hover:scale-110 transition-transform">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M12 21.5C12 21.5 5 14.5 5 9.5A7 7 0 0 1 19 9.5C19 14.5 12 21.5 12 21.5Z" />
                            <circle cx="12" cy="9.5" r="2.5" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 font-kantumruy" lang="km">ផែនទី និងប្រតិទិនឆ្លាតវៃ</h3>
                    <p class="text-xs font-semibold text-amber-800 uppercase tracking-wider mt-0.5">Google Maps & Calendar Sync</p>
                    <p class="text-xs text-stone-600 font-kantumruy mt-3 leading-relaxed" lang="km">
                        ភ្ញៀវអាចចុច «បើកផែនទី» ទៅកាន់គេហដ្ឋាន ឬសាលមង្គលការដោយផ្ទាល់តាម Google Maps និងចុចបញ្ចូលកាលបរិច្ឆេទក្នុង Google / Apple Calendar កុំឱ្យភ្លេចថ្ងៃពិសេស។
                    </p>
                </div>

                <!-- 6. Bilingual & Grandparents Friendly -->
                <div class="card p-7 rounded-2xl border-stone-200/90 hover:border-amber-500/50 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex size-12 items-center justify-center rounded-xl bg-gradient-to-br from-[#7B1122] to-[#58111A] text-amber-200 shadow-sm mb-5 group-hover:scale-110 transition-transform">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M14.751 16.5h4.498" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 font-kantumruy" lang="km">ទ្វេភាសា ខ្មែរ និងអង់គ្លេស</h3>
                    <p class="text-xs font-semibold text-amber-800 uppercase tracking-wider mt-0.5">Bilingual & Accessible</p>
                    <p class="text-xs text-stone-600 font-kantumruy mt-3 leading-relaxed" lang="km">
                        គោរពលោកតាលោកយាយ និងមាតាបិតា ដោយអក្សរខ្មែរធំៗ ច្បាស់ៗ (មិនប្រើអក្សរមាសតូចៗពិបាកមើល) និងមានប៊ូតុងប្តូរទៅភាសាអង់គ្លេសសម្រាប់មិត្តភក្តិបរទេស។
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- THE 6 SACRED WEDDING CEREMONIES WALKTHROUGH -->
    <section id="ceremonies" class="py-20 bg-[#FAF7F2] relative">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-3">
                <p class="text-xs font-bold uppercase tracking-widest text-amber-800">SACRED CAMBODIAN WEDDING RITUALS</p>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-stone-900 font-moul leading-snug" lang="km">
                    ដំណើរនៃពិធីមង្គលការខ្មែរទាំង ៦
                </h2>
                <p class="text-sm sm:text-base text-stone-600 font-kantumruy leading-relaxed" lang="km">
                    រៀបចំលំដាប់ពិធីប្រពៃណីយ៉ាងលម្អិត ដើម្បីឱ្យភ្ញៀវដឹងច្បាស់ពីពេលវេលានៃពិធីនីមួយៗ និងអត្ថន័យដ៏ពិសិដ្ឋ។
                </p>
            </div>

            <!-- Rituals Progression Cards -->
            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <!-- 1. Chey Hong / Hai Chomnoun -->
                <div class="card p-6 border-stone-200/90 bg-white">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="flex size-7 items-center justify-center rounded-full bg-amber-100 text-amber-900 text-xs font-bold font-kantumruy">១</span>
                        <h3 class="text-base font-bold text-stone-900 font-kantumruy" lang="km">ពិធីហែជំនូន (Chey Hong)</h3>
                    </div>
                    <p class="text-xs font-semibold text-amber-800">Gift & Fruit Procession</p>
                    <p class="text-xs text-stone-600 font-kantumruy mt-2.5 leading-relaxed" lang="km">
                        ក្បួនហែផ្លែឈើ និងជំនូនខាន់ស្លាពីខាងកូនកំលោះឆ្ពោះទៅគេហដ្ឋានខាងកូនក្រមុំ ដោយសេចក្តីរីករាយ និងកិត្តិយស។
                    </p>
                </div>

                <!-- 2. Monks Blessing -->
                <div class="card p-6 border-stone-200/90 bg-white">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="flex size-7 items-center justify-center rounded-full bg-amber-100 text-amber-900 text-xs font-bold font-kantumruy">២</span>
                        <h3 class="text-base font-bold text-stone-900 font-kantumruy" lang="km">ពិធីសូត្រមន្តចម្រើនព្រះបរិត្ត</h3>
                    </div>
                    <p class="text-xs font-semibold text-amber-800">Monks’ Chanting & Blessing</p>
                    <p class="text-xs text-stone-600 font-kantumruy mt-2.5 leading-relaxed" lang="km">
                        ព្រះសង្ឃសូត្រធម៌ចម្រើនព្រះបរិត្ត ប្រោះព្រំទឹកមន្តសិរីសួស្តី ដើម្បីបណ្តេញឧបទ្រពចង្រៃ និងប្រសិទ្ធពរជ័យដល់គូស្វាមីភរិយា។
                    </p>
                </div>

                <!-- 3. Hair Cutting Ceremony -->
                <div class="card p-6 border-stone-200/90 bg-white">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="flex size-7 items-center justify-center rounded-full bg-amber-100 text-amber-900 text-xs font-bold font-kantumruy">៣</span>
                        <h3 class="text-base font-bold text-stone-900 font-kantumruy" lang="km">ពិធីកាត់សក់បង្កក់សិរី</h3>
                    </div>
                    <p class="text-xs font-semibold text-amber-800">Cleansing & Hair-Cutting Ceremony</p>
                    <p class="text-xs text-stone-600 font-kantumruy mt-2.5 leading-relaxed" lang="km">
                        ពិធីកាត់សក់សិរីមង្គល ដោយមានលោកអាចារ្យ ឬទេវតាស្រង់ទឹកអប់ ប្រោះព្រំពរជ័យឱ្យគូស្នេហ៍បានស្អាតបរិសុទ្ធទាំងកាយ និងចិត្ត។
                    </p>
                </div>

                <!-- 4. Sampeah Phtem -->
                <div class="card p-6 border-stone-200/90 bg-white">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="flex size-7 items-center justify-center rounded-full bg-amber-100 text-amber-900 text-xs font-bold font-kantumruy">៤</span>
                        <h3 class="text-base font-bold text-stone-900 font-kantumruy" lang="km">ពិធីសំពះផ្ទឹម (Sampeah Phtem)</h3>
                    </div>
                    <p class="text-xs font-semibold text-amber-800">The Nuptial Union Ceremony</p>
                    <p class="text-xs text-stone-600 font-kantumruy mt-2.5 leading-relaxed" lang="km">
                        ពិធីផ្ទឹមបូជាដួងវិញ្ញាណក្ខន្ធដូនតា និងការផ្គូផ្គងកូនប្រុសកូនស្រីឱ្យក្លាយជាប្តីប្រពន្ធពេញលេញតាមប្រពៃណី និងច្បាប់សង្គម។
                    </p>
                </div>

                <!-- 5. Knot Tying Ceremony -->
                <div class="card p-6 border-stone-200/90 bg-white">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="flex size-7 items-center justify-center rounded-full bg-amber-100 text-amber-900 text-xs font-bold font-kantumruy">៥</span>
                        <h3 class="text-base font-bold text-stone-900 font-kantumruy" lang="km">ពិធីចងដៃ និងប្រសិទ្ធពរជ័យ</h3>
                    </div>
                    <p class="text-xs font-semibold text-amber-800">Red Thread Knot-Tying Ceremony</p>
                    <p class="text-xs text-stone-600 font-kantumruy mt-2.5 leading-relaxed" lang="km">
                        មាតាបិតា ញាតិមិត្ត និងភ្ញៀវកិត្តិយសចងអំបោះក្រហមលើកដៃកូនកំលោះកូនក្រមុំ និងបាចផ្កាប្រសិទ្ធពរឱ្យមានសុភមង្គលរហូតចាស់កោងខ្នង។
                    </p>
                </div>

                <!-- 6. Wedding Banquet -->
                <div class="card p-6 border-stone-200/90 bg-white">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="flex size-7 items-center justify-center rounded-full bg-amber-100 text-amber-900 text-xs font-bold font-kantumruy">៦</span>
                        <h3 class="text-base font-bold text-stone-900 font-kantumruy" lang="km">ពិធីជប់លៀងពិសារភោជនាហារ</h3>
                    </div>
                    <p class="text-xs font-semibold text-amber-800">Wedding Banquet & Reception</p>
                    <p class="text-xs text-stone-600 font-kantumruy mt-2.5 leading-relaxed" lang="km">
                        ការជួបជុំទទួលទានអាហារពេលល្ងាចដោយភាពរីករាយ ជាមួយតន្ត្រីសម័យ និងការថតរូបអនុស្សាវរីយ៍ដ៏មានតម្លៃជាមួយភ្ញៀវទាំងអស់។
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- THEMES SHOWCASE SECTION (គំរូស្បែករចនា) -->
    <section id="themes" class="py-20 bg-white border-y border-stone-200/80">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-stone-200/80 pb-6">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-amber-800">BESPOKE STATIONERY THEMES</p>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-stone-900 font-moul leading-snug mt-1" lang="km">
                        ស្បែករចនា «មរតកស្នេហា»
                    </h2>
                    <p class="text-xs font-semibold text-stone-500 font-playfair italic">Morodok Snaeha Base Theme & Luxury Palettes</p>
                </div>
                <a href="/themes/base" target="_blank" class="btn-primary text-xs !py-2.5 !px-5 self-start md:self-auto">
                    <span>សាកល្បងមើលគំរូផ្ទាល់ (Live Theme Demo) ↗</span>
                </a>
            </div>

            <!-- Palettes Showcase Grid -->
            <div class="mt-10 grid gap-6 sm:grid-cols-3">
                <!-- Royal Crimson Palette -->
                <div class="card p-6 border-stone-200/90 rounded-2xl hover:border-amber-500/50 hover:shadow-md transition-all">
                    <div class="h-32 w-full rounded-xl bg-gradient-to-br from-[#7B1122] via-[#58111A] to-[#38070F] p-4 flex flex-col justify-between text-amber-200 shadow-inner">
                        <span class="inline-block self-end text-[10px] font-bold uppercase tracking-wider bg-black/40 px-2 py-0.5 rounded-full border border-amber-300/30">Royal Classic</span>
                        <div>
                            <p class="text-sm font-bold font-moul text-ivory-50" lang="km">ក្រហមឈាមជ្រូក</p>
                            <p class="text-[11px] text-amber-200/80 font-playfair italic">Royal Crimson & Gold Leaf</p>
                        </div>
                    </div>
                    <p class="text-xs text-stone-600 font-kantumruy mt-4 leading-relaxed" lang="km">
                        ពណ៌ក្រហមឈាមជ្រូកតំណាងឱ្យភាពរុងរឿង សិរីសួស្តី និងសេចក្តីស្រឡាញ់ដ៏ស្មោះស្ម័គ្រ។ ពេញនិយមបំផុតសម្រាប់ពិធីមង្គលការខ្មែរគ្រប់ជំនាន់។
                    </p>
                </div>

                <!-- Imperial Jade Palette -->
                <div class="card p-6 border-stone-200/90 rounded-2xl hover:border-amber-500/50 hover:shadow-md transition-all">
                    <div class="h-32 w-full rounded-xl bg-gradient-to-br from-[#1B5242] via-[#12382D] to-[#0C261E] p-4 flex flex-col justify-between text-emerald-200 shadow-inner">
                        <span class="inline-block self-end text-[10px] font-bold uppercase tracking-wider bg-black/40 px-2 py-0.5 rounded-full border border-emerald-300/30">Noble Peace</span>
                        <div>
                            <p class="text-sm font-bold font-moul text-ivory-50" lang="km">ត្បូងមរកត (Jade)</p>
                            <p class="text-[11px] text-emerald-200/80 font-playfair italic">Imperial Jade & Antique Gold</p>
                        </div>
                    </div>
                    <p class="text-xs text-stone-600 font-kantumruy mt-4 leading-relaxed" lang="km">
                        ពណ៌បៃតងត្បូងមរកត តំណាងឱ្យសន្តិភាព ភាពត្រជាក់ត្រជុំ និងការរីកចម្រើនលូតលាស់នៃចំណងអាពាហ៍ពិពាហ៍ថ្មី។
                    </p>
                </div>

                <!-- Pailin Sapphire Palette -->
                <div class="card p-6 border-stone-200/90 rounded-2xl hover:border-amber-500/50 hover:shadow-md transition-all">
                    <div class="h-32 w-full rounded-xl bg-gradient-to-br from-[#1F2B54] via-[#151D3B] to-[#0D1326] p-4 flex flex-col justify-between text-blue-200 shadow-inner">
                        <span class="inline-block self-end text-[10px] font-bold uppercase tracking-wider bg-black/40 px-2 py-0.5 rounded-full border border-blue-300/30">Aristocratic</span>
                        <div>
                            <p class="text-sm font-bold font-moul text-ivory-50" lang="km">ត្បូងកណ្តៀង (Sapphire)</p>
                            <p class="text-[11px] text-blue-200/80 font-playfair italic">Pailin Sapphire & Platinum</p>
                        </div>
                    </div>
                    <p class="text-xs text-stone-600 font-kantumruy mt-4 leading-relaxed" lang="km">
                        ពណ៌ខៀវត្បូងកណ្តៀងតំណាងឱ្យភាពថ្លៃថ្នូរ ភក្តីភាព និងអំណាចនៃក្តីសច្ចៈប្រណិតបែបអភិជន។
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS (៣ ជំហានងាយៗ) -->
    <section id="how-it-works" class="py-20 bg-[#FAF7F2]">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-3">
                <p class="text-xs font-bold uppercase tracking-widest text-amber-800">SIMPLE 3-STEP PROCESS</p>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-stone-900 font-moul leading-snug" lang="km">
                    របៀបបង្កើតសំបុត្រអញ្ជើញក្នុង ២ នាទី
                </h2>
                <p class="text-sm sm:text-base text-stone-600 font-kantumruy leading-relaxed" lang="km">
                    បង្កើតសំបុត្រដ៏ស្រស់ស្អាតដោយខ្លួនឯងបានយ៉ាងងាយស្រួលនៅលើទូរស័ព្ទដៃ ឬកុំព្យូទ័រ។
                </p>
            </div>

            <!-- Steps Grid -->
            <div class="mt-14 grid gap-8 md:grid-cols-3">
                <div class="card p-8 text-center rounded-2xl bg-white border-stone-200/90 shadow-xs relative">
                    <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-amber-100 text-[#7B1122] text-xl font-bold font-moul mb-6">
                        ១
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 font-kantumruy" lang="km">បំពេញព័ត៌មានមង្គលការ</h3>
                    <p class="text-xs font-semibold text-amber-800 uppercase mt-0.5">Enter Details</p>
                    <p class="text-xs text-stone-600 font-kantumruy mt-3 leading-relaxed" lang="km">
                        បញ្ចូលឈ្មោះកូនកំលោះ កូនក្រមុំ មាតាបិតាទាំងសងខាង កាលបរិច្ឆេទ និងទីតាំងប្រារព្ធពិធី។
                    </p>
                </div>

                <div class="card p-8 text-center rounded-2xl bg-white border-stone-200/90 shadow-xs relative">
                    <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-amber-100 text-[#7B1122] text-xl font-bold font-moul mb-6">
                        ២
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 font-kantumruy" lang="km">មើលគំរូសំបុត្រផ្ទាល់</h3>
                    <p class="text-xs font-semibold text-amber-800 uppercase mt-0.5">Preview on Phone</p>
                    <p class="text-xs text-stone-600 font-kantumruy mt-3 leading-relaxed" lang="km">
                        មើលផ្ទៀងផ្ទាត់លើទូរស័ព្ទដៃផ្ទាល់ (Live Phone Preview) ជាមួយភ្លេងការ ពេលវេលា និង KHQR។
                    </p>
                </div>

                <div class="card p-8 text-center rounded-2xl bg-white border-stone-200/90 shadow-xs relative">
                    <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-amber-100 text-[#7B1122] text-xl font-bold font-moul mb-6">
                        ៣
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 font-kantumruy" lang="km">ចែករំលែកទៅកាន់ភ្ញៀវ</h3>
                    <p class="text-xs font-semibold text-amber-800 uppercase mt-0.5">Share with Guests</p>
                    <p class="text-xs text-stone-600 font-kantumruy mt-3 leading-relaxed" lang="km">
                        ចម្លងតំណភ្ជាប់ (Link) ផ្ញើតាម Telegram, Messenger ឬ WhatsApp ទៅកាន់ភ្ញៀវយ៉ាងរហ័ស។
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ SECTION (សំណួរញឹកញាប់) -->
    <section id="faq" class="py-20 bg-white border-t border-stone-200/80">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="text-center space-y-3">
                <p class="text-xs font-bold uppercase tracking-widest text-amber-800">FREQUENTLY ASKED QUESTIONS</p>
                <h2 class="text-2xl sm:text-3xl font-bold text-stone-900 font-moul leading-snug" lang="km">
                    សំណួរដែលត្រូវបានសួរញឹកញាប់
                </h2>
            </div>

            <div class="mt-12 space-y-4">
                <details class="card p-5 group cursor-pointer border-stone-200">
                    <summary class="font-bold text-stone-900 text-sm sm:text-base flex items-center justify-between font-kantumruy" lang="km">
                        <span>តើលោកតាលោកយាយ ឬចាស់ទុំអាចមើលសំបុត្រនេះបានច្បាស់ដែរឬទេ?</span>
                        <span class="text-amber-700 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-stone-600 font-kantumruy leading-relaxed border-t border-stone-100 pt-3" lang="km">
                        ពិតជាច្បាស់ណាស់! Mongkol ត្រូវបានរចនាឡើងជាពិសេសសម្រាប់មនុស្សចាស់ (Accessible for Grandparents) ដោយទំហំអក្សរយ៉ាងតិច 16px កម្រិតពណ៌ច្បាស់ (High Contrast) មិនប្រើអក្សរមាសតូចៗដែលពិបាកអានឡើយ និងមានប៊ូតុងធំៗងាយស្រួលចុច។
                    </p>
                </details>

                <details class="card p-5 group cursor-pointer border-stone-200">
                    <summary class="font-bold text-stone-900 text-sm sm:text-base flex items-center justify-between font-kantumruy" lang="km">
                        <span>តើការស្កេនចំណងដៃ KHQR មានសុវត្ថិភាពកម្រិតណា?</span>
                        <span class="text-amber-700 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-stone-600 font-kantumruy leading-relaxed border-t border-stone-100 pt-3" lang="km">
                        សុវត្ថិភាព 100%! រូបភាព KHQR គឺជារូបភាពដើមដែលចេញពីធនាគារផ្ទាល់ខ្លួនរបស់អ្នក (ABA, ACLEDA, Bakong) ដោយគ្មានការកែច្នៃ ឬកាត់បន្ថយគុណភាពឡើយ។ ភ្ញៀវអាចស្កេនដោយផ្ទាល់ ឬចម្លងលេខគណនីទៅផ្ទេរប្រាក់ក្នុងកម្មវិធីធនាគារបានភ្លាមៗ។
                    </p>
                </details>

                <details class="card p-5 group cursor-pointer border-stone-200">
                    <summary class="font-bold text-stone-900 text-sm sm:text-base flex items-center justify-between font-kantumruy" lang="km">
                        <span>តើខ្ញុំអាចកែសម្រួលព័ត៌មានក្រោយពេលផ្ញើសំបុត្រទៅភ្ញៀវបានទេ?</span>
                        <span class="text-amber-700 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-stone-600 font-kantumruy leading-relaxed border-t border-stone-100 pt-3" lang="km">
                        បានយ៉ាងងាយស្រួល! រាល់ពេលដែលអ្នកកែប្រែម៉ោង ទីតាំង ឬរូបថតនៅក្នុងផ្ទាំងគ្រប់គ្រង (Dashboard) ប្រព័ន្ធនឹងធ្វើបច្ចុប្បន្នភាពលើតំណភ្ជាប់សំបុត្រដោយស្វ័យប្រវត្តិ។ ភ្ញៀវគ្រាន់តែចុចបើកម្តងទៀត នឹងឃើញព័ត៌មានថ្មីភ្លាមៗ។
                    </p>
                </details>

                <details class="card p-5 group cursor-pointer border-stone-200">
                    <summary class="font-bold text-stone-900 text-sm sm:text-base flex items-center justify-between font-kantumruy" lang="km">
                        <span>តើសំបុត្រនេះអាចចាក់ភ្លេងការប្រពៃណីខ្មែរបានទេ?</span>
                        <span class="text-amber-700 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-stone-600 font-kantumruy leading-relaxed border-t border-stone-100 pt-3" lang="km">
                        បាន! នៅពេលភ្ញៀវចុច «បើកសំបុត្រអញ្ជើញ» ភ្លេងការប្រពៃណីពិរោះរណ្តំ នឹងចាប់ផ្តើមបន្លឺឡើងដោយស្វ័យប្រវត្តិ។ ភ្ញៀវក៏អាចចុចលើត្រាមង្គលមាសដើម្បីផ្អាក ឬចាក់ភ្លេងឡើងវិញបានគ្រប់ពេល។
                    </p>
                </details>
            </div>
        </div>
    </section>

    <!-- GRAND ROYAL CTA BANNER -->
    <section class="py-20 bg-[#FAF7F2] relative">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-gradient-to-br from-[#7B1122] via-[#58111A] to-[#38070F] p-8 sm:p-14 text-center text-white relative shadow-2xl overflow-hidden ring-1 ring-amber-500/40">
                <!-- Inner Gold Border Frame -->
                <div class="absolute inset-3 border border-amber-400/30 rounded-2xl pointer-events-none" aria-hidden="true"></div>

                <div class="relative z-10 space-y-5 max-w-2xl mx-auto">
                    <!-- Auspicious Seal -->
                    <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-gradient-to-br from-amber-200 to-amber-500 text-stone-950 shadow-lg ring-4 ring-amber-400/40">
                        <svg class="size-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 3c-2.5 3.5-3 8 0 13c3-5 2.5-9.5 0-13Z" />
                            <path d="M12 16c-4.5-.5-8-3.5-9-8c4 .5 7 2.5 9 6" />
                            <path d="M12 16c4.5-.5 8-3.5 9-8c-4 .5-7 2.5-9 6" />
                            <circle cx="12" cy="18" r="1.5" fill="currentColor" />
                        </svg>
                    </div>

                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold font-moul leading-snug text-ivory-50" lang="km">
                        ត្រៀមខ្លួនសម្រាប់ថ្ងៃមង្គលដ៏ពិសិដ្ឋរបស់អ្នកហើយឬនៅ?
                    </h2>
                    <p class="text-sm sm:text-base text-amber-100 font-kantumruy leading-relaxed" lang="km">
                        បង្កើតសំបុត្រអញ្ជើញឥឡូវនេះ ដើម្បីមើលជាមុនដោយឥតគិតថ្លៃ។ មិនទាមទារកាតឥណទានឡើយ។
                    </p>

                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#D4AF37] to-[#C5A059] px-8 py-4 text-sm font-bold text-stone-950 shadow-lg hover:brightness-105 active:scale-98 transition-all w-full sm:w-auto font-kantumruy" lang="km">
                            <span>បង្កើតសំបុត្រអញ្ជើញឥឡូវនេះ (Start Free)</span>
                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="border-t border-stone-200/80 bg-white py-12 text-center text-xs text-stone-600 font-kantumruy">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-4">
            <div class="flex items-center justify-center gap-2">
                <span class="text-base font-bold text-stone-900 font-playfair">Mongkol</span>
                <span class="text-xs font-semibold text-amber-800" lang="km">· មង្គល</span>
            </div>
            <p class="max-w-md mx-auto text-stone-500" lang="km">
                បង្កើតឡើងដោយក្តីស្រឡាញ់ និងការគោរពដ៏ជ្រាលជ្រៅចំពោះទំនៀមទម្លាប់ប្រពៃណីអាពាហ៍ពិពាហ៍ខ្មែរ។
            </p>
            <div class="flex items-center justify-center gap-6 text-stone-600 pt-2 font-medium">
                <a href="#features" class="hover:text-[#7B1122]">លក្ខណៈពិសេស</a>
                <a href="#themes" class="hover:text-[#7B1122]">ស្បែករចនា</a>
                <a href="/themes/base" target="_blank" class="hover:text-[#7B1122]">គំរូសំបុត្រ (Live Demo)</a>
                <a href="{{ route('login') }}" class="hover:text-[#7B1122]">ចូលគណនី</a>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-4 text-stone-500 text-xs pt-1">
                <a href="{{ route('legal', 'privacy') }}" class="inline-flex items-center min-h-[44px] px-2.5 py-1.5 hover:text-[#7B1122] transition-colors rounded-lg hover:bg-stone-50">គោលការណ៍ឯកជនភាព</a>
                <span class="text-stone-300 hidden sm:inline" aria-hidden="true">·</span>
                <a href="{{ route('legal', 'terms') }}" class="inline-flex items-center min-h-[44px] px-2.5 py-1.5 hover:text-[#7B1122] transition-colors rounded-lg hover:bg-stone-50">លក្ខខណ្ឌប្រើប្រាស់</a>
                <span class="text-stone-300 hidden sm:inline" aria-hidden="true">·</span>
                <a href="{{ route('legal', 'refunds') }}" class="inline-flex items-center min-h-[44px] px-2.5 py-1.5 hover:text-[#7B1122] transition-colors rounded-lg hover:bg-stone-50">គោលការណ៍សងប្រាក់វិញ</a>
            </div>
            <p class="text-stone-400 pt-4 text-[11px]">
                &copy; {{ date('Y') }} Mongkol. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- ========================================================
         FLOATING INTERACTIVE CONTROLS
         ======================================================== -->
    
    <!-- 1. Traditional Khmer Phleng Kar Audio Synthesizer & Floating Seal -->
    <div class="fixed right-4 sm:right-6 bottom-6 z-40 flex items-center gap-3">
        <!-- Floating Tooltip Hint -->
        <div id="music-tooltip" class="hidden sm:flex items-center rounded-lg bg-stone-900 text-amber-200 px-3 py-1.5 text-xs font-kantumruy shadow-lg border border-amber-500/30">
            <span>ភ្លេងការប្រពៃណី (Play Music)</span>
        </div>

        <button
            id="phleng-kar-btn"
            type="button"
            class="group relative flex size-14 items-center justify-center rounded-full bg-gradient-to-br from-[#7B1122] to-[#58111A] text-amber-300 shadow-xl border-2 border-amber-400/80 hover:scale-105 active:scale-95 transition-all cursor-pointer ring-4 ring-amber-500/20"
            aria-label="Toggle Traditional Khmer Wedding Music"
            title="ភ្លេងការប្រពៃណីខ្មែរ (Phleng Kar)"
        >
            <!-- Spinning Gold Dashed Aura (when playing) -->
            <div id="music-aura" class="hidden absolute -inset-2 rounded-full border-2 border-dashed border-amber-400 animate-spin" style="animation-duration: 8s;"></div>

            <!-- Lotus Icon / Music Notes -->
            <svg id="music-icon-play" class="size-7 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 3c-2.5 3.5-3 8 0 13c3-5 2.5-9.5 0-13Z" fill="currentColor" fill-opacity="0.3" />
                <path d="M12 16c-4.5-.5-8-3.5-9-8c4 .5 7 2.5 9 6" />
                <path d="M12 16c4.5-.5 8-3.5 9-8c-4 .5-7 2.5-9 6" />
                <circle cx="12" cy="18" r="1.5" fill="currentColor" />
            </svg>

            <svg id="music-icon-pause" class="size-7 hidden" viewBox="0 0 24 24" fill="currentColor">
                <rect x="6" y="5" width="4" height="14" rx="1.5" />
                <rect x="14" y="5" width="4" height="14" rx="1.5" />
            </svg>
        </button>
    </div>

    <!-- 2. Falling Petals Toggle Button (Bottom Left) -->
    <div class="fixed left-4 sm:left-6 bottom-6 z-40">
        <button
            id="petals-toggle-btn"
            type="button"
            class="flex items-center gap-2 rounded-full bg-white/90 backdrop-blur-md px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-lg border border-amber-300 hover:bg-amber-50 hover:text-amber-900 transition-all cursor-pointer font-kantumruy"
            title="Toggle Falling Flower Blessing"
        >
            <span class="text-sm">🌸</span>
            <span id="petals-label">ផ្កាប្រសិទ្ធពរ (Petals)</span>
        </button>
    </div>

    <!-- ========================================================
         SCRIPTS: Falling Petals Canvas & Khmer Web Audio Engine
         ======================================================== -->
    <script>
        /* ----------------------------------------------------
           2. TRADITIONAL KHMER PHLENG KAR AUDIO SYNTHESIZER
           Authentic Robam Choun Por & Sampeah Phtem pentatonic wedding melody
           Roneat wooden xylophone & Khim dulcimer synthesized via Web Audio API
           ---------------------------------------------------- */
        (function initKhmerMusic() {
            let audioCtx = null;
            let isPlaying = false;
            let noteTimer = null;
            let step = 0;

            const btn = document.getElementById('phleng-kar-btn');
            const aura = document.getElementById('music-aura');
            const playIcon = document.getElementById('music-icon-play');
            const pauseIcon = document.getElementById('music-icon-pause');
            const tooltip = document.getElementById('music-tooltip');

            // Khmer Pentatonic Wedding Scale Frequencies (Robam Choun Por / Sampeah Phtem Motif)
            const NOTES = {
                'G3': 196.00, 'A3': 220.00, 'C4': 261.63, 'D4': 293.66, 'E4': 329.63,
                'G4': 392.00, 'A4': 440.00, 'C5': 523.25, 'D5': 587.33, 'E5': 659.25,
                'G5': 783.99, 'A5': 880.00,
            };

            // Classic Sacred Khmer Wedding Melody Sequence: Robam Choun Por (Blessing Song)
            const MELODY = [
                { note: 'G4', dur: 0.4 }, { note: 'A4', dur: 0.4 }, { note: 'C5', dur: 0.6 },
                { note: 'D5', dur: 0.4 }, { note: 'E5', dur: 0.8 }, { note: 'D5', dur: 0.4 },
                { note: 'C5', dur: 0.6 }, { note: 'A4', dur: 0.4 }, { note: 'G4', dur: 0.8 },
                { note: 'A4', dur: 0.4 }, { note: 'C5', dur: 0.4 }, { note: 'D5', dur: 0.6 },
                { note: 'C5', dur: 0.4 }, { note: 'A4', dur: 0.8 }, { note: 'G4', dur: 0.4 },
                { note: 'A4', dur: 0.4 }, { note: 'C5', dur: 0.8 }, { note: 'E5', dur: 0.4 },
                { note: 'D5', dur: 0.6 }, { note: 'C5', dur: 0.4 }, { note: 'A4', dur: 0.4 },
                { note: 'G4', dur: 1.0 }, { note: 'C4', dur: 0.8 }, { note: 'G4', dur: 0.8 },
            ];

            function playRoneatTone(freq, time, duration) {
                if (!audioCtx) return;

                // 1. Roneat Ek Primary Mallet (Wooden Percussion Ring)
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, time);

                gain.gain.setValueAtTime(0.001, time);
                gain.gain.exponentialRampToValueAtTime(0.22, time + 0.015);
                gain.gain.exponentialRampToValueAtTime(0.001, time + duration * 1.2);

                // 2. Khim Metallic Overtone (Hammered Dulcimer Chime)
                const overtone = audioCtx.createOscillator();
                const overGain = audioCtx.createGain();
                overtone.type = 'triangle';
                overtone.frequency.setValueAtTime(freq * 2, time);

                overGain.gain.setValueAtTime(0.001, time);
                overGain.gain.exponentialRampToValueAtTime(0.06, time + 0.01);
                overGain.gain.exponentialRampToValueAtTime(0.0001, time + duration * 0.8);

                osc.connect(gain);
                gain.connect(audioCtx.destination);

                overtone.connect(overGain);
                overGain.connect(audioCtx.destination);

                osc.start(time);
                osc.stop(time + duration * 1.3);

                overtone.start(time);
                overtone.stop(time + duration * 0.9);
            }

            function playMelodyLoop() {
                if (!isPlaying || !audioCtx) return;

                const item = MELODY[step % MELODY.length];
                const freq = NOTES[item.note] || 440;
                playRoneatTone(freq, audioCtx.currentTime, item.dur);

                step++;
                noteTimer = setTimeout(playMelodyLoop, item.dur * 620);
            }

            function startMusic() {
                if (!audioCtx) {
                    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                }
                if (audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }
                isPlaying = true;
                playMelodyLoop();

                aura.classList.remove('hidden');
                playIcon.classList.add('hidden');
                pauseIcon.classList.remove('hidden');
                if (tooltip) tooltip.textContent = 'កំពុងចាក់ភ្លេងការ (Playing)';
            }

            function stopMusic() {
                isPlaying = false;
                clearTimeout(noteTimer);

                aura.classList.add('hidden');
                playIcon.classList.remove('hidden');
                pauseIcon.classList.add('hidden');
                if (tooltip) tooltip.textContent = 'ភ្លេងការប្រពៃណី (Play Music)';
            }

            if (btn) {
                btn.addEventListener('click', () => {
                    isPlaying ? stopMusic() : startMusic();
                });
            }

            // Friendly Auto-Start on First User Engagement Anywhere
            let initialClickDone = false;
            function onFirstGesture() {
                if (initialClickDone) return;
                initialClickDone = true;
                // Don't blast music aggressively, but prepare audio context
                if (!audioCtx) {
                    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                }
                window.removeEventListener('click', onFirstGesture);
                window.removeEventListener('scroll', onFirstGesture);
            }
            window.addEventListener('click', onFirstGesture, { passive: true });
            window.addEventListener('scroll', onFirstGesture, { passive: true });
        })();
    </script>
</body>
</html>