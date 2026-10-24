<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    // Sembunyikan blok brand bawaan Filament, logo dipasang manual di heading
    public function hasLogo(): bool
    {
        return false;
    }

    // Logo besar di tengah, lalu judul (mirip contoh HTML)
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
                $this->getRememberFormComponent(),
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

    protected function throwFailureValidationException(): never
    {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'data.username' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}