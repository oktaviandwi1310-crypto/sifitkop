<?php

namespace App\Filament\Resources\KoperasiSelfAssessmentResource\RelationManagers;

use App\Filament\Resources\SelfAssessmentResource;
use App\Models\SelfAssessment;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class SelfAssessmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'selfAssessments';

    protected static ?string $title = 'Self Assessment Peserta';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        // Memakai tabel SelfAssessmentResource apa adanya (kolom dan tombol sama persis)
        return SelfAssessmentResource::table($table)
            ->heading('Self Assessment Peserta')
            ->defaultSort('self_assessments.id', 'desc')
            ->recordUrl(fn (SelfAssessment $record) => SelfAssessmentResource::getUrl('edit', ['record' => $record]));
    }
}