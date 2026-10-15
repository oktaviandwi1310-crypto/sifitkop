<?php

namespace App\Filament\Resources\PengujiResource\Pages;

use App\Filament\Resources\PengujiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengujis extends ListRecords
{
    protected static string $resource = PengujiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
