<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Themes · Mongkol</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-stone-50 text-stone-900 antialiased">
    <main class="mx-auto max-w-5xl px-4 py-12">
        <h1 class="text-3xl font-semibold">ស្បែកសំបុត្រ <span class="text-stone-500" lang="en">· Themes</span></h1>
        <p class="mt-2 text-stone-600" lang="en">Tap a theme to open its live demo.</p>

        <ul class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($themes as $theme)
                <li>
                    <a href="{{ route('themes.show', $theme->slug) }}"
                       class="block overflow-hidden rounded-lg border border-stone-200 bg-white hover:border-stone-400">
                        <div class="aspect-[4/5] bg-stone-100">
                            @if ($theme->thumbnail_path)
                                <img src="{{ asset($theme->thumbnail_path) }}" alt="" class="size-full object-cover" loading="lazy">
                            @endif
                        </div>
                        <div class="flex items-center justify-between p-4">
                            <span class="font-medium">{{ $theme->name }}</span>
                            @if ($theme->tier === 'premium')
                                <span class="rounded bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-900">Premium</span>
                            @endif
                        </div>
                    </a>
                </li>
            @empty
                <li class="text-stone-600" lang="en">No themes yet.</li>
            @endforelse
        </ul>
    </main>
</body>
</html>
