<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\OdsApiService;
use App\Models\Koperasi;

$odsService = new OdsApiService();
echo "Menarik profile ODS...\n";
$result = $odsService->getProfile();

if (!$result['success']) {
    echo "Gagal: " . $result['message'] . "\n";
    exit(1);
}

$data = $result['data'];
$total = count($data);
echo "Berhasil mendapat $total koperasi.\n";

$koperasiAktifData = [];
$count = 0;
$startTime = time();

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
        // Abaikan error per item
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
    
    $count++;
    if ($count % 50 === 0) {
        $elapsed = time() - $startTime;
        echo "Diproses: $count / $total (Elapsed: {$elapsed}s)\n";
        
        $chunks = array_chunk($koperasiAktifData, 50);
        foreach ($chunks as $chunk) {
            Koperasi::upsert($chunk, ['nik'], [
                'nama_koperasi', 'badan_hukum', 'tanggal_badan_hukum', 
                'kabupaten_kota', 'alamat', 'klasifikasi_koperasi', 'status_ods', 'updated_at',
                'susunan_pengurus', 'susunan_pengawas'
            ]);
        }
        $koperasiAktifData = [];
    }
}

if (!empty($koperasiAktifData)) {
    $chunks = array_chunk($koperasiAktifData, 50);
    foreach ($chunks as $chunk) {
        Koperasi::upsert($chunk, ['nik'], [
            'nama_koperasi', 'badan_hukum', 'tanggal_badan_hukum', 
            'kabupaten_kota', 'alamat', 'klasifikasi_koperasi', 'status_ods', 'updated_at',
            'susunan_pengurus', 'susunan_pengawas'
        ]);
    }
}

echo "Proses Sinkronisasi 20.000+ data beserta pengurus Selesai!\n";
