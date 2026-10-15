<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ods = new App\Services\OdsApiService();
$res = $ods->getProfile();
if ($res['success']) {
    echo json_encode(array_keys($res['data'][0] ?? []), JSON_PRETTY_PRINT);
}
