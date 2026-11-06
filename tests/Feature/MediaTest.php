<?php

use App\Actions\CreateInvitation;
use App\Enums\MediaKind;
use App\Models\Invitation;
use App\Models\Media;
use App\Models\Plan;
use App\Models\Theme;
use App\Models\User;
use App\Services\Themes\InvitationViewModel;
use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(ReferenceDataSeeder::class);
    Storage::fake('local');
    Storage::fake('public');
});

function withMedia(): Invitation
{
    $invitation = app(CreateInvitation::class)->handle(User::factory()->create(), [
        'theme_id' => Theme::where('slug', 'base')->value('id'),
        'wedding_date' => now()->addMonths(2)->toDateString(),
        'groom' => ['name_km' => 'ចាន់ សុខា', 'name_en' => 'Chan Sokha'],
        'bride' => ['name_km' => 'លី ដារ៉ា', 'name_en' => 'Ly Dara'],
    ]);
    test()->actingAs($invitation->user);

    return $invitation;
}

function uploadTo(Invitation $invitation, array $data)
{
    return test()->post("/app/invitations/{$invitation->id}/media", $data);
}

it('turns an uploaded photo into WebP sizes on the public disk and drops the original', function () {
    $invitation = withMedia();

    uploadTo($invitation, ['kind' => 'gallery', 'file' => UploadedFile::fake()->image('a.jpg', 2400, 1800)])
        ->assertSessionHasNoErrors();

    $media = $invitation->media()->sole();
    expect(array_keys($media->variants))->toBe([1600, 960, 480])
        ->and($media->path)->toBe($media->variants[1600])
        ->and(Storage::disk('local')->allFiles())->toBe([]);

    foreach ($media->variants as $width => $path) {
        // A fresh GD encode carries no EXIF block, so camera and GPS data are gone.
        expect(getimagesizefromstring(Storage::disk('public')->get($path)))->toMatchArray([0 => $width, 'mime' => 'image/webp']);
    }

    // Small photos aren't enlarged.
    uploadTo($invitation, ['kind' => 'gallery', 'file' => UploadedFile::fake()->image('small.png', 300, 200)]);
    $small = Media::latest('id')->first();
    expect(getimagesizefromstring(Storage::disk('public')->get($small->variants[1600]))[0])->toBe(300)
        ->and($small->sort)->toBeGreaterThan($media->sort);

    expect(InvitationViewModel::fromInvitation($invitation->fresh())->data['gallery'])
        ->toBe([Storage::disk('public')->url($media->variants[960]), Storage::disk('public')->url($small->variants[960])]);
});

it('replaces the cover and deletes the old files', function () {
    $invitation = withMedia();

    uploadTo($invitation, ['kind' => 'cover', 'file' => UploadedFile::fake()->image('one.jpg', 800, 1000)]);
    $old = $invitation->media()->sole();
    uploadTo($invitation, ['kind' => 'cover', 'file' => UploadedFile::fake()->image('two.jpg', 800, 1000)]);

    expect($invitation->media()->where('kind', 'cover')->count())->toBe(1)
        ->and(Media::find($old->id))->toBeNull()
        ->and(Storage::disk('public')->allFiles())->toHaveCount(3);
});

it('stores music as MP3 within the size cap', function () {
    $invitation = withMedia();

    uploadTo($invitation, ['kind' => 'music', 'file' => UploadedFile::fake()->create('song.mp3', 500, 'audio/mpeg')])
        ->assertSessionHasNoErrors();
    expect(Storage::disk('public')->exists($invitation->media()->sole()->path))->toBeTrue();

    uploadTo($invitation, ['kind' => 'music', 'file' => UploadedFile::fake()->create('song.wav', 500, 'audio/wav')])
        ->assertSessionHasErrors('file');
    uploadTo($invitation, ['kind' => 'music', 'file' => UploadedFile::fake()->create('big.mp3', 9000, 'audio/mpeg')])
        ->assertSessionHasErrors('file');
    uploadTo($invitation, ['kind' => 'gallery', 'file' => UploadedFile::fake()->create('fake.jpg', 10, 'text/plain')])
        ->assertSessionHasErrors('file');
});

