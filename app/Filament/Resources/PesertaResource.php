<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PesertaResource\Pages;
use App\Models\Peserta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PesertaResource extends Resource
{
    protected static ?string $model = Peserta::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Peserta UKK';
    protected static ?string $modelLabel = 'Peserta UKK';
    protected static ?string $navigationGroup = 'Verifikasi UKK';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar Peserta')
                    ->description('Pilih koperasi dan isi data diri peserta.')
                    ->schema([
                        Forms\Components\Select::make('koperasi_id')
                            ->relationship('koperasi', 'nama_koperasi')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Koperasi Asal'),
                        Forms\Components\TextInput::make('nik_peserta')
                            ->required()
                            ->maxLength(16)
                            ->readOnly()
                            ->unique(ignoreRecord: true)
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
                            ->openable()
                            ->downloadable()
                            ->directory('dokumen-peserta'),
                        Forms\Components\FileUpload::make('dokumen_ktp')
                            ->label('FC KTP / SIM / Passport')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(5120)
                            ->openable()
                            ->downloadable()
                            ->directory('dokumen-peserta'),
                        Forms\Components\FileUpload::make('dokumen_pernyataan')
                            ->label('Surat Pernyataan Memenuhi Aspek (Lampiran VI)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->openable()
                            ->downloadable()
                            ->directory('dokumen-peserta'),
                        Forms\Components\FileUpload::make('dokumen_skck')
                            ->label('SKCK')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(5120)
                            ->openable()
                            ->downloadable()
                            ->directory('dokumen-peserta'),
                        Forms\Components\FileUpload::make('dokumen_slik')
                            ->label('SLIK OJK (Surat Asli)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->openable()
                            ->downloadable()
                            ->directory('dokumen-peserta'),
                        Forms\Components\FileUpload::make('dokumen_rencana_kerja')
                            ->label('Tulisan Rencana Kerja (1 Tahun)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->openable()
                            ->downloadable()
                            ->directory('dokumen-peserta'),
                        Forms\Components\FileUpload::make('dokumen_pailit')
                            ->label('Surat Keterangan Pailit dari Pengadilan Niaga')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->openable()
                            ->downloadable()
                            ->directory('dokumen-peserta'),
                    ])->columns(2),

                Forms\Components\Section::make('Verifikasi Administratif (12 Indikator Juklak)')
                    ->description('Beri centang pada dokumen yang sah. Peserta (Maks 7), Koperasi (Maks 5).')
                    ->schema([
                        Forms\Components\CheckboxList::make('penilaian_admin_checklist.peserta')
                            ->label('Indikator Administratif Peserta (Wajib Lolos = 7)')
                            ->options([
                                'cv' => '1. Daftar Riwayat Hidup (CV)',
                                'ktp' => '2. FC KTP / SIM / Passport',
                                'pernyataan' => '3. Surat Pernyataan Memenuhi Aspek (Lampiran VI)',
                                'skck' => '4. SKCK',
                                'slik' => '5. SLIK OJK (Surat Asli)',
                                'rencana_kerja' => '6. Tulisan Rencana Kerja Menjadi Pengurus 1 Tahun',
                                'pailit' => '7. Surat Keterangan Pailit dari Pengadilan Niaga',
                            ])
                            ->columns(2)
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::kalkulasiSkor($set, $get)),

                        Forms\Components\CheckboxList::make('penilaian_admin_checklist.koperasi')
                            ->label('Indikator Administratif Koperasi (Wajib Lolos = 6)')
                            ->options([
                                'pad' => '1. PAD dan Pengesahan',
                                'rat' => '2. Berita Acara RAT',
                                'lap_keuangan' => '3. Laporan Keuangan (Neraca PHU yang sudah diaudit)',
                                'goaml' => '4. Dokumen GoAML',
                                'npwp' => '5. NPWP Koperasi',
                                'permohonan' => '6. Surat Permohonan',    
                            ])
                            ->columns(2)
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::kalkulasiSkor($set, $get)),
                            
                        Forms\Components\Select::make('status_verifikasi')
                            ->options([
                                'Pending' => 'Pending (Menunggu Verifikasi)',
                                'Revisi' => 'Perlu Revisi Dokumen',
                                'Terverifikasi' => 'Terverifikasi (Lanjut ke UKK)',
                                'Ditolak' => 'Ditolak (Tidak Memenuhi Syarat)',
                            ])
                            ->default('Pending')
                            ->required()
                            ->live() 
                            ->label('Keputusan Status Administratif'),

                        Forms\Components\Textarea::make('keterangan') 
                            ->label('Keterangan / Alasan (Revisi / Penolakan)')
                            ->placeholder('Tuliskan detail dokumen yang harus direvisi atau alasan penolakan...')
                            ->rows(3)
                            ->visible(fn ($get) => in_array($get('status_verifikasi'), ['Revisi', 'Ditolak']))
                            ->required(fn ($get) => in_array($get('status_verifikasi'), ['Revisi', 'Ditolak']))
                            ->columnSpanFull(),
                            
                        Forms\Components\Placeholder::make('info_sa')
                            ->label('Hasil Self Assessment Peserta')
                            ->content(function (Peserta $record) {
                                $sa = \App\Models\SelfAssessment::where('peserta_id', $record->id)->first();
                                if (!$sa) return new \Illuminate\Support\HtmlString('<span class="text-red-600">Peserta belum mengisi Self Assessment.</span>');
                                
                                $totalSA = $sa->integritas_score + $sa->reputasi_score + $sa->kompetensi_score + $sa->inovasi_score;
                                return new \Illuminate\Support\HtmlString("<span class='text-green-600 font-bold'>Sudah diisi. Total Skor SA: {$totalSA} / 59</span>");
                            }),

                        Forms\Components\TextInput::make('skor_admin')
                            ->label('Skor Verifikasi Dokumen (Maks. 17)')
                            ->numeric()
                            ->readOnly(),
                            
                        Forms\Components\Placeholder::make('total_administratif')
                            ->label('Total Penilaian Administratif (Juklak)')
                            ->content(function (Peserta $record, callable $get) {
                                $sa = \App\Models\SelfAssessment::where('peserta_id', $record->id)->first();
                                $totalSA = $sa ? ($sa->integritas_score + $sa->reputasi_score + $sa->kompetensi_score + $sa->inovasi_score) : 0;
                                $skorDokumen = (int) $get('skor_admin');
                                
                                $grandTotal = $totalSA + $skorDokumen;
                                $color = $grandTotal >= 58 ? 'text-green-600' : 'text-red-600';
                                $status = $grandTotal >= 58 ? 'Memenuhi Syarat Lanjut Wawancara' : 'Tidak Memenuhi Syarat';
                                
                                return new \Illuminate\Support\HtmlString("<span class='{$color} font-bold text-lg'>{$grandTotal} / 76</span><br><span class='text-sm text-slate-500'>Syarat Lolos: >= 58. Status: {$status}</span>");
                            }),
                    ])->columns(1),

                Forms\Components\Section::make('Persiapan & Penjadwalan Wawancara (Modul 4)')
                    ->schema([
                        Forms\Components\DateTimePicker::make('jadwal_wawancara')
                            ->label('Atur Jadwal Wawancara'),
                        Forms\Components\Select::make('penguji_id')
                            ->label('Tugaskan Tim Penguji (Maks. 4)')
                            ->options(\App\Models\Penguji::pluck('name', 'id'))
                            ->multiple()
                            ->maxItems(4)
                            ->searchable()
                            ->preload(),
                        Forms\Components\Placeholder::make('dokumen_paparan')
                            ->label('Dokumen Paparan Peserta')
                            ->content(fn (Peserta $record) => $record?->dokumen_paparan 
                                ? new \Illuminate\Support\HtmlString('<a href="/storage/'.$record->dokumen_paparan.'" target="_blank" class="text-blue-600 underline">Lihat Paparan</a>') 
                                : 'Belum diunggah'),
                    ])->columns(3),

                Forms\Components\Section::make('Hasil Akhir UKK')
                    ->schema([
                        Forms\Components\TextInput::make('skor_total')
                            ->label('Total Kumulatif (Akhir)')
                            ->readOnly(),
                        Forms\Components\TextInput::make('status_kelulusan')
                            ->maxLength(255)
                            ->readOnly()
                            ->label('Status Kelulusan Juklak (Lulus >= 82)'),
                    ])->columns(2),
            ]);
    }

    public static function kalkulasiSkor(callable $set, callable $get)
    {
        // Kalkulasi Verifikasi Dokumen Admin (Maks 17)
        $pesertaChecklist = $get('penilaian_admin_checklist.peserta') ?? [];
        $koperasiChecklist = $get('penilaian_admin_checklist.koperasi') ?? [];
        $skorAdmin = count($pesertaChecklist) + count($koperasiChecklist);
        $set('skor_admin', $skorAdmin);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('koperasi.nama_koperasi')
                    ->label('Koperasi Asal')
                    ->searchable()
                    ->sortable()
                    ->limit(20),
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
                    ->label('Verifikasi'),
                Tables\Columns\TextColumn::make('status_sa')
                    ->label('Status SA')
                    ->badge()
                    ->state(function ($record) {
                        return \App\Models\SelfAssessment::where('peserta_id', $record->id)->exists() ? 'Selesai' : 'Belum';
                    })
                    ->color(fn ($state) => $state === 'Selesai' ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('jadwal_wawancara')
                    ->dateTime('d M Y, H:i')
                    ->label('Jadwal Wawancara')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('skor_wawancara')
                    ->numeric()
                    ->sortable()
                    ->label('Skor Wawancara'),
                Tables\Columns\TextColumn::make('status_kelulusan')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'LULUS' => 'success',
                        'TIDAK LULUS' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->label('Hasil Akhir'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_verifikasi')
                    ->options([
                        'Pending' => 'Pending',
                        'Revisi' => 'Revisi',
                        'Terverifikasi' => 'Terverifikasi',
                        'Ditolak' => 'Ditolak',
                    ]),
                Tables\Filters\SelectFilter::make('jabatan_usulan')
                    ->options([
                        'Pengurus' => 'Pengurus',
                        'Pengawas' => 'Pengawas',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('cetak_sk')
                    ->label('Cetak SK')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    // Tombol cetak PDF hanya muncul jika statusnya LULUS
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
