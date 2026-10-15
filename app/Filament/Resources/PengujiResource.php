<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengujiResource\Pages;
use App\Filament\Resources\PengujiResource\RelationManagers;
use App\Models\Penguji;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PengujiResource extends Resource
{
    protected static ?string $model = Penguji::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Daftar Penguji';
    protected static ?string $modelLabel = 'Penguji';
    protected static ?string $pluralModelLabel = 'Daftar Penguji';
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Penguji')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Penguji')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengujis::route('/'),
            'create' => Pages\CreatePenguji::route('/create'),
            'edit' => Pages\EditPenguji::route('/{record}/edit'),
        ];
    }
}
