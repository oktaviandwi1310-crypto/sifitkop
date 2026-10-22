<?php

namespace App\Filament\Resources\KoperasiPesertaResource\Pages;

use App\Filament\Resources\KoperasiPesertaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKoperasiPeserta extends EditRecord
{
    protected static string $resource = KoperasiPesertaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
