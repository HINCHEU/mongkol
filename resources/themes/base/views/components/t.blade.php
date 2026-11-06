{{-- Both languages; CSS shows the one <html lang> names. Pass a lang key, or km/en text (en falls back to km). --}}
@props(['key' => null, 'km' => null, 'en' => null])
@php
    $km = $key ? __("invitation.{$key}", [], 'km') : $km;
    $en = $key ? __("invitation.{$key}", [], 'en') : ($en ?: $km);
@endphp
<span lang="km" data-l="km">{{ $km }}</span><span lang="en" data-l="en">{{ $en }}</span>
