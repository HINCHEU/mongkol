<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One reply per personal link (updated if the guest answers again); generic-link replies carry only a name.
        Schema::create('rsvps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->nullable()->unique()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('attending');
            $table->unsignedTinyInteger('party_size')->default(1);
            $table->string('message', 500)->nullable();
            $table->timestamps();
            $table->index(['invitation_id', 'attending']);
        });

        // The guest book. Hidden wishes stay for the couple but never reach the invitation page.
        Schema::create('wishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('message', 500);
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
            $table->index(['invitation_id', 'is_hidden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishes');
        Schema::dropIfExists('rsvps');
    }
};
