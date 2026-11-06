@php
    $photos = $vm->data['gallery'];
    preg_match('~(?:youtu\.be/|v=|embed/|shorts/)([\w-]{11})~', (string) ($vm->data['video_url'] ?? ''), $match);
    $videoId = $match[1] ?? null;
@endphp
<x-base::section-title section="gallery" />

<div x-data="{ index: null, photos: @js($photos) }">
    @if ($photos)
        <ul class="grid grid-cols-2 gap-2 md:grid-cols-3">
            @foreach ($photos as $i => $photo)
                <li>
                    <button type="button" class="block w-full overflow-hidden rounded-control" @click="index = {{ $i }}">
                        <img src="{{ $photo }}" alt="" loading="lazy" class="aspect-[4/5] w-full object-cover">
                    </button>
                </li>
            @endforeach
        </ul>
        <template x-teleport="body">
            <div class="lightbox" x-show="index !== null" x-cloak @keydown.escape.window="index = null"
                 @keydown.arrow-right.window="index !== null && (index = (index + 1) % photos.length)"
                 @keydown.arrow-left.window="index !== null && (index = (index + photos.length - 1) % photos.length)">
                <img :src="photos[index]" alt="" class="max-h-[85dvh] max-w-full object-contain" @click="index = (index + 1) % photos.length">
                <p class="t-label-sm absolute top-4 left-4 text-ivory-50" x-text="(index + 1) + ' / ' + photos.length"></p>
                <button type="button" class="absolute top-2 right-2 grid size-12 place-items-center text-ivory-50" @click="index = null"
                        aria-label="{{ __('invitation.close', [], 'en') }}"><x-base::icon name="close" /></button>
            </div>
        </template>
    @endif

    @if ($videoId)
        {{-- Video facade (§9.10): the youtube-nocookie iframe loads only after the tap. --}}
        <div class="relative mt-6 aspect-video overflow-hidden rounded-card bg-ink-900" x-data="{ play: false }">
            <template x-if="play">
                <iframe class="absolute inset-0 size-full" src="https://www.youtube-nocookie.com/embed/{{ $videoId }}?autoplay=1"
                        title="Video" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
            </template>
            <button type="button" class="absolute inset-0 grid place-items-center" x-show="!play" @click="play = true"
                    aria-label="{{ __('invitation.play_video', [], 'en') }}">
                <img src="https://i.ytimg.com/vi/{{ $videoId }}/hqdefault.jpg" alt="" loading="lazy" class="absolute inset-0 size-full object-cover opacity-80">
                <span class="seal-btn"><x-base::icon name="play" /></span>
            </button>
        </div>
    @endif
</div>
