<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

// /privacy, /terms and /refunds in Khmer (default) or English. Text: lang/{km,en}/legal.php.
class LegalController extends Controller
{
    public const PAGES = ['privacy', 'terms', 'refunds'];

    public function __invoke(Request $request, string $page): View
    {
        $lang = in_array($request->query('lang'), ['km', 'en'], true) ? $request->query('lang') : ($request->user()?->locale ?? 'km');

        // The numbers come from admin settings and plans, so the pages never promise something else.
        $vars = [
            ':operator' => config('app.operator'),
            ':email' => config('app.contact_email'),
            ':trial_days' => Setting::read('trial_days', 2),
            ':live_days' => Plan::where('is_active', true)->min('live_days_after_wedding') ?? 30,
        ];
        $fill = function ($text) use (&$fill, $vars) {
            return is_array($text) ? array_map($fill, $text) : strtr((string) $text, $vars);
        };

        return view('public.legal', [
            'page' => $page,
            'lang' => $lang,
            'text' => $fill(trans("legal.{$page}", [], $lang)),
            'common' => $fill(trans('legal.common', [], $lang)),
        ]);
    }
}
