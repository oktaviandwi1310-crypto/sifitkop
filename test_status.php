<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ods = new App\Services\OdsApiService();
$res = $ods->getProfile();
if ($res['success']) {
    $data = $res['data'];
    $statuses = [];
    foreach ($data as $item) {
        $status = $item['StatusKoperasi'] ?? 'KOSONG';
        if (!isset($statuses[$status])) {
            $statuses[$status] = 0;
        }
        $statuses[$status]++;
    }
    print_r($statuses);
}
