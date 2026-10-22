<?php

namespace App\Filament\Widgets;

use App\Models\Koperasi;
use App\Models\Peserta;
use Filament\Widgets\Widget;

class PengajuanBaruWidget extends Widget
{
    protected static string $view = 'filament.custom.pengajuan-baru-widget';

    protected static ?int $sort = -10;

    protected int | string | array $columnSpan = 'full';

    // Tampil hanya untuk admin, dan hanya kalau ada pengajuan yang belum diverifikasi
    public static function canView(): bool
    {
        return auth()->user()?->role === 'admin'
            && Peserta::where('status_verifikasi', 'Pending')->exists();
    }

    protected function getViewData(): array
    {
        $pending = fn ($q) => $q->where('status_verifikasi', 'Pending');

        return [
            'total' => Koperasi::whereHas('pesertas', $pending)->count(),
            'items' => Koperasi::query()
                ->whereHas('pesertas', $pending)
                ->withCount(['pesertas as pesertas_pending_count' => $pending])
                ->orderByDesc('pesertas_pending_count')
                ->limit(10)
                ->get(),
        ];
    }
}