<?php

namespace App\Services\Themes;

use Illuminate\Contracts\View\View;

// Renders a theme's layout and the manifest's sections, each resolved theme-first, then up the extends chain.
class ThemeRenderer
{
    public function __construct(private readonly ThemeRegistry $registry) {}

    /**
     * @param  array{calendarUrl?: ?string, beaconUrl?: ?string, skipIntro?: bool, og?: ?array}  $extra
     */
    public function render(string $slug, InvitationViewModel $vm, array $extra = []): View
    {
        $theme = $this->registry->get($slug) ?? $this->registry->get('base');
        $candidates = fn (string $name) => array_map(fn ($s) => "theme-{$s}::{$name}", $theme['chain']);

        $sections = [];

        foreach ($theme['sections'] ?? [] as $section) {
            // A section with no content is skipped, never shown empty; one no theme draws yet (rsvp, wishes) too.
            $view = collect($candidates("sections.{$section}"))->first(fn ($v) => view()->exists($v));

            if ($view !== null && $vm->has($section)) {
                $sections[$section] = $view;
            }
        }

        $palette = $vm->data['theme_settings']['palette'] ?? $theme['options']['palette']['default'] ?? 'crimson';

        return view()->first($candidates('layout'), [
            'vm' => $vm,
            'theme' => $theme,
            'sections' => $sections,
            'palette' => $palette,
            'entry' => $this->registry->entry($theme['slug']),
            'calendarUrl' => $extra['calendarUrl'] ?? null,
            'beaconUrl' => $extra['beaconUrl'] ?? null,
            'skipIntro' => $extra['skipIntro'] ?? false,
            'og' => $extra['og'] ?? null,
        ]);
    }
}
