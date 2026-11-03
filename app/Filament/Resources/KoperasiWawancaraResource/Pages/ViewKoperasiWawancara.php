<?php

namespace App\Filament\Resources\KoperasiWawancaraResource\Pages;

use App\Filament\Resources\KoperasiWawancaraResource;
use Filament\Resources\Pages\ViewRecord;

class ViewKoperasiWawancara extends ViewRecord
{
    protected static string $resource = KoperasiWawancaraResource::class;

    public function getTitle(): string
    {
        return $this->record->nama_koperasi;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}