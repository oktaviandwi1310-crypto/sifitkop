<?php

namespace App\Support;

use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\HtmlString;

class CaptchaGambar
{
    // Tanpa I, O, 0, 1 supaya tidak membingungkan.
    private const KARAKTER = 'ACDEFHJKLMNPRTUVWXY34679';
    private const PANJANG = 5;


    public static function komponen(string $nama): array
    {
        self::pastikanAda($nama);

        return [
            Placeholder::make("captcha_gambar_{$nama}")
                ->label('Kode Verifikasi')
                ->columnSpanFull()
                ->content(function () use ($nama) {
                    self::pastikanAda($nama);

                    return new HtmlString(
                        '<img src="' . e(session("captcha_{$nama}_gambar")) . '" alt="Kode verifikasi" '
                        . 'style="height:3.75rem;width:auto;border-radius:0.375rem;border:1px solid #d1d5db;">'
                    );
                }),

            Actions::make([
                Action::make("segarkan_captcha_{$nama}")
                    ->label('Ganti gambar')
                    ->icon('heroicon-m-arrow-path')
                    ->color('gray')
                    ->link()
                    ->action(function (Set $set) use ($nama) {
                        self::segarkan($nama);
                        $set('captcha', null);
                    }),
            ])->columnSpanFull(),

            TextInput::make('captcha')
                ->label('Ketik kode pada gambar')
                ->helperText('Tidak membedakan huruf besar dan kecil.')
                ->required()
                ->autocomplete(false)
                ->extraInputAttributes(['tabindex' => 4])
                ->columnSpanFull(),
        ];
    }

    public static function pastikanAda(string $nama): void
    {
        if (! session()->has("captcha_{$nama}_kode")) {
            self::segarkan($nama);
        }
    }

    public static function segarkan(string $nama): void
    {
        $kode = '';
        for ($i = 0; $i < self::PANJANG; $i++) {
            $kode .= self::KARAKTER[random_int(0, strlen(self::KARAKTER) - 1)];
        }

        $builder = new CaptchaBuilder($kode);

        $builder->setDistortion(false);        
        $builder->setMaxBehindLines(2);        
        $builder->setMaxFrontLines(0);        
        $builder->setBackgroundColor(255, 255, 255);
        $builder->setTextColor(30, 30, 30);    

        $builder->build(220, 80);              

        session([
            "captcha_{$nama}_kode"   => $kode,
            "captcha_{$nama}_gambar" => $builder->inline(),
        ]);
    }

    // kode sekali pakai
    public static function periksa(string $nama, ?string $input): bool
    {
        $kode = session("captcha_{$nama}_kode");

        session()->forget(["captcha_{$nama}_kode", "captcha_{$nama}_gambar"]);

        return is_string($kode)
            && $kode !== ''
            && hash_equals($kode, strtoupper(trim((string) $input)));
    }
}