<?php

namespace App\Filament\Resources\WawancaraResource\Pages;

use App\Filament\Resources\WawancaraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWawancara extends EditRecord
{
    protected static string $resource = WawancaraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 1. Hitung Wawancara
        $wawancaraTotal = 0;
        for ($i = 1; $i <= 6; $i++) {
            $wawancaraTotal += (int) ($data['wawancara_' . $i] ?? 0);
        }
        $data['skor_wawancara'] = $wawancaraTotal;

        // 2. Ambil Skor Admin
        $skorAdmin = $this->record->skor_admin ?? 0;
        
        // 3. Ambil Skor Self Assessment (SA) dari relasi
        $sa = \App\Models\SelfAssessment::where('peserta_id', $this->record->id)->first();
        $skorSa = $sa ? ($sa->integritas_score + $sa->reputasi_score + $sa->kompetensi_score + $sa->inovasi_score) : 0;
        
        // 4. Hitung Total Akhir
        $totalAkhir = $skorAdmin + $skorSa + $wawancaraTotal;
        $data['skor_total'] = $totalAkhir;

        // 5. Syarat Kelulusan Mutlak Juklak:
        // - Admin + SA (Tahap 1) minimal 64
        // - Wawancara minimal 18
        // - Total Akhir minimal 82
        $lulusTahap1 = ($skorAdmin + $skorSa) >= 64;
        $lulusWawancara = $wawancaraTotal >= 18;
        $lulusTotal = $totalAkhir >= 82;

        if ($lulusTahap1 && $lulusWawancara && $lulusTotal) {
            $data['status_kelulusan'] = 'LULUS';
        } else {
            $data['status_kelulusan'] = 'TIDAK LULUS';
        }

        return $data;
    }
}
