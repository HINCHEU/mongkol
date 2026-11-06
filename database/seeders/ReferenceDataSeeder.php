<?php

namespace Database\Seeders;

use App\Models\EventPreset;
use App\Models\Plan;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

// Data the app needs to run, safe on production and on every deploy:
// firstOrCreate only adds what is missing, so prices and wording edited in admin are kept.
class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        // Placeholder prices: set the real ones in admin.
        Plan::firstOrCreate(['slug' => 'basic'], [
            'name' => 'Basic',
            'price_usd_cents' => 1500,
            'price_khr' => 60000,
            'max_guests' => 300,
            'features' => [
                'gallery_photos' => 6,
                'own_music' => false,
                'video' => false,
                'custom_slug' => false,
                'premium_themes' => false,
                'hide_credit' => false,
            ],
            'live_days_after_wedding' => 30,
        ]);

        Plan::firstOrCreate(['slug' => 'premium'], [
            'name' => 'Premium',
            'price_usd_cents' => 2900,
            'price_khr' => 116000,
            'max_guests' => null,
            'features' => [
                'gallery_photos' => 30,
                'own_music' => true,
                'video' => true,
                'custom_slug' => true,
                'premium_themes' => true,
                'hide_credit' => true,
            ],
            'live_days_after_wedding' => 30,
        ]);

        $presets = [
            ['key' => 'gift_procession', 'title_km' => 'ពិធីហែជំនូន', 'title_en' => 'Gift procession'],
            ['key' => 'blessing', 'title_km' => 'ពិធីសូត្រមន្តចម្រើនព្រះបរិត្ត', 'title_en' => 'Monks’ blessing'],
            ['key' => 'hair_cutting', 'title_km' => 'ពិធីកាត់សក់បង្កក់សិរី', 'title_en' => 'Hair-cutting ceremony'],
            ['key' => 'sampeah_phtem', 'title_km' => 'ពិធីសំពះផ្ទឹម', 'title_en' => 'Sampeah Phtem'],
            ['key' => 'knot_tying', 'title_km' => 'ពិធីចងដៃ', 'title_en' => 'Knot-tying ceremony'],
            ['key' => 'reception', 'title_km' => 'ពិធីជប់លៀងអាពាហ៍ពិពាហ៍', 'title_en' => 'Wedding reception'],
        ];

        foreach ($presets as $sort => $preset) {
            EventPreset::firstOrCreate(['key' => $preset['key']], $preset + ['default_sort' => $sort]);
        }

        Setting::firstOrCreate(['key' => 'trial_days'], ['value' => 2]);
        Setting::firstOrCreate(['key' => 'trial_guest_cap'], ['value' => 10]);

        // Themes are files; this keeps their rows in step on every install and deploy.
        Artisan::call('themes:sync');
    }
}
