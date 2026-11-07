<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Phase 2 theme options: a plan feature like premium_themes. Plans already in the database get it
// on Premium only; the seeder sets it for new installs. Edit it in admin Plans afterwards.
return new class extends Migration
{
    public function up(): void
    {
        foreach (DB::table('plans')->get() as $plan) {
            $features = json_decode($plan->features ?? '{}', true) ?: [];
            $features['theme_options'] ??= $plan->slug === 'premium';
            DB::table('plans')->where('id', $plan->id)->update(['features' => json_encode($features)]);
        }
    }

    public function down(): void
    {
        foreach (DB::table('plans')->get() as $plan) {
            $features = json_decode($plan->features ?? '{}', true) ?: [];
            unset($features['theme_options']);
            DB::table('plans')->where('id', $plan->id)->update(['features' => json_encode($features)]);
        }
    }
};
