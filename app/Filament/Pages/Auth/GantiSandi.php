<?php

namespace App\Filament\Pages\Auth;

use App\Support\KebijakanSandi;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GantiSandi extends EditProfile
{
    public function getTitle(): string
    {
        return 'Ganti Kata Sandi';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('current_password')
                    ->label('Kata Sandi Saat Ini')
                    ->password()
                    ->revealable()
                    ->required()
                    ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                        $pengguna = Filament::auth()->user();

                        if (! $pengguna || ! Hash::check((string) $value, $pengguna->getAuthPassword())) {
                            $fail('Kata sandi saat ini tidak sesuai.');
                        }
                    })
                    ->dehydrated(false),

                $this->getPasswordFormComponent()
                    ->label('Kata Sandi Baru')
                    ->helperText('Minimal 8 karakter, kombinasi huruf besar, huruf kecil, dan angka. Tidak boleh sama dengan username maupun sandi lama.')
                    ->required()
                    ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                        $pengguna = Filament::auth()->user();

                        if ($pengguna && Hash::check((string) $value, $pengguna->getAuthPassword())) {
                            $fail('Kata sandi baru tidak boleh sama dengan kata sandi lama.');
                        }
                    })
                    ->rule(fn () => KebijakanSandi::tidakBolehSamaDenganUsername(Filament::auth()->user()?->username)),

                $this->getPasswordConfirmationFormComponent(),
            ])
            ->operation('edit')
            ->model($this->getUser())
            ->statePath('data');
    }

    protected function getRedirectUrl(): ?string
    {
        return filament()->getUrl();
    }
}