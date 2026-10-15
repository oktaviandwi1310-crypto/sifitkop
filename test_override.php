<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$time_start = microtime(true);
$ods = new App\Services\OdsApiService();
$profileResponse = $ods->getProfile();

$actualCounts = [];
if (isset($profileResponse['success']) && $profileResponse['success']) {
    echo "Total Profiles: " . count($profileResponse['data']) . "\n";
    foreach ($profileResponse['data'] as $prof) {
        $kabRaw = $prof['Kabupaten'] ?? '';
        $kabRaw = preg_replace('/^\d{2}\.\d{2}\s*-\s*/', '', $kabRaw);
        $kab = strtoupper(trim($kabRaw));
        
        $bentuk = $prof['Bentuk_Koperasi'] ?? '';
        if ($bentuk) $actualCounts[$kab]['Bentuk Koperasi'][$bentuk] = ($actualCounts[$kab]['Bentuk Koperasi'][$bentuk] ?? 0) + 1;
        
        $jenis = $prof['Jenis_Koperasi'] ?? '';
        if ($jenis) $actualCounts[$kab]['Jenis Koperasi'][$jenis] = ($actualCounts[$kab]['Jenis Koperasi'][$jenis] ?? 0) + 1;
        
        $kel = $prof['Kelompok_Koperasi'] ?? '';
        if ($kel) $actualCounts[$kab]['Kelompok Koperasi'][$kel] = ($actualCounts[$kab]['Kelompok Koperasi'][$kel] ?? 0) + 1;
        
        $sek = $prof['Sektor_Usaha'] ?? '';
        if ($sek) $actualCounts[$kab]['Sektor Usaha'][$sek] = ($actualCounts[$kab]['Sektor Usaha'][$sek] ?? 0) + 1;
    }
}

echo "Memory usage: " . round(memory_get_usage() / 1024 / 1024, 2) . " MB\n";
echo "Time: " . round(microtime(true) - $time_start, 2) . " s\n";
if (isset($actualCounts['KAB. CILACAP']['Bentuk Koperasi'])) {
    print_r($actualCounts['KAB. CILACAP']['Bentuk Koperasi']);
}
