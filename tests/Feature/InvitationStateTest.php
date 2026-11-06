<?php

use App\Enums\InvitationState;
use App\Models\Invitation;

// state() and the query scopes must always agree, or admin filters would disagree with the guest page.
function assertState(Invitation $invitation, InvitationState $expected): void
{
    $invitation->refresh();
    expect($invitation->state())->toBe($expected);

    foreach (InvitationState::cases() as $state) {
        $inScope = Invitation::query()->{$state->value}()->whereKey($invitation->id)->exists();
        expect($inScope)->toBe($state === $expected, "scope {$state->value}()");
    }
}

it('derives each state from the timestamps', function (string $factoryState, InvitationState $expected) {
    $factory = Invitation::factory();
    $invitation = ($factoryState === 'trial' ? $factory : $factory->{$factoryState}())->create();

    assertState($invitation, $expected);
})->with([
    'trial' => ['trial', InvitationState::Trial],
    'paused' => ['paused', InvitationState::Paused],
    'live' => ['live', InvitationState::Live],
    'ended' => ['ended', InvitationState::Ended],
    'suspended' => ['suspended', InvitationState::Suspended],
]);

it('treats a paid invitation with no end date as live', function () {
    $invitation = Invitation::factory()->live()->create(['live_until' => null]);

    assertState($invitation, InvitationState::Live);
});

it('moves from trial to paused when the trial runs out', function () {
    $invitation = Invitation::factory()->create(['trial_ends_at' => now()->addDays(2)]);
    assertState($invitation, InvitationState::Trial);

    $this->travel(2)->days();
    assertState($invitation, InvitationState::Paused);
});

it('goes live when paid, from trial or from paused', function (string $from) {
    $invitation = $from === 'trial' ? Invitation::factory()->create() : Invitation::factory()->paused()->create();

    $invitation->forceFill(['paid_at' => now(), 'live_until' => now()->addMonths(2)])->save();

    assertState($invitation, InvitationState::Live);
})->with(['trial', 'paused']);

it('ends when live_until passes, and comes back when it is extended', function () {
    $invitation = Invitation::factory()->live()->create(['live_until' => now()->addDay()]);

    $this->travel(1)->days();
    assertState($invitation, InvitationState::Ended);

    $invitation->forceFill(['live_until' => now()->addMonth()])->save();
    assertState($invitation, InvitationState::Live);
});

it('suspends from any state and restores the previous one when lifted', function (string $factoryState, InvitationState $before) {
    $invitation = Invitation::factory()->{$factoryState}()->create();

    $invitation->forceFill(['suspended_at' => now()])->save();
    assertState($invitation, InvitationState::Suspended);

    $invitation->forceFill(['suspended_at' => null])->save();
    assertState($invitation, $before);
})->with([
    'paused' => ['paused', InvitationState::Paused],
    'live' => ['live', InvitationState::Live],
    'ended' => ['ended', InvitationState::Ended],
]);

it('keeps the state timestamps out of mass assignment', function () {
    $invitation = Invitation::factory()->create();

    $invitation->update(['paid_at' => now(), 'live_until' => now()->addYear(), 'user_id' => 999]);

    expect($invitation->fresh())->paid_at->toBeNull()->live_until->toBeNull()
        ->and($invitation->fresh()->user_id)->not->toBe(999);
});
