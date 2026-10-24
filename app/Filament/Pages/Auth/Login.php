<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function hasLogo(): bool
    {
        return false;
    }

    public function getHeading(): string | Htmlable
    {
        return new HtmlString(
            '<img src="' . asset('sifitkop.png') . '" alt="Logo" '
            . 'style="display:block;width:6rem;height:auto;margin:0 auto 0.2rem;">'
            . 'Sign in to Portal'
        );
    }

    public function getSubHeading(): string | Htmlable | null
    {
        return parent::getSubHeading() ?? 'Welcome back.';
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()->label('Continue');
    }

    public function form(Form $form): Form
    {
        // generate string
        if (!session()->has('captcha_string')) {
            $randomString = strtoupper(substr(md5(mt_rand()), 0, 5));
            session(['captcha_string' => $randomString]);
        }

        $captchaText = session('captcha_string');

        return $form
            ->schema([
                $this->getUsernameFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
                
                // captcha acak
                TextInput::make('captcha')
                    ->label(new HtmlString('Kode Verifikasi: <strong style="background:#f3f4f6; padding: 2px 8px; border-radius: 4px; letter-spacing: 3px; font-family: monospace; font-size: 1.1rem; color: #4f46e5;">' . $captchaText . '</strong>'))
                    ->helperText('Masukkan kode di atas dengan benar (tidak case-sensitive)')
                    ->required()
                    ->placeholder('Ketik kodenya di sini')
                    ->extraInputAttributes(['tabindex' => 4]),
            ])
            ->statePath('data');
    }

    protected function getUsernameFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Username')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }

    // --- CEK CAPTCHA SAAT LOGIN DI-SUBMIT ---
    public function authenticate(): ?\Filament\Http\Responses\Auth\Contracts\LoginResponse
    {
        $data = $this->form->getState();
        $sessionCaptcha = session('captcha_string');

        // Validasi input user dengan session 
        if (!isset($data['captcha']) || strtoupper($data['captcha']) !== $sessionCaptcha) {
            
            // Hapus session captcha agar kodenya berubah saat salah input
            session()->forget('captcha_string');

            throw ValidationException::withMessages([
                'data.captcha' => 'Kode verifikasi yang Anda masukkan salah.',
            ]);
        }

        // Jika benar, hapus session captcha agar di-generate ulang untuk sesi berikutnya
        session()->forget('captcha_string');

        return parent::authenticate();
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.username' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}