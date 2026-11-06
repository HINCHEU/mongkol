@php
    $photos = $vm->data['gallery'];
    preg_match('~(?:youtu\.be/|v=|embed/|shorts/)([\w-]{11})~', (string) ($vm->data['video_url'] ?? ''), $match);
    $videoId = $match[1] ?? null;
@endphp
<x-base::section-title section="gallery" />

<div x-data="{ index: null, photos: @js($photos) }">
    @if ($photos)
        <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3">
            @foreach ($photos as $i => $photo)
                <li>
                    <button type="button" class="group block w-full overflow-hidden rounded-lg border border-accent/40 bg-surface shadow-sm transition-all duration-300 hover:border-accent hover:shadow-md cursor-pointer" @click="index = {{ $i }}">
                        <img src="{{ $photo }}" alt="" loading="lazy" class="aspect-[4/5] w-full object-cover transition-transform duration-500 group-hover:scale-105">
                    </button>
                </li>
            @endforeach
        </ul>
        <template x-teleport="body">
            <div class="lightbox" x-show="index !== null" x-cloak @keydown.escape.window="index = null"
                 @keydown.arrow-right.window="index !== null && (index = (index + 1) % photos.length)"
                 @keydown.arrow-left.window="index !== null && (index = (index + photos.length - 1) % photos.length)">
                <img :src="photos[index]" alt="" class="max-h-[85dvh] max-w-full rounded-md object-contain shadow-2xl border border-accent/30" @click="index = (index + 1) % photos.length">
                <p class="t-label-sm absolute top-4 left-4 rounded-full bg-black/60 px-3 py-1 text-gold-300 border border-gold-400/30" x-text="(index + 1) + ' / ' + photos.length"></p>
                <button type="button" class="absolute top-3 right-3 grid size-12 place-items-center rounded-full bg-black/60 text-ivory-50 border border-gold-400/30 transition-colors hover:bg-black/90 cursor-pointer" @click="index = null"
                        aria-label="{{ __('invitation.close', [], 'en') }}"><x-base::icon name="close" class="size-6" /></button>
            </div>
        </template>
    @endif

    @if ($videoId)
        {{-- Video facade: YouTube video with ceremonial play seal --}}
        <div class="relative mt-6 aspect-video overflow-hidden rounded-xl border-2 border-accent/40 bg-ink-950 shadow-md" x-data="{ play: false }">
            <template x-if="play">
                <iframe class="absolute inset-0 size-full" src="https://www.youtube-nocookie.com/embed/{{ $videoId }}?autoplay=1"
                        title="Video" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
            </template>
            <button type="button" class="absolute inset-0 grid place-items-center cursor-pointer" x-show="!play" @click="play = true"
                    aria-label="{{ __('invitation.play_video', [], 'en') }}">
                <img src="https://i.ytimg.com/vi/{{ $videoId }}/hqdefault.jpg" alt="" loading="lazy" class="absolute inset-0 size-full object-cover opacity-75">
                <span class="seal-btn"><x-base::icon name="play" class="size-6 text-gold-300" /></span>
            </button>
        </div>
    @endif
</div>