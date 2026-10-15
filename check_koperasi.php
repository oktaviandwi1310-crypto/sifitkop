<?php
$user = App\Models\User::find(3);
$koperasi = $user->koperasi;
echo "User ID: " . $user->id . "\n";
echo "Koperasi ID: " . $user->koperasi_id . "\n";
echo "Koperasi exists: " . ($koperasi ? 'Yes' : 'No') . "\n";
if ($koperasi) {
    echo "Koperasi Data: " . json_encode($koperasi->toArray()) . "\n";
}
