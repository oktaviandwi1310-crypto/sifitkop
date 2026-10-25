<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Support\CaptchaGambar;
use Filament\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
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
        return $form
            ->schema([
                $this->getUsernameFormComponent(),
                $this->getPasswordFormComponent(),
                ...CaptchaGambar::komponen('login'),
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

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        // captcha sekali pakai
        if (! CaptchaGambar::periksa('login', $data['captcha'] ?? null)) {
            throw ValidationException::withMessages([
                'data.captcha' => 'Kode verifikasi yang Anda masukkan salah.',
            ]);
        }

        $user = User::where('username', $data['username'])->first();

        // Akun terkunci: tolak sebelum sandi diperiksa, dengan pesan yang
        // sama persis seperti login gagal biasa (tidak membocorkan username).
        if ($user?->sedangTerkunci()) {
            $this->throwFailureValidationException();
        }

        try {
            $response = parent::authenticate();
        } catch (ValidationException $e) {
            $user?->catatLoginGagal();

            throw $e;
        }

        // Login berhasil: bersihkan hitungan gagal.
        if ($response !== null) {
            $user?->resetPercobaanLogin();
        }

        return $response;
    }

    protected function throwFailureValidationException(): never
    {
        $maks  = config('keamanan.login.maks_percobaan');
        $menit = config('keamanan.login.kunci_menit');

        throw ValidationException::withMessages([
            'data.username' => "Username atau kata sandi tidak sesuai, atau akun sedang dikunci sementara. "
                . "Akun dikunci {$menit} menit setelah {$maks} kali gagal berturut-turut.",
        ]);
    }
}