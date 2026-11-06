<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            // When the "preview ends soon" email went out, so the hourly scheduler sends it once.
            $table->timestamp('trial_reminded_at')->nullable()->after('trial_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('trial_reminded_at');
        });
    }
};
