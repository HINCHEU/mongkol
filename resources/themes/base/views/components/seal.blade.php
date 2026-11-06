{{-- Authentic Royal Khmer Wedding Seal (ត្រាមង្គលការ) --}}
{{-- 16-lobed lotus petal ring, gold bead track, royal lacquer field, and Khmer blessing insignia --}}
@props(['initials'])
<svg {{ $attributes->merge(['class' => 'size-22']) }} viewBox="0 0 100 100" role="img" aria-label="{{ $initials }}">
    <defs>
        <linearGradient id="seal-gold" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#FFE8A3" />
            <stop offset="25%" stop-color="#E5BE62" />
            <stop offset="50%" stop-color="#BF8D29" />
            <stop offset="75%" stop-color="#F2D37F" />
            <stop offset="100%" stop-color="#875E14" />
        </linearGradient>
        <radialGradient id="seal-lacquer" cx="40%" cy="35%" r="65%">
            <stop offset="0%" stop-color="var(--inv-primary-hover, #6B111E)" />
            <stop offset="60%" stop-color="var(--inv-primary, #58111A)" />
            <stop offset="100%" stop-color="#2D060B" />
        </radialGradient>
        <filter id="seal-shadow" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="4" stdDeviation="4" flood-color="#3A080E" flood-opacity="0.35" />
        </filter>
    </defs>

    <g filter="url(#seal-shadow)">
        {{-- Outer 16-lobed scalloped lotus petal rim (ត្រកៀកផ្កាឈូក/ក្បាច់ផ្កាច័ន្ទ) --}}
        <path d="
            M 50 2
            C 53 2, 56 4, 58 6   C 61 5, 64 6, 66 8   C 69 7, 72 9, 74 12  C 77 12, 79 14, 81 17
            C 84 18, 86 21, 87 24 C 89 26, 91 29, 92 32 C 94 35, 95 38, 95 42 C 96 45, 97 47, 98 50
            C 97 53, 96 55, 95 58 C 95 62, 94 65, 92 68 C 91 71, 89 74, 87 76 C 86 79, 84 82, 81 83
            C 79 86, 77 88, 74 88 C 72 91, 69 93, 66 92 C 64 94, 61 95, 58 94 C 56 96, 53 98, 50 98
            C 47 98, 44 96, 42 94 C 39 95, 36 94, 34 92 C 31 93, 28 91, 26 88 C 23 88, 21 86, 19 83
            C 16 82, 14 79, 13 76 C 11 74, 9 71, 8 68   C 6 65, 5 62, 5 58   C 4 55, 3 53, 2 50
            C 3 47, 4 45, 5 42   C 5 38, 6 35, 8 32   C 9 29, 11 26, 13 24 C 14 21, 16 18, 19 17
            C 21 14, 23 12, 26 12 C 28 9, 31 7, 34 8   C 36 6, 39 5, 42 6   C 44 4, 47 2, 50 2 Z
        " fill="url(#seal-gold)" />

        {{-- Inner bevel gold border --}}
        <circle cx="50" cy="50" r="44" fill="none" stroke="url(#seal-gold)" stroke-width="1.5" />

        {{-- Beaded gold track (គ្រាប់អង្កាំមាស) --}}
        <circle cx="50" cy="50" r="41" fill="none" stroke="url(#seal-gold)" stroke-width="1.25" stroke-dasharray="1.5 2.5" opacity="0.85" />

        {{-- Lacquered royal center dome --}}
        <circle cx="50" cy="50" r="38" fill="url(#seal-lacquer)" stroke="url(#seal-gold)" stroke-width="1.5" />
        <circle cx="50" cy="50" r="35" fill="none" stroke="url(#seal-gold)" stroke-width="0.75" opacity="0.6" />

        {{-- Top Auspicious Khmer blessing curve text: "សិរីសួស្តី" --}}
        <path id="top-curve" d="M 23 48 A 29 29 0 0 1 77 48" fill="none" />
        <text font-family="'Kantumruy Pro', sans-serif" font-weight="600" font-size="6.8" fill="url(#seal-gold)" letter-spacing="0">
            <textPath href="#top-curve" startOffset="50%" text-anchor="middle">សិរីសួស្តី</textPath>
        </text>

        {{-- Center Couple Initials / Monogram in Regal Playfair Italic & Gold Foil --}}
        <text x="50" y="55" text-anchor="middle" font-family="'Playfair Display', serif" font-style="italic" font-weight="600"
              font-size="20" fill="url(#seal-gold)" letter-spacing="0.02em">{{ $initials }}</text>

        {{-- Bottom Auspicious Khmer curve text: "មង្គលការ" --}}
        <path id="bottom-curve" d="M 77 52 A 29 29 0 0 1 23 52" fill="none" />
        <text font-family="'Kantumruy Pro', sans-serif" font-weight="600" font-size="6.8" fill="url(#seal-gold)" letter-spacing="0">
            <textPath href="#bottom-curve" startOffset="50%" text-anchor="middle">មង្គលការ</textPath>
        </text>

        {{-- Flanking tiny gold stars --}}
        <circle cx="21" cy="50" r="1.2" fill="url(#seal-gold)" />
        <circle cx="79" cy="50" r="1.2" fill="url(#seal-gold)" />
    </g>
</svg>