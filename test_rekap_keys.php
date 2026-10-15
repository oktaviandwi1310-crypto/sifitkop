<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ods = new App\Services\OdsApiService();
$res = $ods->getRekap('2025');
$data = $res['data']['data'] ?? $res['data'];
if (!empty($data)) {
    echo json_encode($data[0], JSON_PRETTY_PRINT);
}
