<?php

namespace App\Filament\Resources\SelfAssessmentResource\Pages;

use App\Filament\Resources\SelfAssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSelfAssessment extends CreateRecord
{
    protected static string $resource = SelfAssessmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $integritas = (int) ($data['skor_penguji_integritas'] ?? $data['skor_mandiri_integritas'] ?? 0);
        $reputasi = (int) ($data['skor_penguji_reputasi'] ?? $data['skor_mandiri_reputasi'] ?? 0);
        $kompetensi = (int) ($data['skor_penguji_kompetensi'] ?? $data['skor_mandiri_kompetensi'] ?? 0);
        $inovasi = (int) ($data['skor_penguji_inovasi'] ?? $data['skor_mandiri_inovasi'] ?? 0);

        $isTerpenuhi = ($integritas >= 9 && $reputasi >= 2 && $kompetensi >= 27 && $inovasi >= 9);
        $status = $isTerpenuhi ? 'Terpenuhi' : 'Tidak Terpenuhi';
        $data['status_penguji'] = $status;

        $data['integritas_score'] = $integritas;
        $data['reputasi_score'] = $reputasi;
        $data['kompetensi_score'] = $kompetensi;
        $data['inovasi_score'] = $inovasi;
        $data['status_kelulusan'] = $status;

        return $data;
    }
}
