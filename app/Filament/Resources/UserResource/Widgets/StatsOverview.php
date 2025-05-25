<?php

namespace App\Filament\Resources\UserResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', \App\Models\User::count())
            ->description('Jumlah user yang terdaftar')
            ->icon('heroicon-o-users')
            ->color('primary'),
        ];
    }
}
