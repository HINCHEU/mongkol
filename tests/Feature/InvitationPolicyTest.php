<?php

use App\Models\Invitation;
use App\Models\User;

it('lets the owner view, update and delete their invitation', function () {
    $invitation = Invitation::factory()->create();

    foreach (['view', 'update', 'delete'] as $ability) {
        expect($invitation->user->can($ability, $invitation))->toBeTrue($ability);
    }
});

it('refuses every other user', function () {
    $invitation = Invitation::factory()->create();
    $stranger = User::factory()->create();

    foreach (['view', 'update', 'delete'] as $ability) {
        expect($stranger->can($ability, $invitation))->toBeFalse($ability);
    }
});

it('lets an allowlisted admin preview any invitation, but not an admin role alone', function () {
    config(['app.admin_emails' => ['admin@example.com']]);
    $invitation = Invitation::factory()->create();

    expect(User::factory()->admin()->create(['email' => 'admin@example.com'])->can('view', $invitation))->toBeTrue()
        ->and(User::factory()->admin()->create()->can('view', $invitation))->toBeFalse();
});

it('lets any signed-in user create an invitation', function () {
    expect(User::factory()->create()->can('create', Invitation::class))->toBeTrue();
});
