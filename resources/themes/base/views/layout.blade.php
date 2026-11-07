@php
    $guestLine = $vm->guest ? trim(implode(' ', array_filter([$vm->guest['title'], $vm->guest['name'], $vm->guest['suffix']]))) : null;
    // The classic pairing always loads (the seal draws in Kantumruy Pro and Playfair); a font option adds its own.
    $extraFonts = [
        'modern' => '&family=Cormorant+Garamond:ital,wght@0,500;0,600;1,400&family=Koulen',
        'traditional' => '&family=Cinzel:wght@500;600&family=Moulpali&family=Lora:ital,wght@0,400;0,600;1,400&family=Battambang:wght@400;700',
    ];
    $dock = array_intersect_key(['date' => 'calendar', 'program' => 'list', 'venues' => 'map', 'gift' => 'gift'], $sections);
@endphp
<!DOCTYPE html>
<html lang="{{ $vm->data['locale'] === 'en' ? 'en' : 'km' }}" data-palette="{{ $palette }}" data-font="{{ $font }}"
      data-lang-key="inv-lang:{{ $vm->data['slug'] }}" @class(['has-ribbon' => $vm->isPreview, 'is-open' => $skipIntro])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light">
    <meta name="theme-color" content="#7B1122">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $vm->coupleNames('km') }}</title>
    @include('partials.og')

    <!-- Favicon & Touch Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <script>
        {{-- Before paint: mark JavaScript on (enables the envelope) and restore the guest's language. --}}
        document.documentElement.classList.add('js');
        try {
            const lang = localStorage.getItem(document.documentElement.dataset.langKey);
            if (lang === 'km' || lang === 'en') document.documentElement.lang = lang;
        } catch (e) {}
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;600&family=Moul&family=Playfair+Display:ital,wght@0,500;0,600;1,400&family=Plus+Jakarta+Sans:wght@400;600{{ $extraFonts[$font] ?? '' }}&display=swap">
    @vite($entry)
</head>
<body class="bg-canvas text-ink antialiased" x-data>
    {{-- Theme option: falling lotus, jasmine and romduol petals over the page (invitation.js draws them). --}}
    @if ($vm->data['theme_settings']['petals'] ?? $theme['options']['petals']['default'] ?? false)
        <canvas class="petals" aria-hidden="true"></canvas>
    @endif
    @if ($vm->isPreview)
        <div class="ribbon t-label-sm" role="note"><span lang="en">PREVIEW</span>&nbsp;·&nbsp;<span lang="km">{{ __('invitation.preview', [], 'km') }}</span></div>
    @endif

    {{-- Royal Khmer Wedding Envelope (ស្រោមសំបុត្រមង្គលការ): One tap to unfold the ceremonial folio --}}
    <div class="envelope" x-data="envelope(@js($beaconUrl))" :class="{ 'is-opening': opening }" role="dialog" aria-modal="true" aria-labelledby="envelope-guest">
        {{-- Dimensional Envelope Flap & Gold Edge --}}
        <div class="envelope__flap" aria-hidden="true"></div>
        <svg class="envelope__flap-edge w-full" viewBox="0 0 100 42" preserveAspectRatio="none" aria-hidden="true">
            <path d="M0 0 L50 42 L100 0" fill="none" stroke="currentColor" stroke-width=".5" vector-effect="non-scaling-stroke" />
        </svg>

        {{-- Inner Gold Frame & Corner Ornaments --}}
        <div class="envelope__frame" aria-hidden="true">
            <x-base::kbach class="absolute top-1 left-1 size-7 text-gold-400 opacity-60" />
            <x-base::kbach class="absolute top-1 right-1 size-7 text-gold-400 opacity-60 rotate-90" />
            <x-base::kbach class="absolute bottom-1 left-1 size-7 text-gold-400 opacity-60 -rotate-90" />
            <x-base::kbach class="absolute bottom-1 right-1 size-7 text-gold-400 opacity-60 rotate-180" />
        </div>

        {{-- Royal Wax Seal (ត្រាមង្គល) --}}
        <button type="button" @click="open()" class="envelope__seal group cursor-pointer focus:outline-none" aria-label="{{ __('invitation.open_invitation', [], 'km') }}">
            <x-base::seal :initials="$vm->initials()" class="size-24 transition-transform duration-300 group-hover:scale-105" />
        </button>

        {{-- Auspicious Invitation Text --}}
        <div class="relative z-10 flex flex-col items-center">
            <div class="flex items-center gap-2 text-gold-400">
                <span class="h-px w-5 bg-gold-400/50" aria-hidden="true"></span>
                <p class="t-label text-gold-300 tracking-wider"><x-base::t key="invite" /></p>
                <span class="h-px w-5 bg-gold-400/50" aria-hidden="true"></span>
            </div>

            <p id="envelope-guest" class="t-guest mt-2 max-w-sm px-4 text-ivory-50 drop-shadow">
                @if ($guestLine)
                    <span lang="km" class="t-guest text-gold-200">{{ $guestLine }}</span>
                @else
                    <x-base::t key="honoured_guest" />
                @endif
            </p>

            <p class="t-body-sm mt-1 max-w-xs text-gold-200/80">
                <span lang="km">ចូលរួមជាភ្ញៀវកិត្តិយស ក្នុងពិធីមង្គលការ</span>
                <span lang="en">Cordially invited to our wedding celebration</span>
            </p>
        </div>

        {{-- Ceremonial Open Button --}}
        <button type="button" class="btn btn-on-primary t-label relative z-10 mt-3" @click="open()">
            <x-base::icon name="lotus" class="size-4 text-gold-300" />
            <x-base::t key="open_invitation" />
        </button>
    </div>

    {{-- Language Toggle --}}
    <div class="lang-toggle t-label-sm" role="group" aria-label="{{ __('invitation.language', [], 'km') }} / Language">
        <button type="button" lang="km" @click="$store.lang.set('km')" :aria-pressed="$store.lang.current === 'km'">ខ្មែរ</button>
        <button type="button" lang="en" @click="$store.lang.set('en')" :aria-pressed="$store.lang.current === 'en'">EN</button>
    </div>

    {{-- Main Stationery Folio Canvas --}}
    <main class="inv-canvas">
        {{-- Folio Corner Kbach Accents --}}
        <x-base::kbach class="pointer-events-none absolute top-3 left-3 size-9 text-accent opacity-50" />
        <x-base::kbach class="pointer-events-none absolute top-3 right-3 size-9 text-accent opacity-50 rotate-90" />

        @foreach ($sections as $name => $view)
            @unless ($loop->first)
                <x-base::divider class="mx-auto my-2 w-36 text-accent" />
            @endunless
            <section id="{{ $name }}" class="inv-section reveal" aria-labelledby="{{ $name }}-title">
                @include($view)
            </section>
        @endforeach

        <x-base::kbach class="pointer-events-none absolute bottom-3 left-3 size-9 text-accent opacity-50 -rotate-90" />
        <x-base::kbach class="pointer-events-none absolute bottom-3 right-3 size-9 text-accent opacity-50 rotate-180" />

        {{-- Ceremonial Folio Footer --}}
        <footer class="mt-8 border-t border-hairline pt-8 text-center">
            <x-base::seal :initials="$vm->initials()" class="mx-auto size-14" />
            <p class="mt-4 text-ink-muted">
                <x-base::t :km="$vm->text('thank_you')['km'] ?? __('invitation.thank_you_line', [], 'km')"
                           :en="$vm->text('thank_you')['en'] ?? __('invitation.thank_you_line', [], 'en')" />
            </p>
            @if ($vm->data['show_credit'])
                <p class="t-body-sm mt-5 text-accent-text/70"><x-base::t key="made_with" /></p>
            @endif
            {{-- Guests' names are stored too, so every invitation links the privacy policy. --}}
            <p class="t-body-sm mt-3 text-accent-text/60">
                <a href="{{ route('legal', 'privacy') }}" class="underline decoration-accent-text/30 hover:text-accent-strong transition-colors focus:outline-none focus:ring-1 focus:ring-accent rounded px-1">
                    <x-base::t key="privacy" />
                </a>
            </p>
        </footer>
    </main>

    {{-- Traditional Phleng Kar Music Player --}}
    @if ($vm->data['music'])
        <div class="music-seal" x-data="music" :class="{ 'is-playing': playing }">
            <audio x-ref="audio" src="{{ $vm->data['music'] }}" loop preload="none"></audio>
            <button type="button" class="seal-btn" @click="toggle()"
                    :aria-label="playing ? @js(__('invitation.pause', [], 'km').' · '.__('invitation.pause', [], 'en')) : @js(__('invitation.play_music', [], 'km').' · '.__('invitation.play_music', [], 'en'))">
                <x-base::icon name="music" x-show="!playing" class="size-5" />
                <x-base::icon name="pause" x-show="playing" x-cloak class="size-5" />
            </button>
        </div>
    @endif

    {{-- Floating Quick Dock --}}
    @if (count($dock) > 1)
        <nav class="dock" x-data="dock" :class="{ 'is-hidden': hidden }" aria-label="{{ __('invitation.sections.program', [], 'en') }}">
            @foreach ($dock as $section => $icon)
                <a href="#{{ $section }}" class="t-label-sm">
                    <x-base::icon :name="$icon" class="size-5 text-accent-strong" />
                    <x-base::t :km="__('invitation.dock.'.$section, [], 'km')" :en="__('invitation.dock.'.$section, [], 'en')" />
                </a>
            @endforeach
        </nav>
    @endif

    {{-- Action Toast --}}
    <div class="toast t-body-sm" role="status" x-show="$store.toast.message" x-text="$store.toast.message" x-cloak></div>
</body>
</html>