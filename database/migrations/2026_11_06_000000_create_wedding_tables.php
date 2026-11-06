<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The MVP data model from System Design.md, in dependency order. RSVP and wishes wait for Phase 2.
// Money is in minor units (US cents, whole riel); times are UTC.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->unsignedInteger('price_usd_cents');
            $table->unsignedInteger('price_khr');
            $table->unsignedInteger('max_guests')->nullable()->comment('null = unlimited');
            $table->json('features');
            $table->unsignedSmallInteger('live_days_after_wedding')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('version', 20);
            $table->string('tier', 20)->default('standard');
            $table->boolean('is_active')->default(false);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->string('thumbnail_path')->nullable();
            $table->json('manifest');
            $table->timestamps();
        });

        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->foreignId('theme_id')->constrained();
            $table->json('theme_settings')->nullable();
            $table->json('content')->nullable();
            $table->foreignId('plan_id')->nullable()->constrained();
            $table->date('wedding_date');
            $table->timestamp('trial_ends_at');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('live_until')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 20);
            $table->string('path');
            $table->json('variants')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
            $table->index(['invitation_id', 'kind']);
        });

        Schema::create('invitation_people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('role', 20);
            $table->string('title')->nullable();
            $table->string('name_km');
            $table->string('name_en')->nullable();
            $table->boolean('is_late')->default(false);
            $table->foreignId('photo_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->timestamps();
            $table->unique(['invitation_id', 'role']);
        });

        Schema::create('event_presets', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title_km');
            $table->string('title_en');
            $table->unsignedSmallInteger('default_sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('maps_url', 2048)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('preset_key')->nullable()->comment('null for custom items');
            $table->string('title_km');
            $table->string('title_en')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->foreignId('venue_id')->nullable()->constrained()->nullOnDelete();
            $table->string('note')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('gift_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('owner', 20);
            $table->string('bank')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->foreignId('khqr_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('code', 6);
            $table->string('title')->nullable();
            $table->string('name');
            $table->string('suffix')->nullable();
            $table->string('side', 10)->nullable();
            $table->string('group')->nullable();
            $table->string('phone', 30)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('first_opened_at')->nullable();
            $table->timestamp('last_opened_at')->nullable();
            $table->unsignedInteger('open_count')->default(0);
            $table->timestamps();
            $table->unique(['invitation_id', 'code']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // Restrict, not cascade: payment records outlive a deleted invitation or user.
            $table->foreignId('invitation_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('plan_id')->constrained();
            $table->unsignedInteger('amount_minor');
            $table->char('currency', 3);
            $table->string('method', 20)->default('manual_khqr');
            $table->string('reference')->nullable()->unique();
            $table->foreignId('proof_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('status', 20)->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value');
            $table->timestamps();
        });

        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->nullableMorphs('subject');
            $table->json('changes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        foreach ([
            'activity_log', 'settings', 'payments', 'guests', 'gift_accounts', 'events', 'venues',
            'event_presets', 'invitation_people', 'media', 'invitations', 'themes', 'plans',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
