{{-- Privacy, terms and refunds. Plain on purpose; styling is the design pass's job. --}}
<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $text['title'] }} · Mongkol</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite('resources/css/app.css')
</head>
<body class="min-h-dvh bg-[#FAF7F2] text-stone-900">
    <main class="mx-auto max-w-2xl px-4 py-10 grid gap-6 leading-relaxed">
        <nav class="flex flex-wrap items-center justify-between gap-3 text-sm">
            <a href="{{ url('/') }}" class="font-bold">Mongkol · <span lang="km">មង្គល</span></a>
            <span class="flex gap-3">
                <a href="?lang=km" lang="km" @if ($lang === 'km') aria-current="page" class="font-bold" @endif>ខ្មែរ</a>
                <a href="?lang=en" lang="en" @if ($lang === 'en') aria-current="page" class="font-bold" @endif>English</a>
            </span>
        </nav>

        <header>
            <h1 class="text-2xl font-bold">{{ $text['title'] }}</h1>
            <p class="text-xs text-stone-500 mt-1">{{ $common['updated'] }}</p>
        </header>

        @if (! empty($text['intro']))
            <p>{{ $text['intro'] }}</p>
        @endif

        @foreach ($text['sections'] as $section)
            <section class="grid gap-2">
                <h2 class="text-lg font-semibold">{{ $section['heading'] }}</h2>
                @foreach ($section['body'] as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </section>
        @endforeach

        <p>{{ $common['contact'] }} <a href="mailto:{{ config('app.contact_email') }}" class="underline">{{ config('app.contact_email') }}</a></p>

        <footer class="flex flex-wrap gap-4 border-t border-stone-200 pt-4 text-sm">
            @foreach (\App\Http\Controllers\Public\LegalController::PAGES as $other)
                <a href="{{ route('legal', $other) }}?lang={{ $lang }}" @if ($other === $page) aria-current="page" class="font-bold" @endif>{{ trans("legal.{$other}.title", [], $lang) }}</a>
            @endforeach
        </footer>
    </main>
</body>
</html>
