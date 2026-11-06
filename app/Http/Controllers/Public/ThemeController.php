<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Services\Themes\InvitationViewModel;
use App\Services\Themes\ThemeRegistry;
use App\Services\Themes\ThemeRenderer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

// The theme gallery and each theme's live demo, rendered from its demo.json (or base's).
class ThemeController extends Controller
{
    public function __construct(private readonly ThemeRegistry $registry) {}

    public function index(): View
    {
        return view('public.themes', [
            'themes' => Theme::where('is_active', true)->orderBy('sort')->orderBy('name')->get(),
        ]);
    }

    public function show(string $slug, ThemeRenderer $renderer): View
    {
        return $renderer->render($slug, $this->demo($slug), [
            'calendarUrl' => route('themes.calendar', $slug),
        ]);
    }

    public function calendar(string $slug): Response
    {
        return response($this->demo($slug)->ics(), 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="wedding.ics"',
        ]);
    }

    private function demo(string $slug): InvitationViewModel
    {
        $theme = $this->registry->get($slug);

        abort_unless($theme && Theme::where('slug', $slug)->where('is_active', true)->exists(), 404);

        foreach ($theme['chain'] as $name) {
            $file = $this->registry->get($name)['path'].'/demo.json';

            if (is_file($file)) {
                return InvitationViewModel::fromArray(json_decode(file_get_contents($file), true, flags: JSON_THROW_ON_ERROR));
            }
        }

        abort(404);
    }
}
