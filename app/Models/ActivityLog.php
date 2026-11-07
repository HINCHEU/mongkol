<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/** Who did what in admin: approvals, suspensions, trial extensions, settings and record edits. */
class ActivityLog extends Model
{
    protected $table = 'activity_log';

    public const UPDATED_AT = null;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['changes' => 'array'];
    }

    public static function record(string $action, ?Model $subject = null, array $changes = [], ?User $actor = null): self
    {
        return self::create([
            'actor_id' => $actor?->id ?? auth()->id(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'changes' => $changes ?: null,
        ]);
    }

    /** After an admin edit: logs `{model}.updated` with what changed, or nothing if nothing did. */
    public static function changed(Model $record): void
    {
        $changes = Arr::except($record->getChanges(), ['updated_at', ...$record->getHidden()]);
        // A new password is logged as set, never as its hash.
        if (array_key_exists('password', $record->getChanges())) {
            $changes['password'] = '(changed)';
        }

        if ($changes) {
            self::record(Str::snake(class_basename($record)).'.updated', $record, $changes);
        }
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
