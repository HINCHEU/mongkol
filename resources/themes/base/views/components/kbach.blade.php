{{-- Authentic Khmer Kbach Corner Ornament (ក្បាច់កែងភ្ញីទេស) --}}
{{-- Drawn with traditional Khmer architectural and stationery motif proportions. Rotate for other corners. --}}
<svg {{ $attributes->merge(['class' => 'text-accent size-12']) }} viewBox="0 0 64 64" fill="none" stroke="currentColor"
     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <defs>
        <linearGradient id="kbach-gold-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#F5DE93" />
            <stop offset="40%" stop-color="#D4AF37" />
            <stop offset="70%" stop-color="#C5A059" />
            <stop offset="100%" stop-color="#8C6D23" />
        </linearGradient>
    </defs>
    {{-- Outer corner guide & traditional hairline flourish --}}
    <path d="M 2 62 V 22 C 2 10.954 10.954 2 22 2 H 62" stroke-width="1.25" stroke="currentColor" opacity="0.85" />
    <path d="M 6 62 V 24 C 6 14.059 14.059 6 24 6 H 62" stroke-width="0.75" stroke="currentColor" opacity="0.5" stroke-dasharray="2 2" />

    {{-- Main sweeping Khmer Phni flame vine (តួភ្ញី) --}}
    <path d="M 2 44 C 3 34 8 26 16 18 C 24 10 34 5 46 3 C 38 7 32 13 28 20 C 24 27 23 35 25 42 C 26 46 29 49 33 49 C 38 49 41 45 40 40 C 39 34 33 32 30 35 C 28 37 28 40 30 42 C 32 44 35 43 35 41"
          stroke-width="1.35" stroke="currentColor" fill="none" />

    {{-- Upper secondary flame petal (ត្របកភ្ញីខាងលើ) --}}
    <path d="M 33 7 C 39 8 45 11 51 16 C 45 17 40 20 37 25 C 39 21 44 18 50 18 C 55 18 59 21 61 25 C 57 26 53 28 50 32"
          stroke-width="1.1" stroke="currentColor" fill="none" />

    {{-- Lower secondary flame petal (ត្របកភ្ញីខាងក្រោម) --}}
    <path d="M 7 33 C 8 39 11 45 16 51 C 17 45 20 40 25 37 C 21 39 18 44 18 50 C 18 55 21 59 25 61 C 26 57 28 53 32 50"
          stroke-width="1.1" stroke="currentColor" fill="none" />

    {{-- Traditional Khmer pointed flame leaf accents (ចុងក្បាច់រង) --}}
    <path d="M 19 19 C 24 14 31 11 38 10 C 33 13 30 17 28 22 C 26 27 26 31 27 34"
          stroke-width="0.9" stroke="currentColor" opacity="0.7" />

    <path d="M 19 19 C 14 24 11 31 10 38 C 13 33 17 30 22 28 C 27 26 31 26 34 27"
          stroke-width="0.9" stroke="currentColor" opacity="0.7" />

    {{-- Corner jewel / seed motif (គ្រាប់ក្បាច់) --}}
    <circle cx="8" cy="8" r="2.5" fill="currentColor" stroke="none" opacity="0.9" />
    <circle cx="8" cy="8" r="4.5" fill="none" stroke="currentColor" stroke-width="0.75" opacity="0.6" />
    <circle cx="56" cy="4" r="1.5" fill="currentColor" stroke="none" opacity="0.8" />
    <circle cx="4" cy="56" r="1.5" fill="currentColor" stroke="none" opacity="0.8" />
</svg>