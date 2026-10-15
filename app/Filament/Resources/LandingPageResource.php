<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LandingPageResource\Pages;
use App\Filament\Resources\LandingPageResource\RelationManagers;
use App\Models\LandingPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LandingPageResource extends Resource
{
    protected static ?string $model = LandingPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-window';
    protected static ?string $navigationLabel = 'Landing Page';
    protected static ?string $pluralModelLabel = 'Pengaturan Landing Page';
    protected static ?string $navigationGroup = 'Pengaturan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Hero & About')
                            ->schema([
                                Forms\Components\TextInput::make('hero_title')
                                    ->label('Judul Hero'),
                                Forms\Components\Textarea::make('hero_subtitle')
                                    ->label('Subjudul Hero'),
                                Forms\Components\FileUpload::make('hero_image')
                                    ->label('Gambar Hero (Kosongkan jika default)'),
                                Forms\Components\TextInput::make('hero_stats_title')
                                    ->label('Judul Stats (Opsional)'),
                                
                                Forms\Components\TextInput::make('about_title')
                                    ->label('Judul Tentang Kami'),
                                Forms\Components\Textarea::make('about_description')
                                    ->label('Deskripsi Tentang Kami'),
                                Forms\Components\FileUpload::make('about_image_1')
                                    ->label('Gambar Tentang 1'),
                                Forms\Components\FileUpload::make('about_image_2')
                                    ->label('Gambar Tentang 2'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Layanan & Fitur')
                            ->schema([
                                Forms\Components\Repeater::make('services')
                                    ->label('Modul Penilaian / Layanan')
                                    ->schema([
                                        Forms\Components\TextInput::make('tag')
                                            ->label('Label Tag (misal: Penilaian)'),
                                        Forms\Components\TextInput::make('title')
                                            ->label('Judul Modul'),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Deskripsi Singkat'),
                                        Forms\Components\TextInput::make('icon')
                                            ->label('Ikon (contoh: bi-clipboard-check-fill)'),
                                    ]),
                                Forms\Components\Repeater::make('features')
                                    ->label('Keunggulan SIFITKOP')
                                    ->schema([
                                        Forms\Components\TextInput::make('icon')
                                            ->label('Ikon (contoh: bi-eye-fill)'),
                                        Forms\Components\TextInput::make('title')
                                            ->label('Judul Fitur'),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Deskripsi Singkat'),
                                    ]),
                            ]),
                        // Forms\Components\Tabs\Tab::make('Tim & Testimoni')
                        //     ->schema([
                        //         Forms\Components\Repeater::make('team')
                        //             ->label('Tim Asesor')
                        //             ->schema([
                        //                 Forms\Components\TextInput::make('name')
                        //                     ->label('Nama Asesor'),
                        //                 Forms\Components\TextInput::make('role')
                        //                     ->label('Jabatan/Posisi'),
                        //                 Forms\Components\FileUpload::make('image')
                        //                     ->label('Foto'),
                        //             ]),
                        //         Forms\Components\Repeater::make('testimonials')
                        //             ->label('Testimoni Koperasi')
                        //             ->schema([
                        //                 Forms\Components\TextInput::make('name')
                        //                     ->label('Nama Pemberi Testimoni'),
                        //                 Forms\Components\TextInput::make('role')
                        //                     ->label('Jabatan/Nama Koperasi'),
                        //                 Forms\Components\Textarea::make('quote')
                        //                     ->label('Kutipan Testimoni'),
                        //             ]),
                        //     ]),
                        Forms\Components\Tabs\Tab::make('Kontak & Footer')
                            ->schema([
                                Forms\Components\TextInput::make('contact_address')
                                    ->label('Alamat Kontak'),
                                Forms\Components\TextInput::make('contact_email')
                                    ->label('Email Kontak'),
                                Forms\Components\TextInput::make('contact_phone')
                                    ->label('Telepon Kontak'),
                                Forms\Components\Textarea::make('footer_description')
                                    ->label('Deskripsi Singkat Footer'),
                            ]),
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hero_title')->label('Judul Utama'),
                Tables\Columns\TextColumn::make('updated_at')->label('Terakhir Diubah')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLandingPages::route('/'),
            'create' => Pages\CreateLandingPage::route('/create'),
            'edit' => Pages\EditLandingPage::route('/{record}/edit'),
        ];
    }
}
