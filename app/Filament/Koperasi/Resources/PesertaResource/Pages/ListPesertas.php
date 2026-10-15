<?php

namespace App\Filament\Koperasi\Resources\PesertaResource\Pages;

use App\Filament\Koperasi\Resources\PesertaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPesertas extends ListRecords
{
    protected static string $resource = PesertaResource::class;

    protected function getHeaderActions(): array
    {
        $koperasi = auth()->user()->koperasi;
        $isLengkap = $koperasi && 
                     $koperasi->dokumen_pad && 
                     $koperasi->dokumen_rat && 
                     $koperasi->dokumen_lap_keuangan && 
                     $koperasi->dokumen_goaml && 
                     $koperasi->dokumen_npwp;
                     
        $isAktif = $koperasi && $koperasi->status_ods === 'Aktif';
        $canCreate = $isLengkap && $isAktif;
        
        $tooltip = '';
        if (!$isAktif) {
            $tooltip = 'Status koperasi Anda tidak aktif. Tidak bisa mendaftarkan peserta.';
        } elseif (!$isLengkap) {
            $tooltip = 'Lengkapi seluruh Dokumen Persyaratan di menu Profil Koperasi terlebih dahulu.';
        }

        return [
            Actions\CreateAction::make()
                ->disabled(!$canCreate)
                ->tooltip($tooltip),
        ];
    }
}
