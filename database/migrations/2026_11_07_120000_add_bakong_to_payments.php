<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Phase 2 automatic payment: the dynamic KHQR we showed, its md5 (what Bakong is asked about) and when it expires.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->text('khqr')->nullable()->after('reference');
            $table->char('md5', 32)->nullable()->unique()->after('khqr');
            $table->timestamp('expires_at')->nullable()->after('md5');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['khqr', 'md5', 'expires_at']);
        });
    }
};
