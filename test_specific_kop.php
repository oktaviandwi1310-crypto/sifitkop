<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ods = new App\Services\OdsApiService();
$profileResponse = $ods->getProfile();

if (isset($profileResponse['success']) && $profileResponse['success']) {
    foreach ($profileResponse['data'] as $prof) {
        if (strpos(strtoupper($prof['Nama_Koperasi'] ?? ''), 'DAYEUHLUHUR MAJENANG') !== false) {
            echo "MATCH FOUND:\n";
            echo "Nama: " . $prof['Nama_Koperasi'] . "\n";
            echo "Kabupaten: " . $prof['Kabupaten'] . "\n";
            echo "Bentuk: '" . $prof['Bentuk_Koperasi'] . "'\n";
            echo "Jenis: '" . $prof['Jenis_Koperasi'] . "'\n";
            echo "Kelompok: '" . $prof['Kelompok_Koperasi'] . "'\n";
            echo "Sektor: '" . $prof['Sektor_Usaha'] . "'\n";
            echo "Status: '" . $prof['StatusKoperasi'] . "'\n";
            echo "-------------------------\n";
        }
    }
}
