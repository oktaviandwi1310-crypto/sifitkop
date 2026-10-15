<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ods = new App\Services\OdsApiService();
$start = microtime(true);
$tahun = '2024';

$response = $ods->getRekap($tahun);
echo "Rekap time: " . (microtime(true) - $start) . "s\n";
$profileResponse = $ods->getProfile();
echo "Profile time: " . (microtime(true) - $start) . "s\n";

if ($response['success']) {
    $data = $response['data'];
    $data = isset($data['data']) ? $data['data'] : $data;
    
    $actualCounts = [];
    if (isset($profileResponse['success']) && $profileResponse['success']) {
        $pData = $profileResponse['data'];
        $pData = isset($pData['data']) ? $pData['data'] : $pData;
        foreach ($pData as $prof) {
            $kabRaw = $prof['Kabupaten'] ?? '';
            $kabRaw = preg_replace('/^\d{2}\.\d{2}\s*-\s*/', '', $kabRaw);
            $kab = strtoupper(trim($kabRaw));
            
            $bentuk = strtoupper(trim($prof['Bentuk_Koperasi'] ?? ''));
            if ($bentuk) $actualCounts[$kab]['BENTUK KOPERASI'][$bentuk] = ($actualCounts[$kab]['BENTUK KOPERASI'][$bentuk] ?? 0) + 1;
            
            $jenis = strtoupper(trim($prof['Jenis_Koperasi'] ?? ''));
            if ($jenis) $actualCounts[$kab]['JENIS KOPERASI'][$jenis] = ($actualCounts[$kab]['JENIS KOPERASI'][$jenis] ?? 0) + 1;
            
            $kel = strtoupper(trim($prof['Kelompok_Koperasi'] ?? ''));
            if ($kel) $actualCounts[$kab]['KELOMPOK KOPERASI'][$kel] = ($actualCounts[$kab]['KELOMPOK KOPERASI'][$kel] ?? 0) + 1;
            
            $sek = strtoupper(trim($prof['Sektor_Usaha'] ?? ''));
            if ($sek) $actualCounts[$kab]['SEKTOR USAHA'][$sek] = ($actualCounts[$kab]['SEKTOR USAHA'][$sek] ?? 0) + 1;
        }
    }

    echo "Calculation time: " . (microtime(true) - $start) . "s\n";
}
