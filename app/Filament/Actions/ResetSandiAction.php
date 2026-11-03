<?php

namespace App\Filament\Actions;

use App\Models\User;
use App\Support\KebijakanSandi;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;

class ResetSandiAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'resetSandi';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Reset Sandi')
            ->icon('heroicon-o-key')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Reset kata sandi pengguna?')
            ->modalDescription('Sistem membuat sandi sementara. Pengguna wajib menggantinya saat login berikutnya.')
            ->visible(fn (): bool => Auth::user()?->role === 'admin')
            ->action(function (User $record): void {
                // visible() hanya menyembunyikan tombol; cek lagi di server.
                abort_unless(Auth::user()?->role === 'admin', 403);

                $sementara = KebijakanSandi::buatSementara();

                $record->forceFill([
                    'password'              => $sementara,
                    'password_changed_at'   => null, 
                    'failed_login_attempts' => 0,
                    'locked_until'          => null,
                ])->save();

                Notification::make()
                    ->title('Sandi berhasil direset')
                    ->body(new HtmlString(
                        'Username: <strong>' . e($record->username) . '</strong><br>'
                        . 'Sandi sementara: <strong style="font-family:monospace">' . e($sementara) . '</strong><br>'
                        . 'Salin sekarang, sandi ini tidak akan ditampilkan lagi.'
                    ))
                    ->success()
                    ->persistent()
                    ->send();
            });
    }
}