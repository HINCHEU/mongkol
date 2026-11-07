<?php

namespace App\Services\Themes;

use Illuminate\Contracts\View\View;

// Renders a theme's layout and the manifest's sections, each resolved theme-first, then up the extends chain.
class ThemeRenderer
{
    public function __construct(private readonly ThemeRegistry $registry) {}

    /**
     * @param  array{calendarUrl?: ?string, beaconUrl?: ?string, skipIntro?: bool, og?: ?array, formsUrl?: ?string}  $extra
     */
    public function render(string $slug, InvitationViewModel $vm, array $extra = []): View
    {
        $theme = $this->registry->get($slug) ?? $this->registry->get('base');
        $candidates = fn (string $name) => array_map(fn ($s) => "theme-{$s}::{$name}", $theme['chain']);

        $settings = $vm->data['theme_settings'] ?? [];
        $sections = [];

        foreach (self::arrange($theme['sections'] ?? [], $settings) as $section) {
            // A section with no content is skipped, never shown empty; one no theme draws yet (rsvp, wishes) too.
            $view = collect($candidates("sections.{$section}"))->first(fn ($v) => view()->exists($v));

            if ($view !== null && $vm->has($section)) {
                $sections[$section] = $view;
            }
        }

        // A choice the theme doesn't offer (saved under another theme) falls back to its default.
        $choice = fn (string $key, string $fallback) => in_array($settings[$key] ?? null, $theme['options'][$key]['choices'] ?? [], true)
            ? $settings[$key] : ($theme['options'][$key]['default'] ?? $fallback);

        return view()->first($candidates('layout'), [
            'vm' => $vm,
            'theme' => $theme,
            'sections' => $sections,
            'palette' => $choice('palette', 'crimson'),
            'font' => $choice('font', 'classic'),
            'entry' => $this->registry->entry($theme['slug']),
            'calendarUrl' => $extra['calendarUrl'] ?? null,
            'beaconUrl' => $extra['beaconUrl'] ?? null,
            // Where RSVP and wishes post to; null in the demo and the editor preview, which only show the forms.
            'formsUrl' => $extra['formsUrl'] ?? null,
            'skipIntro' => $extra['skipIntro'] ?? false,
            'og' => $extra['og'] ?? null,
        ]);
    }

    /**
     * The couple's order and hidden sections (Premium theme options). The cover always opens the page;
     * a section they never placed, say one a theme update adds, keeps its manifest order at the end.
     *
     * @param  list<string>  $sections
     * @return list<string>
     */
    public static function arrange(array $sections, array $settings): array
    {
        $order = array_flip($settings['order'] ?? []);
        $rest = array_values(array_filter($sections, fn ($s) => $s !== 'cover' && ! in_array($s, $settings['hidden'] ?? [], true)));
        usort($rest, fn ($a, $b) => ($order[$a] ?? PHP_INT_MAX) <=> ($order[$b] ?? PHP_INT_MAX)); // stable since PHP 8

        return in_array('cover', $sections, true) ? ['cover', ...$rest] : $rest;
    }
}
