{{-- Line icons (§7): 1.5 px stroke, currentColor. --}}
@props(['name'])
<svg {{ $attributes->merge(['class' => 'size-6']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor"
     stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('calendar')
            <rect x="3" y="5" width="18" height="16" rx="2" /><path d="M3 10h18M8 3v4M16 3v4" />
            @break
        @case('list')
            <path d="M9 6h11M9 12h11M9 18h11" /><circle cx="4.5" cy="6" r="1" /><circle cx="4.5" cy="12" r="1" /><circle cx="4.5" cy="18" r="1" />
            @break
        @case('map')
            <path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21Z" /><circle cx="12" cy="9.5" r="2.5" />
            @break
        @case('gift')
            <rect x="3" y="8" width="18" height="13" rx="1.5" /><path d="M3 12h18M12 8v13M12 8c-2.5 0-5-1-5-3s2-2.5 3.5-1.5S12 8 12 8Zm0 0c2.5 0 5-1 5-3s-2-2.5-3.5-1.5S12 8 12 8Z" />
            @break
        @case('music')
            <path d="M9 18V5l11-2v13" /><circle cx="6" cy="18" r="3" /><circle cx="17" cy="16" r="3" />
            @break
        @case('pause')
            <path d="M9 5v14M15 5v14" />
            @break
        @case('play')
            <path d="M8 5v14l11-7Z" />
            @break
        @case('copy')
            <rect x="9" y="9" width="12" height="12" rx="2" /><path d="M5 15H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v1" />
            @break
        @case('download')
            <path d="M12 3v12M7 10l5 5 5-5M4 21h16" />
            @break
        @case('close')
            <path d="M6 6l12 12M18 6 6 18" />
            @break
    @endswitch
</svg>
