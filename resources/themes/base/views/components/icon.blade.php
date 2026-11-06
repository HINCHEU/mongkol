{{-- Line & Ceremonial icons: 1.5 px stroke, currentColor. --}}
@props(['name'])
<svg {{ $attributes->merge(['class' => 'size-6']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor"
     stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('calendar')
            <rect x="3" y="4" width="18" height="18" rx="2.5" />
            <path d="M3 10h18M8 2v4M16 2v4" />
            <circle cx="8" cy="14" r="1" fill="currentColor" stroke="none" />
            <circle cx="12" cy="14" r="1" fill="currentColor" stroke="none" />
            <circle cx="16" cy="14" r="1" fill="currentColor" stroke="none" />
            <circle cx="8" cy="17" r="1" fill="currentColor" stroke="none" />
            <circle cx="12" cy="17" r="1" fill="currentColor" stroke="none" />
            @break
        @case('list')
            <path d="M9 6h11M9 12h11M9 18h11" />
            <circle cx="4.5" cy="6" r="1.25" fill="currentColor" stroke="none" />
            <circle cx="4.5" cy="12" r="1.25" fill="currentColor" stroke="none" />
            <circle cx="4.5" cy="18" r="1.25" fill="currentColor" stroke="none" />
            @break
        @case('map')
            <path d="M12 21.5C12 21.5 5 14.5 5 9.5A7 7 0 0 1 19 9.5C19 14.5 12 21.5 12 21.5Z" />
            <circle cx="12" cy="9.5" r="2.5" />
            @break
        @case('gift')
            <rect x="3" y="8" width="18" height="13" rx="2" />
            <path d="M3 12h18M12 8v13" />
            <path d="M12 8C10 5.5 7.5 5.5 6 7s1 5 6 1ZM12 8C14 5.5 16.5 5.5 18 7s-1 5-6 1Z" />
            @break
        @case('music')
            <path d="M9 18V5l11-2v13" />
            <circle cx="6" cy="18" r="3" />
            <circle cx="17" cy="16" r="3" />
            @break
        @case('pause')
            <line x1="8" y1="5" x2="8" y2="19" stroke-width="2" />
            <line x1="16" y1="5" x2="16" y2="19" stroke-width="2" />
            @break
        @case('play')
            <polygon points="7 4 20 12 7 20 7 4" fill="currentColor" stroke="none" />
            @break
        @case('copy')
            <rect x="8.5" y="8.5" width="12" height="12" rx="2" />
            <path d="M5 15.5H4a1.5 1.5 0 0 1-1.5-1.5V4a1.5 1.5 0 0 1 1.5-1.5h10A1.5 1.5 0 0 1 15.5 4v1" />
            @break
        @case('download')
            <path d="M12 3v12M7.5 10.5L12 15l4.5-4.5M4 21h16" />
            @break
        @case('close')
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
            @break
        @case('lotus')
            <path d="M12 3c-2.5 3.5-3 8 0 13c3-5 2.5-9.5 0-13Z" />
            <path d="M12 16c-4.5-.5-8-3.5-9-8c4 .5 7 2.5 9 6" />
            <path d="M12 16c4.5-.5 8-3.5 9-8c-4 .5-7 2.5-9 6" />
            <path d="M6 18c3 2 9 2 12 0" />
            @break
        @case('knot')
            <circle cx="8" cy="12" r="5" />
            <circle cx="16" cy="12" r="5" />
            <path d="M12 8.5v7" />
            @break
        @case('rings')
            <circle cx="9" cy="13" r="5.5" />
            <circle cx="15" cy="11" r="5.5" />
            <path d="M15 3.5l1.5 1.5L15 6.5l-1.5-1.5Z" fill="currentColor" />
            @break
        @case('heart')
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
            @break
        @default
            <circle cx="12" cy="12" r="9" />
    @endswitch
</svg>