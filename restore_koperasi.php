<?php
$koperasi = App\Models\Koperasi::withTrashed()->find(1);
if ($koperasi && $koperasi->trashed()) {
    $koperasi->restore();
    echo "Koperasi ID 1 restored!\n";
} else {
    echo "Koperasi ID 1 not found or not trashed.\n";
}
