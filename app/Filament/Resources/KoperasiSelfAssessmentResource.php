<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KoperasiSelfAssessmentResource\RelationManagers;
use App\Models\Koperasi;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\KoperasiSelfAssessmentResource\Pages;


class KoperasiSelfAssessmentResource extends Resource
{
    protected static ?string $model = Koperasi::class;

    protected static ?string $slug = 'self-assessment-ukk';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Self Assessment';
    protected static ?string $modelLabel = 'Koperasi';
    protected static ?string $pluralModelLabel = 'Self Assessment';
    protected static ?string $navigationGroup = 'Verifikasi UKK';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'nama_koperasi';

    // Hanya koperasi yang pesertanya sudah punya data Self Assessment
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->has('selfAssessments')
            ->withCount('selfAssessments');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Informasi Koperasi')
                ->schema([
                    Infolists\Components\TextEntry::make('nama_koperasi')->label('Nama Koperasi'),
                    Infolists\Components\TextEntry::make('nik')->label('NIK'),
                    Infolists\Components\TextEntry::make('kabupaten_kota')->label('Kabupaten / Kota'),
                    Infolists\Components\TextEntry::make('total_sa')
                        ->label('Total Self Assessment')
                        ->state(fn (Koperasi $record) => $record->self_assessments_count)
                        ->badge()
                        ->color('info'),
                ])
                ->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_koperasi')
                    ->label('Koperasi Asal')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('kabupaten_kota')
                    ->label('Kabupaten / Kota')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('self_assessments_count')
                    ->label('Total Self Assessment')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat Self Assessment'),
            ])
            ->recordUrl(fn (Koperasi $record) => static::getUrl('view', ['record' => $record]))
            ->defaultSort('nama_koperasi');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SelfAssessmentsRelationManager::class,
        ];
    }

   public static function getPages(): array
    {
        return [
            'index' => Pages\ListKoperasiSelfAssessments::route('/'),
            'view'  => Pages\ViewKoperasiSelfAssessment::route('/{record}'),
        ];
    }
}