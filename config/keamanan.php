<?php

return [
    'sandi' => [
        'min_panjang'       => 8,
        'huruf_besar_kecil' => true,
        'angka'             => true,
        'masa_berlaku_hari' => 90,
        'peringatan_hari'   => 14,
    ],
    'login' => [
        'maks_percobaan' => 5,
        'kunci_menit'    => 15,
    ],
];