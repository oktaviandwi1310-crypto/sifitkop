<?php

namespace App\Filament\Koperasi\Resources;

use App\Filament\Koperasi\Resources\PesertaResource\Pages;
use App\Models\Peserta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PesertaResource extends Resource
{
    protected static ?string $model = Peserta::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Pengajuan UKK (Peserta)';
    protected static ?string $modelLabel = 'Pengajuan Peserta UKK';
    protected static ?string $navigationGroup = 'Menu Utama';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('koperasi_id', auth()->user()->koperasi_id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar Peserta')
                    ->description('Isi data diri calon pengurus/pengawas yang akan diajukan UKK.')
                    ->schema([
                        Forms\Components\Hidden::make('koperasi_id')
                            ->default(fn () => auth()->user()->koperasi_id),
                        Forms\Components\TextInput::make('nik_peserta')
                            ->required()
                            ->maxLength(16)
                            ->rule(function (?App\Models\Peserta $record) {
                                return function (string $attribute, $value, \Closure $fail) use ($record) {
                                    $koperasi = auth()->user()->koperasi;
                                    if (!$koperasi) return;
                                    
                                    $isPrimer = stripos($koperasi->klasifikasi_koperasi, 'Primer') !== false;
                                    
                                    $existsInSame = \App\Models\Peserta::where('nik_peserta', $value)
                                        ->where('koperasi_id', $koperasi->id)
                                        ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                                        ->exists();
                                        
                                    if ($existsInSame) {
                                        $fail('Peserta dengan NIK ini sudah terdaftar di Koperasi Anda.');
                                        return;
                                    }
                                    
                                    if ($isPrimer) {
                                        $existsInOtherPrimer = \App\Models\Peserta::where('nik_peserta', $value)
                                            ->whereHas('koperasi', function ($query) {
                                                $query->where('klasifikasi_koperasi', 'like', '%Primer%');
                                            })
                                            ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                                            ->exists();
                                            
                                        if ($existsInOtherPrimer) {
                                            $fail('Sesuai aturan, Peserta ini sudah terdaftar di Koperasi Primer lain sehingga tidak dapat didaftarkan ganda.');
                                        }
                                    }
                                };
                            })
                            ->label('NIK Peserta (KTP)'),
                        Forms\Components\TextInput::make('nama_lengkap')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Lengkap'),
                        Forms\Components\Select::make('jabatan_usulan')
                            ->options([
                                'Pengurus' => 'Pengurus',
                                'Pengawas' => 'Pengawas',
                            ])
                            ->required()
                            ->label('Jabatan yang Diusulkan'),
                    ])->columns(2),

                Forms\Components\Section::make('Persyaratan Administratif (Upload Dokumen)')
                    ->description('Silakan unggah dokumen persyaratan dalam format PDF/JPG.')
                    ->schema([
                        Forms\Components\FileUpload::make('dokumen_cv')
                            ->label('Daftar Riwayat Hidup (CV)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->directory('dokumen-peserta')
                            ->required(),
                        Forms\Components\FileUpload::make('dokumen_ktp')
                            ->label('FC KTP / SIM / Passport')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(5120)
                            ->directory('dokumen-peserta')
                            ->required(),
                        Forms\Components\FileUpload::make('dokumen_pernyataan')
                            ->label('Surat Pernyataan Memenuhi Aspek (Lampiran VI)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->directory('dokumen-peserta')
                            ->required(),
                        Forms\Components\FileUpload::make('dokumen_skck')
                            ->label('SKCK')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(5120)
                            ->directory('dokumen-peserta')
                            ->required(),
                        Forms\Components\FileUpload::make('dokumen_slik')
                            ->label('SLIK OJK (Surat Asli)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->directory('dokumen-peserta')
                            ->required(),
                        Forms\Components\FileUpload::make('dokumen_rencana_kerja')
                            ->label('Tulisan Rencana Kerja (1 Tahun)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->directory('dokumen-peserta')
                            ->required(),
                        Forms\Components\FileUpload::make('dokumen_pailit')
                            ->label('Surat Keterangan Pailit dari Pengadilan Niaga')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->directory('dokumen-peserta')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->searchable()
                    ->label('Nama Peserta'),
                Tables\Columns\TextColumn::make('jabatan_usulan')
                    ->badge()
                    ->label('Usulan'),
                Tables\Columns\TextColumn::make('status_verifikasi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Revisi' => 'info',
                        'Terverifikasi' => 'success',
                        'Ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->label('Status Dokumen'),
                Tables\Columns\TextColumn::make('jadwal_wawancara')
                    ->dateTime('d M Y, H:i')
                    ->label('Jadwal Wawancara')
                    ->placeholder('Belum Dijadwalkan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_kelulusan')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'LULUS' => 'success',
                        'TIDAK LULUS' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->label('Hasil Akhir UKK'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('buka_link')
                    ->label('Tautan Assessment')
                    ->icon('heroicon-o-link')
                    ->color('info')
                    ->url(fn (Peserta $record) => url('/assessment/' . $record->token_assessment))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('cetak_sk')
                    ->label('Unduh SK')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn (Peserta $record) => $record->status_kelulusan === 'LULUS')
                    ->action(function (Peserta $record) {
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.sk-kelulusan', ['peserta' => $record]);
                        return response()->streamDownload(
                            fn () => print($pdf->output()), 
                            "SK_Kelulusan_UKK_{$record->nik_peserta}.pdf"
                        );
                    }),
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
            'index' => Pages\ListPesertas::route('/'),
            'create' => Pages\CreatePeserta::route('/create'),
            'edit' => Pages\EditPeserta::route('/{record}/edit'),
        ];
    }
}
