<?php

namespace App\Filament\Koperasi\Widgets;

use Filament\Widgets\Widget;

class KoperasiStatusWidget extends Widget
{
    protected static string $view = 'filament.koperasi.widgets.koperasi-status-widget';
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $koperasi = auth()->user()->koperasi;
        $isAktif = $koperasi && $koperasi->status_ods === 'Aktif';
        $isLengkap = $koperasi && 
                     $koperasi->dokumen_pad && 
                     $koperasi->dokumen_rat && 
                     $koperasi->dokumen_lap_keuangan && 
                     $koperasi->dokumen_goaml && 
                     $koperasi->dokumen_npwp;

        return [
            'isAktif' => $isAktif,
            'isLengkap' => $isLengkap,
        ];
    }
}
