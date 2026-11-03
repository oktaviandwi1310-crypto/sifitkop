<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KoperasiWawancaraResource\Pages;
use App\Filament\Resources\KoperasiWawancaraResource\RelationManagers;
use App\Models\Koperasi;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class KoperasiWawancaraResource extends Resource
{
    protected static ?string $model = Koperasi::class;

    protected static ?string $slug = 'wawancara-ukk';

    protected static ?string $navigationIcon = 'heroicon-o-microphone';
    protected static ?string $navigationLabel = 'Wawancara UKK';
    protected static ?string $modelLabel = 'Koperasi';
    protected static ?string $pluralModelLabel = 'Wawancara UKK';
    protected static ?string $navigationGroup = 'Uji Kelayakan';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'nama_koperasi';

    /**
     * Aturan peserta mana yang boleh tampil diambil dari WawancaraResource
     * (hanya Terverifikasi + filter akun penguji), jadi cukup diubah di satu tempat.
     */
    public static function scopePeserta(Builder $query): Builder
    {
        $key = $query->getModel()->getQualifiedKeyName();

        return $query->whereIn($key, WawancaraResource::getEloquentQuery()->select($key));
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('pesertas', fn (Builder $q) => static::scopePeserta($q))
            ->withCount(['pesertas as pesertas_wawancara_count' => fn (Builder $q) => static::scopePeserta($q)]);
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
                        ->state(fn (Koperasi $record) => $record->pesertas_wawancara_count)
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
                Tables\Columns\TextColumn::make('pesertas_wawancara_count')
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
              RelationManagers\WawancarasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' =>  Pages\ListKoperasiWawancaras::route('/'),
            'view'  =>  Pages\ViewKoperasiWawancara::route('/{record}'),
        ];
    }
}