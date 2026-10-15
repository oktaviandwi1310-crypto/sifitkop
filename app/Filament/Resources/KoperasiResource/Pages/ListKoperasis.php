<?php

namespace App\Filament\Resources\KoperasiResource\Pages;

use App\Filament\Resources\KoperasiResource;
use App\Services\OdsApiService;
use App\Models\Koperasi;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListKoperasis extends ListRecords
{
    protected static string $resource = KoperasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('tarikLiveOds')
                ->label('Tarik Data Live ODS')
                ->icon('heroicon-o-cloud-arrow-down')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (OdsApiService $ods) {
                    $result = $ods->getProfile();
                    if (!$result['success'] || empty($result['data'])) {
                        Notification::make()->title('Gagal menarik data dari ODS')->danger()->send();
                        return;
                    }
                    
                    $data = $result['data'];
                    $koperasiAktifData = [];
                    
                    // Supaya tidak timeout di sisi PHP jika berjalan sangat lama
                    set_time_limit(0);

                    foreach ($data as $item) {
                        $nik = $item['NIK'] ?? $item['nik'] ?? null;
                        if (!$nik) continue;

                        $susunanPengurus = null;
                        $susunanPengawas = null;
                        
                        // Menarik data Detail per NIK
                        try {
                            $detailResult = $odsService->getDetail($nik);
                            if ($detailResult['success'] && isset($detailResult['data'][0])) {
                                $odsData = $detailResult['data'][0];
                                $pengurusList = $odsData['Pengurus'] ?? [];
                                
                                $tempPengurus = [];
                                $tempPengawas = [];
                                
                                foreach ($pengurusList as $p) {
                                    $nama = $p['Nama'] ?? '';
                                    $jabatanRaw = strtolower($p['IdJabatan'] ?? '');
                                    $statusKepengurusan = strtolower($p['StatusKepengurusan'] ?? '');
                                    
                                    if ($statusKepengurusan !== 'aktif') continue;
                                    
                                    if (isset($p['IdStatus']) && str_contains($p['IdStatus'], '1 - Pengurus')) {
                                        $jabatan = 'Anggota';
                                        if (str_contains($jabatanRaw, 'ketua')) $jabatan = 'Ketua';
                                        elseif (str_contains($jabatanRaw, 'sekretaris')) $jabatan = 'Sekretaris';
                                        elseif (str_contains($jabatanRaw, 'bendahara')) $jabatan = 'Bendahara';
                                        
                                        $tempPengurus[] = ['nama' => $nama, 'jabatan' => $jabatan];
                                    } elseif (isset($p['IdStatus']) && str_contains($p['IdStatus'], '2 - Pengawas')) {
                                        $jabatan = 'Anggota Pengawas';
                                        if (str_contains($jabatanRaw, 'ketua')) $jabatan = 'Ketua Pengawas';
                                        
                                        $tempPengawas[] = ['nama' => $nama, 'jabatan' => $jabatan];
                                    }
                                }
                                
                                $susunanPengurus = empty($tempPengurus) ? null : json_encode($tempPengurus);
                                $susunanPengawas = empty($tempPengawas) ? null : json_encode($tempPengawas);
                            }
                        } catch (\Exception $e) {
                            // Abaikan error per item, lanjut ke NIK berikutnya
                        }
                        
                        $koperasiAktifData[] = [
                            'nik' => $nik,
                            'nama_koperasi' => $item['Nama_Koperasi'] ?? $item['nama'] ?? '',
                            'badan_hukum' => $item['Nomor_Badan_Hukum_Pendirian'] ?? $item['badan_hukum'] ?? '',
                            'tanggal_badan_hukum' => $item['Tanggal_Badan_Hukum_Pendirian'] ?? $item['tanggal_badan_hukum'] ?? '',
                            'kabupaten_kota' => $item['Kabupaten'] ?? $item['kabupaten'] ?? '',
                            'alamat' => $item['Alamat'] ?? $item['alamat'] ?? '',
                            'klasifikasi_koperasi' => $item['Bentuk_Koperasi'] ?? $item['bentuk_koperasi'] ?? '',
                            'status_ods' => 'Aktif',
                            'status_pengajuan' => 'Draft',
                            'susunan_pengurus' => $susunanPengurus ? json_decode($susunanPengurus, true) : null,
                            'susunan_pengawas' => $susunanPengawas ? json_decode($susunanPengawas, true) : null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    
                    // Upsert dalam kelompok 1000 untuk mencegah query terpotong di database
                    $chunks = array_chunk($koperasiAktifData, 1000);
                    foreach ($chunks as $chunk) {
                        Koperasi::upsert($chunk, ['nik'], [
                            'nama_koperasi', 'badan_hukum', 'tanggal_badan_hukum', 
                            'kabupaten_kota', 'alamat', 'klasifikasi_koperasi', 'status_ods', 'updated_at',
                            'susunan_pengurus', 'susunan_pengawas'
                        ]);
                    }
                    
                    Notification::make()->title('Berhasil sinkronisasi '.count($koperasiAktifData).' koperasi aktif beserta data pengurusnya')->success()->send();
                }),
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make(),
            'Primer Kabupaten/Kota' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('klasifikasi_koperasi', 'Primer Kabupaten/Kota')),
            'Primer Nasional' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('klasifikasi_koperasi', 'Primer Nasional')),
            'Primer Provinsi' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('klasifikasi_koperasi', 'Primer Provinsi')),
            'Sekunder Kabupaten/Kota' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('klasifikasi_koperasi', 'Sekunder Kabupaten/Kota')),
        ];
    }

    public function getFooter(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.custom.sticky-header-css');
    }
}
