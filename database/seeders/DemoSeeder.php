<?php

namespace Database\Seeders;

use App\Enums\GuestSide;
use App\Models\EventPreset;
use App\Models\Invitation;
use App\Models\Plan;
use App\Models\Theme;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

// A complete demo couple for local work and the theme gallery. Never runs in production.
// Plain data, no Faker: the production image ships without dev packages.
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (Invitation::where('slug', 'sokha-dara')->exists()) {
            return;
        }

        $user = User::firstOrCreate(['email' => 'demo@mongkol.test'], ['name' => 'Demo Couple']);

        // shortcut: stands in for the base theme until `themes:sync` exists (M3), which will own this row.
        $theme = Theme::firstOrCreate(['slug' => 'base'], [
            'name' => 'Morodok Snaeha',
            'version' => '1.0.0',
            'manifest' => ['slug' => 'base', 'sections' => ['cover', 'invitation', 'date', 'program', 'venues', 'gallery', 'gift']],
            'is_active' => true,
        ]);

        $invitation = new Invitation([
            'slug' => 'sokha-dara',
            'theme_id' => $theme->id,
            'wedding_date' => '2026-12-12',
            'content' => [
                'opening' => ['km' => 'សូមគោរពអញ្ជើញ', 'en' => 'You are warmly invited'],
                'dress_code' => ['km' => 'សម្លៀកបំពាក់ប្រពៃណី', 'en' => 'Traditional attire'],
            ],
        ]);
        // Live with no end date, so the demo never pauses.
        $invitation->forceFill([
            'user_id' => $user->id,
            'plan_id' => Plan::where('slug', 'premium')->value('id'),
            'trial_ends_at' => now(),
            'paid_at' => now(),
            'live_until' => null,
        ])->save();

        $invitation->people()->createMany([
            ['role' => 'groom', 'name_km' => 'ចាន់ សុខា', 'name_en' => 'Chan Sokha'],
            ['role' => 'bride', 'name_km' => 'លី ដារ៉ា', 'name_en' => 'Ly Dara'],
            ['role' => 'groom_father', 'title' => 'លោក', 'name_km' => 'ចាន់ វណ្ណា', 'name_en' => 'Chan Vanna'],
            ['role' => 'groom_mother', 'title' => 'លោកស្រី', 'name_km' => 'សៀង សុភា', 'name_en' => 'Seang Sophea'],
            ['role' => 'bride_father', 'title' => 'លោក', 'name_km' => 'លី ហុង', 'name_en' => 'Ly Hong', 'is_late' => true],
            ['role' => 'bride_mother', 'title' => 'លោកស្រី', 'name_km' => 'ម៉ៅ ចន្ធូ', 'name_en' => 'Mao Chanthou'],
        ]);

        $home = $invitation->venues()->create([
            'name' => 'គេហដ្ឋានខាងស្រី',
            'address' => 'ភូមិ ១, សង្កាត់ ទឹកល្អក់ ១, ខណ្ឌ ទួលគោក, ភ្នំពេញ',
            'maps_url' => 'https://maps.google.com/?q=11.5700,104.8900',
        ]);
        $hall = $invitation->venues()->create([
            'name' => 'Mongkol Garden Hall',
            'address' => 'Phnom Penh',
            'maps_url' => 'https://maps.google.com/?q=11.5560,104.9282',
        ]);

        $times = [
            'gift_procession' => '07:00', 'blessing' => '09:00', 'hair_cutting' => '10:30',
            'sampeah_phtem' => '13:30', 'knot_tying' => '15:00', 'reception' => '17:00',
        ];

        foreach (EventPreset::whereIn('key', array_keys($times))->orderBy('default_sort')->get() as $sort => $preset) {
            $invitation->events()->create([
                'preset_key' => $preset->key,
                'title_km' => $preset->title_km,
                'title_en' => $preset->title_en,
                // Entered in Phnom Penh time, stored in UTC.
                'starts_at' => CarbonImmutable::parse("2026-12-12 {$times[$preset->key]}", 'Asia/Phnom_Penh')->utc(),
                'venue_id' => $preset->key === 'reception' ? $hall->id : $home->id,
                'sort' => $sort,
            ]);
        }

        $invitation->giftAccounts()->create([
            'owner' => 'couple',
            'bank' => 'ABA Bank',
            'account_name' => 'CHAN SOKHA',
            'account_number' => '000 000 000',
        ]);

        $guests = [
            ['code' => 'k7Px2Q', 'title' => 'លោក', 'name' => 'ចាន់ ថា', 'suffix' => 'និងភរិយា', 'side' => GuestSide::Groom],
            ['code' => 'Rm4tYh', 'title' => 'លោកស្រី', 'name' => 'សុខ ស្រីមុំ', 'side' => GuestSide::Bride],
            ['code' => 'Wn8cEa', 'title' => 'កញ្ញា', 'name' => 'ហេង លីដា', 'suffix' => 'និងក្រុមគ្រួសារ', 'side' => GuestSide::Bride],
        ];

        foreach ($guests as $guest) {
            $invitation->guests()->make($guest)->forceFill(['code' => $guest['code']])->save();
        }
    }
}
