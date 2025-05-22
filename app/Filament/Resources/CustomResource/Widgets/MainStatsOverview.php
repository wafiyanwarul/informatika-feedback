<?php

namespace App\Filament\Resources\CustomResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\Role;
use App\Models\User;

class MainStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Dosen', Dosen::count())
                ->description('Jumlah dosen aktif')
                ->icon('heroicon-o-academic-cap')
                ->color('success'),

            Stat::make('Total Mata Kuliah', MataKuliah::count())
                ->description('Jumlah mata kuliah terdaftar')
                ->icon('heroicon-o-book-open')
                ->color('info'),

            Stat::make('Total Role', Role::count())
                ->description('Jumlah role sistem')
                ->icon('heroicon-o-lock-closed')
                ->color('warning'),

            Stat::make('Total Users', User::count())
                ->description('Jumlah pengguna terdaftar')
                ->icon('heroicon-o-users')
                ->color('primary'),
        ];
    }
}
