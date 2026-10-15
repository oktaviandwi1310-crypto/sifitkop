<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ods = new App\Services\OdsApiService();
$start = microtime(true);
$response = $ods->getRekap('2024');
echo "Time: " . (microtime(true) - $start) . "s\n";
if ($response['success']) {
    echo "Success! count: " . count($response['data']['data'] ?? $response['data']) . "\n";
} else {
    echo "Failed!\n";
    print_r($response);
}
