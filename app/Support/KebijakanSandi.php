<?php

namespace App\Support;

use Closure;
use Illuminate\Validation\Rules\Password;
use Random\Randomizer;

class KebijakanSandi
{
    /** Aturan dasar: panjang, huruf besar+kecil, angka (dari config). */
    public static function aturanDasar(): Password
    {
        $cfg  = config('keamanan.sandi');
        $rule = Password::min($cfg['min_panjang']);

        if ($cfg['huruf_besar_kecil']) {
            $rule->mixedCase();
        }
        if ($cfg['angka']) {
            $rule->numbers();
        }

        return $rule;
    }

    /** Aturan: sandi tidak boleh sama dengan username (tidak case-sensitive). */
    public static function tidakBolehSamaDenganUsername(?string $username): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($username): void {
            if (filled($username) && mb_strtolower((string) $value) === mb_strtolower($username)) {
                $fail('Kata sandi tidak boleh sama dengan username.');
            }
        };
    }

    /** Sandi sementara untuk reset admin; dijamin memenuhi aturan di atas. */
    public static function buatSementara(int $panjang = 12): string
    {
        $besar = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $kecil = 'abcdefghijkmnopqrstuvwxyz';
        $angka = '23456789';
        $semua = $besar . $kecil . $angka;

        $karakter = [
            $besar[random_int(0, strlen($besar) - 1)],
            $kecil[random_int(0, strlen($kecil) - 1)],
            $angka[random_int(0, strlen($angka) - 1)],
        ];

        while (count($karakter) < $panjang) {
            $karakter[] = $semua[random_int(0, strlen($semua) - 1)];
        }

        return implode('', (new Randomizer())->shuffleArray($karakter));
    }
}