{{-- Link-preview card for Telegram, Messenger and Facebook. Every theme layout includes this in <head>. --}}
@if ($og)
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Mongkol">
    <meta property="og:title" content="{{ $og['title'] }}">
    <meta property="og:description" content="{{ $og['description'] }}">
    <meta property="og:url" content="{{ $og['url'] }}">
    @if ($og['image'])
        <meta property="og:image" content="{{ $og['image'] }}">
        <meta name="twitter:card" content="summary_large_image">
    @endif
@endif
