<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KoperasiResource\Pages;
use App\Filament\Resources\KoperasiResource\RelationManagers;
use App\Models\Koperasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KoperasiResource extends Resource
{
    protected static ?string $model = Koperasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Master Koperasi';
    protected static ?string $modelLabel = 'Koperasi';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $recordTitleAttribute = 'nama_koperasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Profil Koperasi')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Identitas & Legalitas')
                            ->schema([
                                Forms\Components\TextInput::make('nik')
                                    ->required()
                                    ->maxLength(16)
                                    ->suffixAction(
                                        \Filament\Forms\Components\Actions\Action::make('tarik_ods')
                                            ->icon('heroicon-m-cloud-arrow-down')
                                            ->label('Tarik ODS')
                                            ->action(function ($state, \Filament\Forms\Set $set) {
                                                if (empty($state)) {
                                                    \Filament\Notifications\Notification::make()->title('Masukkan NIK terlebih dahulu')->warning()->send();
                                                    return;
                                                }
                                                $odsService = new \App\Services\OdsApiService();
                                                $result = $odsService->getDetail($state);
                                                if ($result['success']) {
                                                    $data = $result['data'];
                                                    $set('nama_koperasi', $data['koperasi'] ?? $data['nama'] ?? '');
                                                    $set('badan_hukum', $data['badan_hukum'] ?? $data['bh'] ?? '');
                                                    $set('tanggal_badan_hukum', $data['tanggal_badan_hukum'] ?? '');
                                                    $set('kabupaten_kota', $data['kabupaten'] ?? $data['kota'] ?? '');
                                                    $set('alamat', $data['alamat'] ?? '');
                                                    $set('status_ods', $data['status'] ?? 'Aktif');
                                                    $set('klasifikasi_koperasi', $data['bentuk_koperasi'] ?? $data['bentuk'] ?? '');

                                                    if (isset($data['pengurus']) && is_array($data['pengurus'])) {
                                                        $pengurus = [];
                                                        $pengawas = [];
                                                        foreach ($data['pengurus'] as $p) {
                                                            $jabatan = strtolower($p['jabatan'] ?? '');
                                                            if (str_contains($jabatan, 'pengawas')) {
                                                                $pengawas[] = ['nama' => $p['nama'] ?? '', 'jabatan' => $p['jabatan'] ?? 'Pengawas'];
                                                            } else {
                                                                $pengurus[] = ['nama' => $p['nama'] ?? '', 'jabatan' => $p['jabatan'] ?? ''];
                                                            }
                                                        }
                                                        $set('susunan_pengurus', $pengurus);
                                                        $set('susunan_pengawas', $pengawas);
                                                    }

                                                    \Filament\Notifications\Notification::make()->title('Data Berhasil Ditarik dari ODS')->success()->send();
                                                } else {
                                                    \Filament\Notifications\Notification::make()->title('Gagal: ' . $result['message'])->danger()->send();
                                                }
                                            })
                                    ),
                                Forms\Components\TextInput::make('nama_koperasi')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('badan_hukum')
                                    ->maxLength(255)->label('Nomor Badan Hukum'),
                                Forms\Components\TextInput::make('tanggal_badan_hukum')
                                    ->maxLength(255)->label('Tanggal Badan Hukum'),
                                Forms\Components\TextInput::make('kabupaten_kota')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('alamat')
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('status_ods')
                                    ->options(['Aktif' => 'Aktif', 'Tidak Aktif' => 'Tidak Aktif'])
                                    ->default('Aktif'),
                                Forms\Components\Select::make('status_pengajuan')
                                    ->options(['Draft' => 'Draft', 'Pengajuan' => 'Pengajuan'])
                                    ->default('Draft'),
                                Forms\Components\TextInput::make('klasifikasi_koperasi')
                                    ->label('Jenis Koperasi')
                                    ->disabled()
                                    ->dehydrated(false) // Mencegah data tertimpa null saat disave jika field disabled
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Data Usaha')
                            ->schema([
                                Forms\Components\Repeater::make('data_usaha')
                                    ->label('Riwayat Data Usaha Koperasi (3 Tahun Terakhir)')
                                    ->schema([
                                        Forms\Components\TextInput::make('tahun')->label('Tahun Buku')->numeric()->required(),
                                        Forms\Components\TextInput::make('volume_usaha')->label('Volume Usaha (Rp)')->numeric(),
                                        Forms\Components\TextInput::make('aset')->label('Total Aset (Rp)')->numeric(),
                                        Forms\Components\TextInput::make('shu')->label('Sisa Hasil Usaha / SHU (Rp)')->numeric(),
                                        Forms\Components\TextInput::make('modal_sendiri')->label('Modal Sendiri (Rp)')->numeric(),
                                        Forms\Components\TextInput::make('modal_luar')->label('Modal Luar (Rp)')->numeric(),
                                    ])
                                    ->columns(2)
                                    ->collapsible()
                                    ->defaultItems(1),
                            ]),

                        Forms\Components\Tabs\Tab::make('Susunan Pengurus')
                            ->schema([
                                Forms\Components\Repeater::make('susunan_pengurus')
                                    ->label('Pengurus Saat Ini')
                                    ->schema([
                                        Forms\Components\TextInput::make('nama')->required(),
                                        Forms\Components\Select::make('jabatan')->options([
                                            'Ketua' => 'Ketua',
                                            'Sekretaris' => 'Sekretaris',
                                            'Bendahara' => 'Bendahara',
                                            'Anggota' => 'Anggota',
                                        ])->required(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0),
                            ]),

                        Forms\Components\Tabs\Tab::make('Susunan Pengawas')
                            ->schema([
                                Forms\Components\Repeater::make('susunan_pengawas')
                                    ->label('Pengawas Saat Ini')
                                    ->schema([
                                        Forms\Components\TextInput::make('nama')->required(),
                                        Forms\Components\Select::make('jabatan')->options([
                                            'Ketua Pengawas' => 'Ketua Pengawas',
                                            'Anggota Pengawas' => 'Anggota Pengawas',
                                        ])->required(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0),
                            ]),

                        Forms\Components\Tabs\Tab::make('Dokumen Persyaratan')
                            ->schema([
                                Forms\Components\FileUpload::make('dokumen_pad')
                                    ->label('PAD dan Pengesahan (PDF)')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->directory('dokumen_koperasi')
                                    ->openable()
                                    ->downloadable()
                                    ->required(),
                                Forms\Components\FileUpload::make('dokumen_rat')
                                    ->label('Berita Acara RAT (PDF)')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->directory('dokumen_koperasi')
                                    ->openable()
                                    ->downloadable()
                                    ->required(),
                                Forms\Components\FileUpload::make('dokumen_lap_keuangan')
                                    ->label('Laporan Keuangan - Neraca PHU (Audit/PDF)')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->directory('dokumen_koperasi')
                                    ->openable()
                                    ->downloadable()
                                    ->required(),
                                Forms\Components\FileUpload::make('dokumen_goaml')
                                    ->label('Dokumen GoAML (PDF)')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->directory('dokumen_koperasi')
                                    ->openable()
                                    ->downloadable()
                                    ->required(),
                                Forms\Components\FileUpload::make('dokumen_npwp')
                                    ->label('NPWP Koperasi (PDF/Gambar)')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->directory('dokumen_koperasi')
                                    ->openable()
                                    ->downloadable()
                                    ->required(),
                                Forms\Components\FileUpload::make('dokumen_permohonan')
                                    ->label('Surat Permohonan (PDF)')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->directory('dokumen_koperasi')
                                    ->openable()
                                    ->downloadable()
                                    ->required(),
                            ])->columns(1),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('nama_koperasi')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('badan_hukum')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kabupaten_kota')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('klasifikasi_koperasi')
                    ->label('Bentuk Koperasi')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status_ods')
                    ->label('ODS')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aktif' => 'success',
                        'Tidak Aktif' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_pengajuan')
                    ->label('Pengajuan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Draft' => 'gray',
                        'Pengajuan' => 'warning',
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        default => 'primary',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kabupaten_kota')
                    ->label('Kabupaten / Kota')
                    ->options(function () {
                        return \App\Models\Koperasi::query()
                            ->select('kabupaten_kota')
                            ->distinct()
                            ->whereNotNull('kabupaten_kota')
                            ->where('kabupaten_kota', '!=', '')
                            ->pluck('kabupaten_kota', 'kabupaten_kota')
                            ->toArray();
                    })
                    ->searchable(),
            ])
            ->paginationPageOptions([10, 25, 50, 100, 'all'])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('tarikDetailOds')
                    ->label('Tarik Detail ODS')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Koperasi $record) {
                        try {
                            $odsService = new \App\Services\OdsApiService();
                            $result = $odsService->getDetail($record->nik);
                            if ($result['success'] && isset($result['data'][0])) {
                                $odsData = $result['data'][0];
                                $pengurusList = $odsData['Pengurus'] ?? [];
                                
                                $susunanPengurus = [];
                                $susunanPengawas = [];
                                
                                foreach ($pengurusList as $p) {
                                    $nama = $p['Nama'] ?? '';
                                    $jabatanRaw = strtolower($p['IdJabatan'] ?? '');
                                    $statusKepengurusan = strtolower($p['StatusKepengurusan'] ?? '');
                                    
                                    if ($statusKepengurusan !== 'aktif') continue;
                                    
                                    if (isset($p['IdStatus']) && str_contains($p['IdStatus'], '1 - Pengurus')) {
                                        $jabatan = 'Anggota';
                                        if (str_contains($jabatanRaw, 'ketua')) $jabatan = 'Ketua';
                                        elseif (str_contains($jabatanRaw, 'sekretaris')) $jabatan = 'Sekretaris';
                                        elseif (str_contains($jabatanRaw, 'bendahara')) $jabatan = 'Bendahara';
                                        
                                        $susunanPengurus[] = [
                                            'nama' => $nama,
                                            'jabatan' => $jabatan
                                        ];
                                    } elseif (isset($p['IdStatus']) && str_contains($p['IdStatus'], '2 - Pengawas')) {
                                        $jabatan = 'Anggota Pengawas';
                                        if (str_contains($jabatanRaw, 'ketua')) $jabatan = 'Ketua Pengawas';
                                        
                                        $susunanPengawas[] = [
                                            'nama' => $nama,
                                            'jabatan' => $jabatan
                                        ];
                                    }
                                }
                                
                                $record->update([
                                    'susunan_pengurus' => empty($susunanPengurus) ? null : $susunanPengurus,
                                    'susunan_pengawas' => empty($susunanPengawas) ? null : $susunanPengawas,
                                ]);
                                
                                \Filament\Notifications\Notification::make()->title('Data Pengurus & Pengawas berhasil ditarik')->success()->send();
                            } else {
                                \Filament\Notifications\Notification::make()->title('Gagal menarik detail dari ODS')->danger()->send();
                            }
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()->title('Terjadi kesalahan saat memanggil API')->danger()->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('tarikDetailOdsBulk')
                    ->label('Tarik Detail ODS Terpilih')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion()
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $odsService = new \App\Services\OdsApiService();
                        $berhasil = 0;
                        
                        foreach ($records as $record) {
                            try {
                                $result = $odsService->getDetail($record->nik);
                                if ($result['success'] && isset($result['data'][0])) {
                                    $odsData = $result['data'][0];
                                    $pengurusList = $odsData['Pengurus'] ?? [];
                                    
                                    $susunanPengurus = [];
                                    $susunanPengawas = [];
                                    
                                    foreach ($pengurusList as $p) {
                                        $nama = $p['Nama'] ?? '';
                                        $jabatanRaw = strtolower($p['IdJabatan'] ?? '');
                                        $statusKepengurusan = strtolower($p['StatusKepengurusan'] ?? '');
                                        
                                        if ($statusKepengurusan !== 'aktif') continue;
                                        
                                        if (isset($p['IdStatus']) && str_contains($p['IdStatus'], '1 - Pengurus')) {
                                            $jabatan = 'Anggota';
                                            if (str_contains($jabatanRaw, 'ketua')) $jabatan = 'Ketua';
                                            elseif (str_contains($jabatanRaw, 'sekretaris')) $jabatan = 'Sekretaris';
                                            elseif (str_contains($jabatanRaw, 'bendahara')) $jabatan = 'Bendahara';
                                            
                                            $susunanPengurus[] = [
                                                'nama' => $nama,
                                                'jabatan' => $jabatan
                                            ];
                                        } elseif (isset($p['IdStatus']) && str_contains($p['IdStatus'], '2 - Pengawas')) {
                                            $jabatan = 'Anggota Pengawas';
                                            if (str_contains($jabatanRaw, 'ketua')) $jabatan = 'Ketua Pengawas';
                                            
                                            $susunanPengawas[] = [
                                                'nama' => $nama,
                                                'jabatan' => $jabatan
                                            ];
                                        }
                                    }
                                    
                                    $record->update([
                                        'susunan_pengurus' => empty($susunanPengurus) ? null : $susunanPengurus,
                                        'susunan_pengawas' => empty($susunanPengawas) ? null : $susunanPengawas,
                                    ]);
                                    $berhasil++;
                                }
                            } catch (\Exception $e) {
                                continue;
                            }
                        }
                        
                        \Filament\Notifications\Notification::make()->title("Detail ditarik untuk $berhasil koperasi")->success()->send();
                    }),
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
            'index' => Pages\ListKoperasis::route('/'),
            'create' => Pages\CreateKoperasi::route('/create'),
            'edit' => Pages\EditKoperasi::route('/{record}/edit'),
        ];
    }
}
