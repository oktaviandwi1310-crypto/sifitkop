<?php

namespace App\Filament\Resources\WawancaraResource\Pages;

use App\Filament\Resources\WawancaraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWawancaras extends ListRecords
{
    protected static string $resource = WawancaraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getFooter(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.custom.sticky-header-css');
    }
}
