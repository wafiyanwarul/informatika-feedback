<?php

namespace App\Filament\Resources\DosenResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Dosen', \App\Models\Dosen::count())
                ->description('Jumlah dosen aktif')
                ->icon('heroicon-o-academic-cap')
                ->color('success'),
        ];
    }
}
