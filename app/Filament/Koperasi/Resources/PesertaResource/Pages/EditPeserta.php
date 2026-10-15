<?php

namespace App\Filament\Koperasi\Resources\PesertaResource\Pages;

use App\Filament\Koperasi\Resources\PesertaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPeserta extends EditRecord
{
    protected static string $resource = PesertaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
