<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ods = new App\Services\OdsApiService();
$res = $ods->getProfile();
if ($res['success']) {
    $count = 0;
    foreach ($res['data'] as $item) {
        if (stripos($item['Kabupaten'] ?? '', 'KAB. CILACAP') !== false) {
            if (stripos($item['Bentuk_Koperasi'] ?? '', 'Sekunder Kabupaten/Kota') !== false) {
                echo "NIK: " . $item['NIK'] . " - Nama: " . $item['Nama_Koperasi'] . " - Status: " . ($item['StatusKoperasi'] ?? 'KOSONG') . "\n";
                $count++;
            }
        }
    }
    echo "Total Data Ditemukan di Profile: " . $count . "\n";
}
