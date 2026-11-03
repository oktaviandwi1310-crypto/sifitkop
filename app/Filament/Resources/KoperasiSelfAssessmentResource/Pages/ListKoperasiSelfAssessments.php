<?php

namespace App\Filament\Resources\KoperasiSelfAssessmentResource\Pages;

use App\Filament\Resources\KoperasiSelfAssessmentResource;
use Filament\Resources\Pages\ListRecords;

class ListKoperasiSelfAssessments extends ListRecords
{
    protected static string $resource = KoperasiSelfAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getFooter(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.custom.sticky-header-css');
    }
}