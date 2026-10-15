<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SelfAssessmentResource\Pages;
use App\Models\SelfAssessment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SelfAssessmentResource extends Resource
{
    protected static ?string $model = SelfAssessment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Self Assessment';
    protected static ?string $navigationGroup = 'Verifikasi UKK';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Peserta')
                    ->schema([
                        Forms\Components\Select::make('peserta_id')
                            ->relationship('peserta', 'nama_lengkap')
                            ->required()
                            ->searchable()
                            ->label('Nama Peserta (Calon Pengurus/Pengawas)'),
                    ]),
                    
                Forms\Components\Section::make('Penilaian Mandiri (Skor per Variabel)')
                    ->description('Masukkan skor berdasarkan kuesioner Self Assessment. Skor akan diakumulasi secara otomatis berdasarkan standar Juklak Halaman 50.')
                    ->schema([
                        Forms\Components\TextInput::make('integritas_score')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->label('Skor Integritas (Terpenuhi >= 9)')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::kalkulasiKelulusan($set, $get)),
                            
                        Forms\Components\TextInput::make('reputasi_score')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->label('Skor Reputasi Keuangan (Terpenuhi >= 2)')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::kalkulasiKelulusan($set, $get)),
                            
                        Forms\Components\TextInput::make('kompetensi_score')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->label('Skor Kompetensi (Terpenuhi >= 27)')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::kalkulasiKelulusan($set, $get)),
                            
                        Forms\Components\TextInput::make('inovasi_score')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->label('Skor Kreativitas & Inovasi (Terpenuhi >= 9)')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::kalkulasiKelulusan($set, $get)),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Hasil Kelulusan Self Assessment')
                    ->schema([
                        Forms\Components\TextInput::make('status_kelulusan')
                            ->readOnly()
                            ->label('Status Kelayakan (Otomatis)')
                            ->helperText('Peserta harus memenuhi batas minimum di SEMUA aspek secara bersaman.'),
                    ]),
            ]);
    }

    public static function kalkulasiKelulusan(callable $set, callable $get)
    {
        $integritas = (int) $get('integritas_score');
        $reputasi = (int) $get('reputasi_score');
        $kompetensi = (int) $get('kompetensi_score');
        $inovasi = (int) $get('inovasi_score');
        
        if ($integritas >= 9 && $reputasi >= 2 && $kompetensi >= 27 && $inovasi >= 9) {
            $set('status_kelulusan', 'Terpenuhi');
        } else {
            $set('status_kelulusan', 'Tidak Terpenuhi');
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('peserta.nama_lengkap')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Peserta'),
                Tables\Columns\TextColumn::make('integritas_score')->label('Integritas')->sortable(),
                Tables\Columns\TextColumn::make('reputasi_score')->label('Reputasi Keuangan')->sortable(),
                Tables\Columns\TextColumn::make('kompetensi_score')->label('Kompetensi')->sortable(),
                Tables\Columns\TextColumn::make('inovasi_score')->label('Kreativitas & Inovasi')->sortable(),
                Tables\Columns\TextColumn::make('status_kelulusan')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Terpenuhi' => 'success',
                        'Tidak Terpenuhi' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListSelfAssessments::route('/'),
            'create' => Pages\CreateSelfAssessment::route('/create'),
            'edit' => Pages\EditSelfAssessment::route('/{record}/edit'),
        ];
    }
}
