<?php

namespace App\Console\Commands;

use App\Models\Theme;
use App\Services\Themes\ThemeRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class ThemesSync extends Command
{
    protected $signature = 'themes:sync';

    protected $description = 'Read every theme.json into the themes table (new themes start inactive)';

    public function handle(ThemeRegistry $registry): int
    {
        foreach ($registry->all() as $slug => $manifest) {
            $theme = Theme::firstOrNew(['slug' => $slug]);

            if (! $theme->exists) {
                // Tier and visibility are the admin's call after this, so only a new row takes them.
                // Base is the fallback every invitation can use, so it starts active.
                $theme->tier = $manifest['tier'] ?? 'standard';
                $theme->is_active = $slug === 'base';
            }

            $theme->fill([
                'name' => $manifest['name'],
                'version' => $manifest['version'],
                'manifest' => Arr::except($manifest, ['path', 'chain']),
            ])->save();

            $this->line(sprintf('  %s %s %s', $theme->wasRecentlyCreated ? 'added  ' : 'updated', $slug, $manifest['version']));
        }

        // Kept, not deleted: invitations may still point at a theme whose folder is gone.
        foreach (Theme::whereNotIn('slug', array_keys($registry->all()))->pluck('slug') as $orphan) {
            $this->warn("  {$orphan} is in the database but has no theme folder.");
        }

        return self::SUCCESS;
    }
}
