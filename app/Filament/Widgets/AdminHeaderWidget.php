<?php

namespace App\Filament\Widgets;

use App\Models\Invitation;
use App\Models\Payment;
use Filament\Widgets\Widget;

class AdminHeaderWidget extends Widget
{
    protected static ?int $sort = -3;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.admin-header-widget';

    public function getViewData(): array
    {
        $pendingPayments = Payment::where('status', Payment::PENDING)->whereNotNull('proof_media_id')->count();
        $liveInvitations = Invitation::live()->count();

        return [
            'user' => auth()->user(),
            'pendingPayments' => $pendingPayments,
            'liveInvitations' => $liveInvitations,
            'phnomPenhTime' => now()->timezone('Asia/Phnom_Penh')->format('l, d M Y · h:i A'),
        ];
    }
}
