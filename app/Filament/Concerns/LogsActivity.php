<?php

namespace App\Filament\Concerns;

use App\Models\ActivityLog;

/** For Edit record pages: every admin edit lands in the activity log with what changed. */
trait LogsActivity
{
    protected function afterSave(): void
    {
        ActivityLog::changed($this->record);
    }
}
