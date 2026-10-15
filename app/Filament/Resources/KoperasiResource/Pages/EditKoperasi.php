<?php

namespace App\Filament\Resources\KoperasiResource\Pages;

use App\Filament\Resources\KoperasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKoperasi extends EditRecord
{
    protected static string $resource = KoperasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (!empty($data['nik'])) {
            $odsService = new \App\Services\OdsApiService();
            $result = $odsService->getDetail($data['nik']);
            
            if ($result['success'] && isset($result['data'])) {
                $odsData = $result['data'];
                $data['nama_koperasi'] = $odsData['koperasi'] ?? $odsData['nama'] ?? ($data['nama_koperasi'] ?? '');
                $data['badan_hukum'] = $odsData['badan_hukum'] ?? $odsData['bh'] ?? ($data['badan_hukum'] ?? '');
                $data['tanggal_badan_hukum'] = $odsData['tanggal_badan_hukum'] ?? ($data['tanggal_badan_hukum'] ?? '');
                $data['kabupaten_kota'] = $odsData['kabupaten'] ?? $odsData['kota'] ?? ($data['kabupaten_kota'] ?? '');
                $data['alamat'] = $odsData['alamat'] ?? ($data['alamat'] ?? '');
                $data['klasifikasi_koperasi'] = $odsData['bentuk_koperasi'] ?? $odsData['bentuk'] ?? ($data['klasifikasi_koperasi'] ?? '');

                if (isset($odsData['pengurus']) && is_array($odsData['pengurus'])) {
                    $pengurus = [];
                    $pengawas = [];
                    foreach ($odsData['pengurus'] as $p) {
                        $jabatan = strtolower($p['jabatan'] ?? '');
                        if (str_contains($jabatan, 'pengawas')) {
                            $pengawas[] = ['nama' => $p['nama'] ?? '', 'jabatan' => $p['jabatan'] ?? 'Pengawas'];
                        } else {
                            $pengurus[] = ['nama' => $p['nama'] ?? '', 'jabatan' => $p['jabatan'] ?? ''];
                        }
                    }
                    $data['susunan_pengurus'] = $pengurus;
                    $data['susunan_pengawas'] = $pengawas;
                }
            }
        }

        return $data;
    }
}
