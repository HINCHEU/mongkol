@props(['section'])
<header class="mb-8 text-center">
    {{-- Sacred mini crest above section header --}}
    <div class="mx-auto mb-2 flex items-center justify-center gap-2 text-accent">
        <span class="h-px w-6 bg-gradient-to-r from-transparent to-accent" aria-hidden="true"></span>
        <svg class="size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
            <path d="M8 1L9.8 5.4L14.5 6L11 9.2L12 14L8 11.6L4 14L5 9.2L1.5 6L6.2 5.4L8 1Z" />
        </svg>
        <span class="h-px w-6 bg-gradient-to-l from-transparent to-accent" aria-hidden="true"></span>
    </div>

    <h2 id="{{ $section }}-title" class="t-headline text-primary" lang="km">{{ __("invitation.sections.{$section}", [], 'km') }}</h2>
    <p class="t-label-sm mt-1 text-accent-text tracking-widest uppercase" lang="en">{{ __("invitation.sections.{$section}", [], 'en') }}</p>

    <x-base::divider class="mx-auto mt-3 w-28 text-accent" />
</header>