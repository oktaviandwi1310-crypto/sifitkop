<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ods = new App\Services\OdsApiService();
$response = $ods->getRekap('2025');

if ($response['success']) {
    $data = isset($response['data']['data']) ? $response['data']['data'] : $response['data'];
    foreach ($data as $item) {
        if (strtoupper($item['Kabupaten']) === 'KAB. CILACAP' && strpos($item['DetailRekap'], 'Primer Nasional') !== false) {
            print_r($item);
        }
    }
}
