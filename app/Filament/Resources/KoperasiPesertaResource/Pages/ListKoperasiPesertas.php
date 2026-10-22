<?php
// app/Filament/Resources/KoperasiPesertaResource/Pages/ListKoperasiPesertas.php

namespace App\Filament\Resources\KoperasiPesertaResource\Pages;

use App\Filament\Resources\KoperasiPesertaResource;
use Filament\Resources\Pages\ListRecords;

class ListKoperasiPesertas extends ListRecords
{
    protected static string $resource = KoperasiPesertaResource::class;

    // Tombol "New" tidak dipakai di level koperasi
    protected function getHeaderActions(): array
    {
        return [];
    }

    // Gaya sticky header yang sama dengan halaman list lain
    public function getFooter(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.custom.sticky-header-css');
    }
}