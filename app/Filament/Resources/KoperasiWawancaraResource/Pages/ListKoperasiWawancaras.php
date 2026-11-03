<?php

namespace App\Filament\Resources\KoperasiWawancaraResource\Pages;

use App\Filament\Resources\KoperasiWawancaraResource;
use Filament\Resources\Pages\ListRecords;

class ListKoperasiWawancaras extends ListRecords
{
    protected static string $resource = KoperasiWawancaraResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getFooter(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.custom.sticky-header-css');
    }
}