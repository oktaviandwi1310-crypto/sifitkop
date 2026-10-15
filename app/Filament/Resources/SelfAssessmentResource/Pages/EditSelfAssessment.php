<?php

namespace App\Filament\Resources\SelfAssessmentResource\Pages;

use App\Filament\Resources\SelfAssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSelfAssessment extends EditRecord
{
    protected static string $resource = SelfAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
