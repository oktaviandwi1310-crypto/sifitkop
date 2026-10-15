<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WawancaraResource\Pages;
use App\Filament\Resources\WawancaraResource\RelationManagers;
use App\Models\Peserta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class WawancaraResource extends Resource
{
    protected static ?string $model = Peserta::class;

    protected static ?string $navigationIcon = 'heroicon-o-microphone';
    protected static ?string $navigationLabel = 'Wawancara UKK';
    protected static ?string $modelLabel = 'Wawancara';
    protected static ?string $navigationGroup = 'Uji Kelayakan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Peserta & Penugasan')
                    ->description('Hanya dapat diubah oleh Admin.')
                    ->schema([
                        Forms\Components\TextInput::make('nama_lengkap')
                            ->disabled()
                            ->label('Nama Peserta'),
                        Forms\Components\TextInput::make('jabatan_usulan')
                            ->disabled()
                            ->label('Jabatan'),
                        Forms\Components\DateTimePicker::make('jadwal_wawancara')
                            ->label('Jadwal Wawancara')
                            ->disabled(fn () => Auth::user()->role !== 'admin'),
                        Forms\Components\Select::make('penguji_id')
                            ->label('Asesor / Penguji')
                            ->options(
                                \App\Models\User::whereIn('role', ['admin', 'penguji'])->pluck('name', 'id')
                            )
                            ->searchable()
                            ->disabled(fn () => Auth::user()->role !== 'admin'),
                        Forms\Components\FileUpload::make('dokumen_paparan')
                            ->label('Materi Paparan (PDF/PPT)')
                            ->disabled(fn () => Auth::user()->role !== 'koperasi') // Biasanya Koperasi yg upload, tapi admin bisa download
                            ->downloadable()
                            ->acceptedFileTypes(['application/pdf', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'])
                            ->directory('paparan-peserta'),
                    ])->columns(2),

                Forms\Components\Section::make('Penilaian Wawancara (Asesor)')
                    ->description('Skala penilaian 1 (Kurang Sekali) hingga 4 (Sangat Baik). Batas kelulusan wawancara adalah >= 18.')
                    ->schema([
                        Forms\Components\Select::make('wawancara_1')->label('1. Pengetahuan Kondisi Koperasi')->options([1=>'1 (Kurang Sekali)',2=>'2 (Kurang)',3=>'3 (Cukup)',4=>'4 (Baik)'])->required()->native(false),
                        Forms\Components\Select::make('wawancara_2')->label('2. Pemahaman Manajemen Keuangan Koperasi')->options([1=>'1 (Kurang Sekali)',2=>'2 (Kurang)',3=>'3 (Cukup)',4=>'4 (Baik)'])->required()->native(false),
                        Forms\Components\Select::make('wawancara_3')->label('3. Kecakapan dan Keahlian Bidang Perkoperasian')->options([1=>'1 (Kurang Sekali)',2=>'2 (Kurang)',3=>'3 (Cukup)',4=>'4 (Baik)'])->required()->native(false),
                        Forms\Components\Select::make('wawancara_4')->label('4. Kecakapan dan Keahlian Membangun Relasi')->options([1=>'1 (Kurang Sekali)',2=>'2 (Kurang)',3=>'3 (Cukup)',4=>'4 (Baik)'])->required()->native(false),
                        Forms\Components\Select::make('wawancara_5')->label('5. Kemampuan Menyusun Strategi Bisnis')->options([1=>'1 (Kurang Sekali)',2=>'2 (Kurang)',3=>'3 (Cukup)',4=>'4 (Baik)'])->required()->native(false),
                        Forms\Components\Select::make('wawancara_6')->label('6. Kemampuan Membangun Keunggulan & Kesinambungan')->options([1=>'1 (Kurang Sekali)',2=>'2 (Kurang)',3=>'3 (Cukup)',4=>'4 (Baik)'])->required()->native(false),
                    ])->columns(2)->disabled(fn () => Auth::user()->role !== 'penguji' && Auth::user()->role !== 'admin'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Peserta'),
                Tables\Columns\TextColumn::make('koperasi.nama_koperasi')
                    ->searchable()
                    ->sortable()
                    ->label('Koperasi'),
                Tables\Columns\TextColumn::make('jadwal_wawancara')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->label('Jadwal'),
                Tables\Columns\TextColumn::make('penguji.name')
                    ->label('Penguji')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('skor_wawancara')
                    ->label('Total Skor')
                    ->badge()
                    ->state(function ($record) {
                        return $record->wawancara_1 + $record->wawancara_2 + $record->wawancara_3 + $record->wawancara_4 + $record->wawancara_5 + $record->wawancara_6;
                    })
                    ->color(fn ($state) => $state >= 18 ? 'success' : ($state == 0 ? 'gray' : 'danger'))
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'Belum Dinilai' : $state),
                Tables\Columns\TextColumn::make('status_kelulusan')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'LULUS' => 'success',
                        'TIDAK LULUS' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->label('Keputusan Akhir'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('lihat_sa')
                    ->label('Lihat SA')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('info')
                    ->modalHeading('Hasil Self Assessment (Penilaian Mandiri)')
                    ->modalContent(fn (Peserta $record) => view('filament.components.view-sa', ['peserta' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
                    
                Tables\Actions\EditAction::make()->label('Nilai / Edit'),
                Tables\Actions\Action::make('cetak_berita_acara')
                    ->label('Berita Acara')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->action(function (\App\Models\Peserta $record) {
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.berita-acara', ['peserta' => $record]);
                        return response()->streamDownload(
                            fn () => print($pdf->output()), 
                            "Berita_Acara_UKK_{$record->nik_peserta}.pdf"
                        );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    // Filter Query agar hanya yang lolos verifikasi yang masuk ke tahap Wawancara.
    // Jika login sebagai penguji, hanya tampilkan yang di-assign ke dia.
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('status_verifikasi', 'Terverifikasi');
        
        if (Auth::user()->role === 'penguji') {
            $query->where('penguji_id', Auth::user()->id);
        }
        
        return $query;
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
            'index' => Pages\ListWawancaras::route('/'),
            'edit' => Pages\EditWawancara::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
