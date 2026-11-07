<?php

namespace App\Services\Themes;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Reads every resources/themes/{slug}/theme.json, resolves `extends`, and registers
 * each theme's views as the `theme-{slug}` namespace.
 *
 * A resolved manifest carries `path` (its own folder) and `chain` (itself, then each
 * ancestor), which is the order views and assets are looked up in.
 */
class ThemeRegistry
{
    /** @var array<string, array<string, mixed>>|null */
    private ?array $themes = null;

    /** @param list<string> $paths folders that hold theme folders */
    public function __construct(private readonly array $paths) {}

    /** @return array<string, array<string, mixed>> */
    public function all(): array
    {
        // shortcut: re-reads the manifests on every request (a few small files); cache them if themes grow to dozens.
        return $this->themes ??= $this->load();
    }

    public function get(string $slug): ?array
    {
        return $this->all()[$slug] ?? null;
    }

    public function registerViews(): void
    {
        foreach ($this->all() as $slug => $theme) {
            View::addNamespace("theme-{$slug}", $theme['path'].'/views');
        }

        if ($base = $this->get('base')) {
            Blade::anonymousComponentPath($base['path'].'/views/components', 'base');
        }
    }

    /** The theme's Vite entry, relative to the project root: its own, or the nearest ancestor's. */
    public function entry(string $slug): string
    {
        foreach ($this->get($slug)['chain'] as $name) {
            $file = $this->all()[$name]['path'].'/assets/app.js';

            if (is_file($file)) {
                return Str::after(str_replace('\\', '/', $file), str_replace('\\', '/', base_path()).'/');
            }
        }

        throw new RuntimeException("Theme [{$slug}] has no assets/app.js in its chain.");
    }

    /** @return array<string, array<string, mixed>> */
    private function load(): array
    {
        $raw = [];

        foreach ($this->paths as $path) {
            foreach (glob($path.'/*/theme.json') ?: [] as $file) {
                $manifest = json_decode(file_get_contents($file), true, flags: JSON_THROW_ON_ERROR);
                $raw[$manifest['slug']] = $manifest + ['path' => dirname($file)];
            }
        }

        $resolved = [];

        foreach ($raw as $slug => $manifest) {
            $chain = [$slug];
            $parent = $manifest['extends'] ?? null;

            while ($parent !== null) {
                if (! isset($raw[$parent]) || in_array($parent, $chain, true)) {
                    throw new RuntimeException("Theme [{$slug}] extends [{$parent}], which is missing or circular.");
                }

                $chain[] = $parent;
                // Options merge one level down, so a child can replace one (its own palettes) and keep the rest.
                $manifest['options'] = ($manifest['options'] ?? []) + ($raw[$parent]['options'] ?? []);
                $manifest += $raw[$parent]; // the child's own keys win; ancestors fill the gaps
                $parent = $raw[$parent]['extends'] ?? null;
            }

            $resolved[$slug] = ['chain' => $chain] + $manifest;
        }

        return $resolved;
    }
}
