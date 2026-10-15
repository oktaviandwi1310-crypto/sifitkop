<?php

namespace App\Filament\Widgets;

use App\Models\Koperasi;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class KoperasiBarChart extends ChartWidget
{
    protected static ?string $heading = 'Pertumbuhan Koperasi Terdaftar (Tahun Ini)';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        $currentYear = date('Y');

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->translatedFormat('M');
            $data[] = Koperasi::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $i)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Koperasi Terdaftar',
                    'data' => $data,
                    'backgroundColor' => '#f59e0b', // amber-500
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
