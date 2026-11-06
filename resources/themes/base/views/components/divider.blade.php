{{-- Lotus divider (§7): 1.25 px stroke in currentColor, gold-500 by default. --}}
<svg {{ $attributes->merge(['class' => 'text-accent']) }} viewBox="0 0 120 24" fill="none" stroke="currentColor"
     stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M4 14h36M80 14h36" />
    <circle cx="44" cy="14" r="1.25" fill="currentColor" stroke="none" />
    <circle cx="76" cy="14" r="1.25" fill="currentColor" stroke="none" />
    <path d="M60 3c-4 4.5-4 11 0 17c4-6 4-12.5 0-17Z" />
    <path d="M60 20c-6.5-.5-11.5-4.5-13-10.5c5.5.5 9.5 3.5 12 7.5" />
    <path d="M60 20c6.5-.5 11.5-4.5 13-10.5c-5.5.5-9.5 3.5-12 7.5" />
    <path d="M52 21h16" />
</svg>