it('attaches a KHQR to the couple’s own account and removes it with the account', function () {
    $invitation = withMedia();
    $account = $invitation->giftAccounts()->create(['owner' => 'groom']);
    $foreign = withMedia()->giftAccounts()->create(['owner' => 'bride']);
    $this->actingAs($invitation->user);

    uploadTo($invitation, ['kind' => 'khqr', 'gift_account_id' => $foreign->id, 'file' => UploadedFile::fake()->image('qr.png', 600, 600)])
        ->assertSessionHasErrors('gift_account_id');
    uploadTo($invitation, ['kind' => 'khqr', 'file' => UploadedFile::fake()->image('qr.png', 600, 600)])
        ->assertSessionHasErrors('gift_account_id');

    uploadTo($invitation, ['kind' => 'khqr', 'gift_account_id' => $account->id, 'file' => UploadedFile::fake()->image('qr.png', 600, 600)])
        ->assertSessionHasNoErrors();
    $qr = $account->fresh()->khqr;
    expect($qr->kind)->toBe(MediaKind::Khqr)
        ->and($qr->path)->toEndWith('.png')
        ->and(InvitationViewModel::fromInvitation($invitation->fresh())->data['gift_accounts'][0]['khqr'])->toBe(Storage::disk('public')->url($qr->path));

    $this->put("/app/invitations/{$invitation->id}/gift", ['accounts' => []])->assertSessionHasNoErrors();
    expect(Media::find($qr->id))->toBeNull()
        ->and(Storage::disk('public')->allFiles())->toBe([]);
});

it('caps the gallery at the plan’s photo count', function () {
    $invitation = withMedia();
    $plan = Plan::orderBy('price_usd_cents')->first();
    $invitation->forceFill(['plan_id' => $plan->id, 'paid_at' => now()])->save();
    foreach (range(1, $plan->features['gallery_photos']) as $i) {
        $invitation->media()->create(['kind' => 'gallery', 'path' => "x{$i}.webp", 'variants' => [960 => "x{$i}.webp"]]);
    }

    uploadTo($invitation, ['kind' => 'gallery', 'file' => UploadedFile::fake()->image('a.jpg')])->assertSessionHasErrors('file');
});

it('reorders the gallery and saves the YouTube link', function () {
    $invitation = withMedia();
    [$a, $b] = collect([1, 2])->map(fn ($i) => $invitation->media()->create(['kind' => 'gallery', 'path' => "{$i}.webp", 'variants' => [960 => "{$i}.webp"], 'sort' => $i]));
    $cover = $invitation->media()->create(['kind' => 'cover', 'path' => 'c.webp']);

    $this->put("/app/invitations/{$invitation->id}/photos", ['video_url' => 'https://youtu.be/abcdefghijk', 'gallery' => [$b->id, $a->id]])
        ->assertSessionHasNoErrors();

    expect($invitation->media()->where('kind', 'gallery')->pluck('id')->all())->toBe([$b->id, $a->id])
        ->and($invitation->fresh()->content['video_url'])->toBe('https://youtu.be/abcdefghijk');

    $this->put("/app/invitations/{$invitation->id}/photos", ['video_url' => 'https://evil.example/watch'])->assertSessionHasErrors('video_url');
    $this->put("/app/invitations/{$invitation->id}/photos", ['video_url' => null, 'gallery' => [$cover->id]])->assertSessionHasErrors('gallery.0');
});

it('lets only the owner delete media, and only their own', function () {
    $invitation = withMedia();
    $media = $invitation->media()->create(['kind' => 'cover', 'path' => 'c.webp']);
    $other = withMedia();

    $this->actingAs($other->user)->delete("/app/invitations/{$invitation->id}/media/{$media->id}")->assertForbidden();
    $this->delete("/app/invitations/{$other->id}/media/{$media->id}")->assertNotFound();
    $this->actingAs($invitation->user)->post("/app/invitations/{$invitation->id}/media", [])->assertSessionHasErrors(['kind', 'file']);

    $this->delete("/app/invitations/{$invitation->id}/media/{$media->id}")->assertRedirect();
    expect(Media::find($media->id))->toBeNull();
});
