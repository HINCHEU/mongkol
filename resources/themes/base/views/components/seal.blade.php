{{-- Monogram seal (§7): primary fill, gold-leaf ring, initials in Playfair italic. --}}
@props(['initials'])
<svg {{ $attributes->merge(['class' => 'size-22']) }} viewBox="0 0 88 88" role="img" aria-label="{{ $initials }}">
    <defs>
        <linearGradient id="gold-leaf" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#d4af37" />
            <stop offset=".45" stop-color="#c5a059" />
            <stop offset="1" stop-color="#8c6d23" />
        </linearGradient>
    </defs>
    <circle cx="44" cy="44" r="42" style="fill: var(--inv-primary)" stroke="url(#gold-leaf)" stroke-width="3" />
    <circle cx="44" cy="44" r="35" fill="none" stroke="url(#gold-leaf)" stroke-width=".75" />
    <text x="44" y="51" text-anchor="middle" font-family="Playfair Display, serif" font-style="italic"
          font-size="22" style="fill: var(--inv-on-primary)">{{ $initials }}</text>
</svg>
