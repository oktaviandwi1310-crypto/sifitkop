<?php

namespace App\Filament\Resources\KoperasiSelfAssessmentResource\Pages;

use App\Filament\Resources\KoperasiSelfAssessmentResource;
use Filament\Resources\Pages\ViewRecord;

class ViewKoperasiSelfAssessment extends ViewRecord
{
    protected static string $resource = KoperasiSelfAssessmentResource::class;

    public function getTitle(): string
    {
        return $this->record->nama_koperasi;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}