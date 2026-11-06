@props(['section'])
<header class="mb-7 text-center">
    <h2 id="{{ $section }}-title" class="t-headline text-primary" lang="km">{{ __("invitation.sections.{$section}", [], 'km') }}</h2>
    <p class="t-label-sm mt-1 text-accent-text" lang="en">{{ __("invitation.sections.{$section}", [], 'en') }}</p>
    <x-base::divider class="mx-auto mt-3 w-16" />
</header>
