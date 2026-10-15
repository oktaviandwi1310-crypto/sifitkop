<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SelfAssessmentResource\Pages;
use App\Models\SelfAssessment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;

class SelfAssessmentResource extends Resource
{
    protected static ?string $model = SelfAssessment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Self Assessment';
    protected static ?string $navigationGroup = 'Verifikasi UKK';

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Forms\Components\Section::make('Identitas Peserta')
    //                 ->schema([
    //                     Forms\Components\Select::make('peserta_id')
    //                         ->relationship('peserta', 'nama_lengkap')
    //                         ->required()
    //                         ->searchable()
    //                         ->label('Nama Peserta (Calon Pengurus/Pengawas)'),
    //                 ]),
                    
    //             Forms\Components\Section::make('Penilaian Mandiri (Skor per Variabel)')
    //                 ->description('Masukkan skor berdasarkan kuesioner Self Assessment. Skor akan diakumulasi secara otomatis berdasarkan standar Juklak Halaman 50.')
    //                 ->schema([
    //                     Forms\Components\TextInput::make('integritas_score')
    //                         ->numeric()
    //                         ->required()
    //                         ->default(0)
    //                         ->label('Skor Integritas (Terpenuhi >= 9)')
    //                         ->live(onBlur: true)
    //                         ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::kalkulasiKelulusan($set, $get)),
                            
    //                     Forms\Components\TextInput::make('reputasi_score')
    //                         ->numeric()
    //                         ->required()
    //                         ->default(0)
    //                         ->label('Skor Reputasi Keuangan (Terpenuhi >= 2)')
    //                         ->live(onBlur: true)
    //                         ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::kalkulasiKelulusan($set, $get)),
                            
    //                     Forms\Components\TextInput::make('kompetensi_score')
    //                         ->numeric()
    //                         ->required()
    //                         ->default(0)
    //                         ->label('Skor Kompetensi (Terpenuhi >= 27)')
    //                         ->live(onBlur: true)
    //                         ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::kalkulasiKelulusan($set, $get)),
                            
    //                     Forms\Components\TextInput::make('inovasi_score')
    //                         ->numeric()
    //                         ->required()
    //                         ->default(0)
    //                         ->label('Skor Kreativitas & Inovasi (Terpenuhi >= 9)')
    //                         ->live(onBlur: true)
    //                         ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::kalkulasiKelulusan($set, $get)),
    //                 ])->columns(2),
                    
    //             Forms\Components\Section::make('Hasil Kelulusan Self Assessment')
    //                 ->schema([
    //                     Forms\Components\TextInput::make('status_kelulusan')
    //                         ->readOnly()
    //                         ->label('Status Kelayakan (Otomatis)')
    //                         ->helperText('Peserta harus memenuhi batas minimum di SEMUA aspek secara bersaman.'),
    //                 ]),
    //         ]);
    // }

