<?php
// app/Filament/Resources/KoperasiPesertaResource/Pages/ViewKoperasiPeserta.php

namespace App\Filament\Resources\KoperasiPesertaResource\Pages;

use App\Filament\Resources\KoperasiPesertaResource;
use Filament\Resources\Pages\ViewRecord;

class ViewKoperasiPeserta extends ViewRecord
{
    protected static string $resource = KoperasiPesertaResource::class;

    public function getTitle(): string
    {
        return $this->record->nama_koperasi;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}