<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Koperasi Terdaftar', \App\Models\Koperasi::count())
                ->description('Jumlah entitas koperasi pada sistem')
                ->descriptionIcon('heroicon-m-building-office')
                ->chart([1, 3, 4, 2, 5, 4, 7])
                ->color('primary'),
                
            Stat::make('Total Pengajuan Peserta UKK', \App\Models\Peserta::count())
                ->description('Total calon pengurus/pengawas')
                ->descriptionIcon('heroicon-m-users')
                ->chart([2, 5, 3, 8, 4, 9, 12])
                ->color('info'),
                
            Stat::make('Peserta LULUS UKK', \App\Models\Peserta::where('status_kelulusan', 'LULUS')->count())
                ->description('Memenuhi ambang batas nilai >= 82')
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart([0, 1, 0, 3, 2, 5, 8])
                ->color('success'),
        ];
    }
}
