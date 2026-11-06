<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeTheme extends Command
{
    protected $signature = 'make:theme {slug : lower-case letters, digits and dashes, e.g. royal-lotus}';

    protected $description = 'Create a theme that extends base; override only the sections you restyle';

    public function handle(): int
    {
        $slug = $this->argument('slug');

        if (! preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $slug)) {
            $this->error('The slug must be lower-case letters, digits and dashes, e.g. royal-lotus.');

            return self::FAILURE;
        }

        $path = resource_path("themes/{$slug}");

        if (File::exists($path)) {
            $this->error("resources/themes/{$slug} already exists.");

            return self::FAILURE;
        }

        File::ensureDirectoryExists("{$path}/views/sections");
        File::ensureDirectoryExists("{$path}/assets");

        File::put("{$path}/theme.json", json_encode([
            'slug' => $slug,
            'name' => Str::headline($slug),
            'version' => '1.0.0',
            'extends' => 'base',
            'tier' => 'standard',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");

        File::put("{$path}/views/sections/.gitkeep", '');
        File::put("{$path}/assets/app.css", "@import \"../../base/assets/app.css\";\n@source \"../views\";\n\n/* Override --inv-* variables and add styles for the sections this theme restyles. */\n");
        File::put("{$path}/assets/app.js", "import './app.css';\nimport '../../base/assets/invitation.js';\n");

        $this->info("Created resources/themes/{$slug}.");
        $this->line('  1. Copy a section from resources/themes/base/views/sections into views/sections and restyle it.');
        $this->line('  2. Add demo.json and thumbnail.webp if this theme needs its own demo.');
        $this->line('  3. Run `php artisan themes:sync`, then activate it in admin.');

        return self::SUCCESS;
    }
}
