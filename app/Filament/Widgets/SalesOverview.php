<?php

namespace App\Filament\Widgets;

use App\Models\Invitation;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

// The week at a glance on /admin: what came in, what waits on you, what's live, what it earned.
class SalesOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $revenue = Payment::where('status', Payment::APPROVED)
            ->where('reviewed_at', '>=', now()->timezone('Asia/Phnom_Penh')->startOfMonth()->utc())
            ->selectRaw('currency, sum(amount_minor) as total')->groupBy('currency')->pluck('total', 'currency');
        $waiting = Payment::where('status', Payment::PENDING)->whereNotNull('proof_media_id')->count();

        return [
            Stat::make('New trials (7 days)', Invitation::where('created_at', '>=', now()->subDays(7))->count())
                ->description('Couples in preview')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Payments to review', $waiting)
                ->description($waiting ? 'Action required: verify KHQR' : 'All transactions cleared')
                ->descriptionIcon($waiting ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-badge')
                ->color($waiting ? 'warning' : 'success')
                ->url(route('filament.admin.resources.payments.index')),
            Stat::make('Live invitations', Invitation::live()->count())
                ->description('Active wedding ceremonies')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('success'),
            Stat::make('Revenue this month', Payment::format((int) ($revenue['USD'] ?? 0), 'USD'))
                ->description('+ '.Payment::format((int) ($revenue['KHR'] ?? 0), 'KHR'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
        ];
    }
}
