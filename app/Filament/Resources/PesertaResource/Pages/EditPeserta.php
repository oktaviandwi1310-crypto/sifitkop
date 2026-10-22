<?php
// app/Filament/Resources/PesertaResource/Pages/EditPeserta.php

namespace App\Filament\Resources\PesertaResource\Pages;

use App\Filament\Resources\KoperasiPesertaResource;
use App\Filament\Resources\PesertaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPeserta extends EditRecord
{
    protected static string $resource = PesertaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('kembali')
                ->label('Kembali ke Daftar Peserta')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => KoperasiPesertaResource::getUrl('view', ['record' => $this->record->koperasi_id])),
            Actions\DeleteAction::make(),
        ];
    }
}