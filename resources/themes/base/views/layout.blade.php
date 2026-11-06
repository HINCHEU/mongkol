@php
    $guestLine = $vm->guest ? trim(implode(' ', array_filter([$vm->guest['title'], $vm->guest['name'], $vm->guest['suffix']]))) : null;
    $dock = array_intersect_key(['date' => 'calendar', 'program' => 'list', 'venues' => 'map', 'gift' => 'gift'], $sections);
@endphp
<!DOCTYPE html>
<html lang="{{ $vm->data['locale'] === 'en' ? 'en' : 'km' }}" data-palette="{{ $palette }}"
      data-lang-key="inv-lang:{{ $vm->data['slug'] }}" @class(['has-ribbon' => $vm->isPreview])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $vm->coupleNames('km') }}</title>
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;600&family=Moul&family=Playfair+Display:ital,wght@0,500;0,600;1,400&family=Plus+Jakarta+Sans:wght@400;600&display=swap">
    @vite($entry)
</head>
<body class="bg-canvas text-ink antialiased" x-data>
    @if ($vm->isPreview)
        <div class="ribbon t-label-sm" role="note"><span lang="en">PREVIEW</span>&nbsp;·&nbsp;<span lang="km">{{ __('invitation.preview', [], 'km') }}</span></div>
    @endif

    {{-- Envelope (§10): the guest's name, one tap to open. --}}
    <div class="envelope" x-data="envelope(@js($beaconUrl))" :class="{ 'is-opening': opening }" role="dialog" aria-modal="true" aria-labelledby="envelope-guest">
        <div class="envelope__flap" aria-hidden="true"></div>
        <svg class="envelope__flap-edge w-full" viewBox="0 0 100 38" preserveAspectRatio="none" aria-hidden="true">
            <path d="M0 0 50 38 100 0" fill="none" stroke="currentColor" stroke-width=".4" vector-effect="non-scaling-stroke" />
        </svg>
        <x-base::seal :initials="$vm->initials()" class="envelope__seal size-22" />
        <p class="t-label text-gold-400"><x-base::t key="invite" /></p>
        <p id="envelope-guest" class="t-guest max-w-xs">
            @if ($guestLine)
                {{-- Guest lines are stored in Khmer, so they keep Khmer metrics in either language. --}}
                <span lang="km" class="t-guest">{{ $guestLine }}</span>
            @else
                <x-base::t key="honoured_guest" />
            @endif
        </p>
        <button type="button" class="btn btn-on-primary t-label mt-4" @click="open()">
            <x-base::t key="open_invitation" />
        </button>
    </div>

    <div class="lang-toggle t-label-sm" role="group" aria-label="{{ __('invitation.language', [], 'km') }} / Language">
        <button type="button" lang="km" @click="$store.lang.set('km')" :aria-pressed="$store.lang.current === 'km'">ខ្មែរ</button>
        <button type="button" lang="en" @click="$store.lang.set('en')" :aria-pressed="$store.lang.current === 'en'">EN</button>
    </div>

    <main class="inv-canvas">
        @foreach ($sections as $name => $view)
            @unless ($loop->first)
                <x-base::divider class="mx-auto w-30" />
            @endunless
            <section id="{{ $name }}" class="inv-section reveal" aria-labelledby="{{ $name }}-title">
                @include($view)
            </section>
        @endforeach

        <footer class="mt-10 border-t border-hairline pt-10 text-center">
            <x-base::seal :initials="$vm->initials()" class="mx-auto size-12" />
            <p class="mt-4 text-ink-muted">
                <x-base::t :km="$vm->text('thank_you')['km'] ?? __('invitation.thank_you_line', [], 'km')"
                           :en="$vm->text('thank_you')['en'] ?? __('invitation.thank_you_line', [], 'en')" />
            </p>
            @if ($vm->data['show_credit'])
                <p class="t-body-sm mt-6 text-ink-muted"><x-base::t key="made_with" /></p>
            @endif
        </footer>
    </main>

    @if ($vm->data['music'])
        <div class="music-seal" x-data="music" :class="{ 'is-playing': playing }">
            <audio x-ref="audio" src="{{ $vm->data['music'] }}" loop preload="none"></audio>
            <button type="button" class="seal-btn" @click="toggle()"
                    :aria-label="playing ? @js(__('invitation.pause', [], 'km').' · '.__('invitation.pause', [], 'en')) : @js(__('invitation.play_music', [], 'km').' · '.__('invitation.play_music', [], 'en'))">
                <x-base::icon name="music" x-show="!playing" />
                <x-base::icon name="pause" x-show="playing" x-cloak />
            </button>
        </div>
    @endif

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

    <div class="toast t-body-sm" role="status" x-show="$store.toast.message" x-text="$store.toast.message" x-cloak></div>
</body>
</html>
