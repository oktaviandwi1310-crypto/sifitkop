<?php

namespace App\Filament\Widgets;

use App\Models\Peserta;
use Filament\Widgets\ChartWidget;

class StatusPesertaChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Status Kelulusan Peserta';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $lulus = Peserta::where('status_kelulusan', 'LULUS')->count();
        $tidakLulus = Peserta::where('status_kelulusan', 'TIDAK LULUS')->count();
        $belumDiuji = Peserta::whereNull('status_kelulusan')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Peserta UKK',
                    'data' => [$lulus, $tidakLulus, $belumDiuji],
                    'backgroundColor' => [
                        '#10b981', // emerald-500
                        '#ef4444', // red-500
                        '#f59e0b', // amber-500
                    ],
                ],
            ],
            'labels' => ['Lulus', 'Tidak Lulus', 'Belum Diuji / Diproses'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
