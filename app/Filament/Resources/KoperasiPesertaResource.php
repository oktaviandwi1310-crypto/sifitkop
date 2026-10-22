<?php
// app/Filament/Resources/KoperasiPesertaResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\KoperasiPesertaResource\Pages;
use App\Filament\Resources\KoperasiPesertaResource\RelationManagers;
use App\Models\Koperasi;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KoperasiPesertaResource extends Resource
{
    protected static ?string $model = Koperasi::class;

    // Slug dibedakan dari KoperasiResource (Master Koperasi) yang memakai model yang sama
    protected static ?string $slug = 'peserta-ukk';

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Peserta UKK';
    protected static ?string $modelLabel = 'Koperasi';
    protected static ?string $pluralModelLabel = 'Peserta UKK';
    protected static ?string $navigationGroup = 'Verifikasi UKK';
    protected static ?string $recordTitleAttribute = 'nama_koperasi';

    // Hanya koperasi yang sudah punya peserta (hapus has() kalau mau semua koperasi tampil)
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->has('pesertas');
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
                    Infolists\Components\TextEntry::make('total_peserta')
                        ->label('Total Peserta')
                        ->state(fn (Koperasi $record) => $record->pesertas()->count())
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
                Tables\Columns\TextColumn::make('pesertas_count')
                    ->counts('pesertas')
                    ->label('Total Peserta')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat Peserta'),
            ])
            ->recordUrl(fn (Koperasi $record) => static::getUrl('view', ['record' => $record]))
            ->defaultSort('nama_koperasi');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PesertasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKoperasiPesertas::route('/'),
            'view'  => Pages\ViewKoperasiPeserta::route('/{record}'),
        ];
    }
}