    public static function form(Form $form): Form
    {
        // Data Label Kuesioner (Sesuai Form Peserta)
        $integritasLabels = [
            'A. Cakap melakukan perbuatan hukum.',
            'B.1. Tidak sedang menjalani hukuman pidana yang telah berkekuatan hukum tetap.',
            'B.2. Tidak pernah dihukum karena melakukan tindak pidana yang merugikan koperasi/keuangan negara (5 tahun terakhir).',
            'C. Memiliki komitmen mematuhi peraturan perundang-undangan dan mendukung kebijakan Kementerian.',
            'D.1. Tidak pernah melanggar komitmen terhadap Koperasi (dalam 3 tahun terakhir).',
            'D.2. Tidak mengulangi perbuatan yang dilarang bagi pengurus.',
            'D.3. Tidak melakukan perbuatan yang memberikan keuntungan tidak wajar yang merugikan.',
            'D.4. Tidak melakukan perbuatan yang melanggar ketentuan, kewenangan, dan tanggung jawab.',
            'D.5. Tidak pernah dinyatakan tidak mampu atau gagal menjalankan tugas jabatannya sebelumnya.',
        ];

        $reputasiLabels = [
            '1. Tidak memiliki kredit macet/pembiayaan macet (berdasarkan SLIK OJK/dibuktikan dengan Surat Pernyataan).',
            '2. Tidak pernah dinyatakan pailit dalam waktu 5 tahun sebelum pencalonan.',
        ];

        $kompetensiLabels = [
            '1. Pengetahuan manajemen, SDM, kelembagaan, permodalan, dan akuntansi Koperasi.',
            '2. Kemampuan dalam mencapai tujuan organisasi Koperasi secara berkesinambungan.',
            '3. Kemampuan mengelola sumber daya manusia (SDM).',
            '4. Pemahaman terhadap Undang-Undang Perkoperasian dan peraturan turunannya.',
            '5. Pemahaman UU Tindak Pidana Pencucian Uang dan/atau UU lain yang terkait.',
            '6. Kemampuan merumuskan dan mengawal realisasi visi, misi, dan target.',
            '7. Kemampuan menetapkan target, prioritas, serta strategi mitigasi risiko.',
            '8. Kemampuan merumuskan kebijakan yang relevan.',
            '9. Keahlian dan pengalaman di bidang koperasi/keuangan minimum 2 tahun.',
        ];

        $inovasiLabels = [
            '1. Kemampuan dalam merencanakan pemanfaatan teknologi informasi untuk pengawasan/pengelolaan koperasi.',
            '2. Kemampuan dalam mengembangkan bisnis dan merespons tren pasar secara adaptif.',
            '3. Dukungan terhadap digitalisasi layanan dan operasional Koperasi (Core Banking System / Mobile).',
        ];

        $klarifikasiLabels = [
            '1. Jelaskan kewajiban dan tanggung jawab Saudara jika nanti terpilih sebagai Pengurus dan Pengawas Koperasi.',
            '2. Jelaskan mengenai produk/usaha Koperasi saat ini.',
            '3. Jelaskan mengenai perizinan yang telah dimiliki oleh Koperasi.',
            '4. Jelaskan mengenai mekanisme dan peraturan mengenai pembagian Sisa Hasil Usaha (SHU) di Koperasi.',
            '5. Jelaskan pemahaman Saudara mengenai mekanisme pengambilan keputusan di Koperasi.',
            '6. Jelaskan mekanisme pengawasan internal yang dilakukan di koperasi.',
            '7. Jelaskan pemahaman Saudara mengenai peraturan yang harus dimiliki oleh Koperasi dan bagaimana proses pengesahannya.',
            '8. Apakah Saudara pernah bertindak sebagai Pengurus dan Pengawas Koperasi yang dipailitkan/PKPU?',
            '9. Informasikan secara detail mengenai Simpanan Pokok, Simpanan Wajib ataupun Simpanan Lainnya di Koperasi.',
            '10. Jelaskan pemahaman Saudara mengenai modal penyertaan pada Koperasi.',
            '11. Apakah Saudara/i merangkap jabatan sebagai pengurus pada KSP/KSPPS lain?',
            '12. Apakah Saudara/i merangkap jabatan sebagai pengawas pada KSP/KSPPS lain?',
            '13. Apakah saudara dapat menjelaskan informasi negatif yang telah kami terima dari pemberitaan atau pengaduan?',
            '14. Apakah Saudara/i memiliki hubungan semenda dengan pengurus dan pengawas di KSP/KSPPS?',
            '15. Jelaskan alasan saudara tidak direkomendasikan di dalam rapat anggota sebagai pengurus atau pengawas.',
        ];

        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Peserta')
                    ->schema([
                        Forms\Components\Select::make('peserta_id')
                            ->relationship('peserta', 'nama_lengkap')
                            ->required()
                            ->searchable()
                            ->disabledOn('edit')
                            ->label('Nama Peserta (Calon Pengurus/Pengawas)'),
                    ]),

                // SECTION 1: PENILAIAN MANDIRI (SKOR PER VARIABEL)
                Forms\Components\Section::make('Penilaian Mandiri (Skor per Variabel)')
                    ->description('Masukkan skor berdasarkan kuesioner Self Assessment. Skor akan diakumulasi secara otomatis berdasarkan standar Juklak Halaman 50.')
                    ->schema([
                        Forms\Components\TextInput::make('integritas_score')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->live() // <-- Tambahkan live() di sini
                            ->label('Skor Integritas (Terpenuhi >= 9)')
                            ->afterStateUpdated(fn ($set, $get) => self::kalkulasiKelulusanManual($set, $get)),

                        Forms\Components\TextInput::make('reputasi_score')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->live() // <-- Tambahkan live() di sini
                            ->label('Skor Reputasi Keuangan (Terpenuhi >= 2)')
                            ->afterStateUpdated(fn ($set, $get) => self::kalkulasiKelulusanManual($set, $get)),

                        Forms\Components\TextInput::make('kompetensi_score')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->live() // <-- Tambahkan live() di sini
                            ->label('Skor Kompetensi (Terpenuhi >= 27)')
                            ->afterStateUpdated(fn ($set, $get) => self::kalkulasiKelulusanManual($set, $get)),

                        Forms\Components\TextInput::make('inovasi_score')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->live() // <-- Tambahkan live() di sini
                            ->label('Skor Kreativitas & Inovasi (Terpenuhi >= 9)')
                            ->afterStateUpdated(fn ($set, $get) => self::kalkulasiKelulusanManual($set, $get)),
                    ])->columns(2),

                // SECTION 2: HASIL KELULUSAN
                Forms\Components\Section::make('Hasil Kelulusan Self Assessment')
                    ->schema([
                        Forms\Components\TextInput::make('status_kelulusan')
                            ->readOnly()
                            ->label('Status Kelayakan (Otomatis)')
                            ->helperText('Peserta harus memenuhi batas minimum di SEMUA aspek secara bersamaan.'),
                    ]),

                // SECTION 3: RINCIAN KUESIONER (DIBAWAH HASIL KELULUSAN)
                    Forms\Components\Section::make('Rincian Kuesioner & Jawaban (Dapat Diedit Admin)')
                        ->description('Mengubah nilai indikator di bawah ini akan memperbarui total skor dan hasil kelulusan di atas secara otomatis.')
                        ->schema([
                            Forms\Components\Tabs::make('Kuesioner Tabs')
                                ->tabs([
                                    // TAB INTEGRITAS
                                    Forms\Components\Tabs\Tab::make('Persyaratan Integritas')
                                        ->schema([
                                            Forms\Components\Repeater::make('jawaban_raw.integritas')
                                                ->label('Indikator Integritas')
                                                ->schema([
                                                    Forms\Components\Placeholder::make('pertanyaan')
                                                        ->label('Pertanyaan / Indikator')
                                                        ->content(function ($component) use ($integritasLabels) {
                                                            $statePath = $component->getContainer()->getStatePath(); // misal: data.jawaban_raw.integritas.0
                                                            $parts = explode('.', $statePath);
                                                            $index = (int) end($parts);
                                                            return $integritasLabels[$index] ?? 'Indikator Integritas';
                                                        }),
                                                    Forms\Components\Radio::make('skor')
                                                        ->options([1 => 'Ya', 0 => 'Tidak'])
                                                        ->inline()
                                                        ->required()
                                                        ->live()
                                                        ->afterStateUpdated(fn (Set $set, Get $get) => self::kalkulasiUlangSemua($set, $get))
                                                        ->label('Pilihan'),
                                                    Forms\Components\Textarea::make('uraian')
                                                        ->rows(1)
                                                        ->label('Uraian/Keterangan'),
                                                ])
                                                ->columns(1)
                                                ->addable(false)
                                                ->deletable(false)
                                                ->reorderable(false),
                                        ]),

                                    // TAB REPUTASI
                                    Forms\Components\Tabs\Tab::make('Reputasi Keuangan')
                                        ->schema([
                                            Forms\Components\Repeater::make('jawaban_raw.reputasi')
                                                ->label('Indikator Reputasi Keuangan')
                                                ->schema([
                                                    Forms\Components\Placeholder::make('pertanyaan')
                                                        ->label('Pertanyaan / Indikator')
                                                        ->content(function ($component) use ($reputasiLabels) {
                                                            $parts = explode('.', $component->getContainer()->getStatePath());
                                                            $index = (int) end($parts);
                                                            return $reputasiLabels[$index] ?? 'Indikator Reputasi';
                                                        }),
                                                    Forms\Components\Radio::make('skor')
                                                        ->options([1 => 'Ya', 0 => 'Tidak'])
                                                        ->inline()
                                                        ->required()
                                                        ->live()
                                                        ->afterStateUpdated(fn (Set $set, Get $get) => self::kalkulasiUlangSemua($set, $get))
                                                        ->label('Pilihan'),
                                                    Forms\Components\Textarea::make('uraian')
                                                        ->rows(1)
                                                        ->label('Uraian/Keterangan'),
                                                ])
                                                ->columns(1)
                                                ->addable(false)
                                                ->deletable(false)
                                                ->reorderable(false),
                                        ]),

                                    // TAB KOMPETENSI
                                    Forms\Components\Tabs\Tab::make('Persyaratan Kompetensi')
                                        ->schema([
                                            Forms\Components\Repeater::make('jawaban_raw.kompetensi')
                                                ->label('Indikator Kompetensi')
                                                ->schema([
                                                    Forms\Components\Placeholder::make('pertanyaan')
                                                        ->label('Pertanyaan / Indikator')
                                                        ->content(function ($component) use ($kompetensiLabels) {
                                                            $parts = explode('.', $component->getContainer()->getStatePath());
                                                            $index = (int) end($parts);
                                                            return $kompetensiLabels[$index] ?? 'Indikator Kompetensi';
                                                        }),
                                                    Forms\Components\Radio::make('skor')
                                                        ->options([
                                                            1 => '1 (Sangat Kurang)',
                                                            2 => '2 (Kurang)',
                                                            3 => '3 (Cukup)',
                                                            4 => '4 (Baik)',
                                                        ])
                                                        ->inline()
                                                        ->required()
                                                        ->live()
                                                        ->afterStateUpdated(fn (Set $set, Get $get) => self::kalkulasiUlangSemua($set, $get))
                                                        ->label('Skor'),
                                                    Forms\Components\Textarea::make('uraian')
                                                        ->rows(2)
                                                        ->label('Uraian/Keterangan'),
                                                ])
                                                ->columns(1)
                                                ->addable(false)
                                                ->deletable(false)
                                                ->reorderable(false),
                                        ]),

                                    // TAB INOVASI
                                    Forms\Components\Tabs\Tab::make('Kreativitas & Inovasi')
                                        ->schema([
                                            Forms\Components\Repeater::make('jawaban_raw.inovasi')
                                                ->label('Indikator Inovasi')
                                                ->schema([
                                                    Forms\Components\Placeholder::make('pertanyaan')
                                                        ->label('Pertanyaan / Indikator')
                                                        ->content(function ($component) use ($inovasiLabels) {
                                                            $parts = explode('.', $component->getContainer()->getStatePath());
                                                            $index = (int) end($parts);
                                                            return $inovasiLabels[$index] ?? 'Indikator Inovasi';
                                                        }),
                                                    Forms\Components\Radio::make('skor')
                                                    ->options([
                                                        1 => 'Ya',
                                                        0 => 'Tidak',
                                                    ])
                                                    ->inline()
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(function ($set, $get) {
                                                        self::kalkulasiUlangSemua($set, $get);
                                                    })
                                                    ->label('Pilihan'),
                                                    Forms\Components\Textarea::make('uraian')
                                                        ->rows(2)
                                                        ->label('Uraian/Keterangan'),
                                                ])
                                                ->columns(1)
                                                ->addable(false)
                                                ->deletable(false)
                                                ->reorderable(false),
                                        ]),

                                    // TAB KLARIFIKASI
                                    Forms\Components\Tabs\Tab::make('Klarifikasi')
                                        ->schema([
                                            Forms\Components\Repeater::make('jawaban_raw.klarifikasi')
                                                ->label('Daftar Pertanyaan Klarifikasi')
                                                ->schema([
                                                    Forms\Components\Placeholder::make('pertanyaan')
                                                        ->label('Pertanyaan Klarifikasi')
                                                        ->content(function ($component) use ($klarifikasiLabels) {
                                                            $parts = explode('.', $component->getContainer()->getStatePath());
                                                            $index = (int) end($parts);
                                                            return $klarifikasiLabels[$index] ?? 'Pertanyaan Klarifikasi';
                                                        }),
                                                    Forms\Components\Textarea::make('jawaban')
                                                        ->rows(2)
                                                        ->label('Jawaban Peserta'),
                                                ])
                                                ->columns(1)
                                                ->addable(false)
                                                ->deletable(false)
                                                ->reorderable(false),
                                        ]),
                                ]),
                        ]),
            ]);
    }

    /**
     * Otomatis Menghitung Ulang Total Skor & Status Kelulusan ketika Skor Indikator diubah oleh Admin
     */
    public static function kalkulasiUlangSemua($set, $get)
    {
        // Mengambil data jawaban_raw dari root form
        $jawabanRaw = $get('../../jawaban_raw') ?? $get('jawaban_raw') ?? [];

        $integritasItems = $jawabanRaw['integritas'] ?? [];
        $reputasiItems = $jawabanRaw['reputasi'] ?? [];
        $kompetensiItems = $jawabanRaw['kompetensi'] ?? [];
        $inovasiItems = $jawabanRaw['inovasi'] ?? [];

        $sumIntegritas = array_reduce($integritasItems, fn ($carry, $item) => $carry + (int) ($item['skor'] ?? 0), 0);
        $sumReputasi = array_reduce($reputasiItems, fn ($carry, $item) => $carry + (int) ($item['skor'] ?? 0), 0);
        $sumKompetensi = array_reduce($kompetensiItems, fn ($carry, $item) => $carry + (int) ($item['skor'] ?? 0), 0);
        $sumInovasi = array_reduce($inovasiItems, fn ($carry, $item) => $carry + (int) ($item['skor'] ?? 0), 0);

        // Set nilai skor ke field utama di atas
        $set('../../integritas_score', $sumIntegritas);
        $set('../../reputasi_score', $sumReputasi);
        $set('../../kompetensi_score', $sumKompetensi);
        $set('../../inovasi_score', $sumInovasi);

        // Juga set tanpa ../../ sebagai cadangan
        $set('integritas_score', $sumIntegritas);
        $set('reputasi_score', $sumReputasi);
        $set('kompetensi_score', $sumKompetensi);
        $set('inovasi_score', $sumInovasi);

        // Cek Status Kelulusan
        $status = ($sumIntegritas >= 9 && $sumReputasi >= 2 && $sumKompetensi >= 27 && $sumInovasi >= 9) 
            ? 'Terpenuhi' 
            : 'Tidak Terpenuhi';

        $set('../../status_kelulusan', $status);
        $set('status_kelulusan', $status);
    }

    /**
     * Otomatis Menghitung Kelulusan jika Admin Mengubah Input Skor Manual Secara Langsung
     */
    public static function kalkulasiKelulusanManual($set, $get)
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
                Tables\Columns\TextColumn::make('reputasi_score')->label('Reputasi')->sortable(),
                Tables\Columns\TextColumn::make('kompetensi_score')->label('Kompetensi')->sortable(),
                Tables\Columns\TextColumn::make('inovasi_score')->label('Inovasi')->sortable(),
                Tables\Columns\TextColumn::make('status_kelulusan')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Terpenuhi' => 'success',
                        'Tidak Terpenuhi' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    // public static function kalkulasiKelulusan(callable $set, callable $get)
    // {
    //     $integritas = (int) $get('integritas_score');
    //     $reputasi = (int) $get('reputasi_score');
    //     $kompetensi = (int) $get('kompetensi_score');
    //     $inovasi = (int) $get('inovasi_score');
        
    //     if ($integritas >= 9 && $reputasi >= 2 && $kompetensi >= 27 && $inovasi >= 9) {
    //         $set('status_kelulusan', 'Terpenuhi');
    //     } else {
    //         $set('status_kelulusan', 'Tidak Terpenuhi');
    //     }
    // }

    // public static function table(Table $table): Table
    // {
    //     return $table
    //         ->columns([
    //             Tables\Columns\TextColumn::make('peserta.nama_lengkap')
    //                 ->searchable()
    //                 ->sortable()
    //                 ->label('Nama Peserta'),
    //             Tables\Columns\TextColumn::make('integritas_score')->label('Integritas')->sortable(),
    //             Tables\Columns\TextColumn::make('reputasi_score')->label('Reputasi Keuangan')->sortable(),
    //             Tables\Columns\TextColumn::make('kompetensi_score')->label('Kompetensi')->sortable(),
    //             Tables\Columns\TextColumn::make('inovasi_score')->label('Kreativitas & Inovasi')->sortable(),
    //             Tables\Columns\TextColumn::make('status_kelulusan')
    //                 ->badge()
    //                 ->color(fn (?string $state): string => match ($state) {
    //                     'Terpenuhi' => 'success',
    //                     'Tidak Terpenuhi' => 'danger',
    //                     default => 'gray',
    //                 }),
    //             Tables\Columns\TextColumn::make('updated_at')
    //                 ->dateTime()
    //                 ->sortable()
    //                 ->toggleable(isToggledHiddenByDefault: true),
    //         ])
    //         ->filters([
    //             //
    //         ])
    //         ->actions([
    //             Tables\Actions\EditAction::make(),
    //         ])
    //         ->bulkActions([
    //             Tables\Actions\BulkActionGroup::make([
    //                 Tables\Actions\DeleteBulkAction::make(),
    //             ]),
    //         ]);
    // }

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
