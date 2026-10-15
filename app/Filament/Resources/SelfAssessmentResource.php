<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SelfAssessmentResource\Pages;
use App\Models\SelfAssessment;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class SelfAssessmentResource extends Resource
{
    protected static ?string $model = SelfAssessment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Self Assessment';
    protected static ?string $navigationGroup = 'Verifikasi UKK';

    public static function form(Form $form): Form
    {
        // 1. Data Label Indikator Integritas (9 Butir)
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

        // 2. Data Label Reputasi Keuangan (2 Butir)
        $reputasiLabels = [
            '1. Tidak memiliki kredit macet/pembiayaan macet (berdasarkan SLIK OJK / dibuktikan dengan Surat Pernyataan).',
            '2. Tidak pernah dinyatakan pailit dalam waktu 5 tahun sebelum pencalonan.',
        ];

        // 3. Data Label Kompetensi (9 Butir)
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

        // 4. Data Label Kreativitas & Inovasi (3 Butir)
        $inovasiLabels = [
            '1. Kemampuan dalam merencanakan pemanfaatan teknologi informasi untuk pengawasan/pengelolaan koperasi.',
            '2. Kemampuan dalam mengembangkan bisnis dan merespons tren pasar secara adaptif.',
            '3. Dukungan terhadap digitalisasi layanan dan operasional Koperasi (Core Banking System / Mobile).',
        ];

        // 5. Data Label Klarifikasi (15 Butir Esai)
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
                // SECTION IDENTITAS
                Forms\Components\Section::make('Identitas Calon Pengurus / Pengawas')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Select::make('peserta_id')
                            ->relationship('peserta', 'nama_lengkap')
                            ->required()
                            ->searchable()
                            ->disabledOn('edit')
                            ->label('Nama Peserta UKK'),

                        Forms\Components\Placeholder::make('info_peserta')
                            ->label('Informasi Peserta & Koperasi')
                            ->content(function (?SelfAssessment $record) {
                                if (!$record || !$record->peserta) {
                                    return '-';
                                }
                                $peserta = $record->peserta;
                                $koperasi = $peserta->koperasi?->nama_koperasi ?? '-';
                                return new HtmlString("
                                    <div class='flex flex-wrap gap-4 text-sm'>
                                        <div><span class='text-gray-400'>Koperasi:</span> <b>{$koperasi}</b></div>
                                        <div><span class='text-gray-400'>NIK:</span> <b>{$peserta->nik_peserta}</b></div>
                                        <div><span class='text-gray-400'>Jabatan Usulan:</span> <span class='px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300'>{$peserta->jabatan_usulan}</span></div>
                                    </div>
                                ");
                            }),
                    ])->columns(2),

                // SECTION PERBANDINGAN 2 PENILAIAN
                Forms\Components\Section::make('Rekapitulasi: Penilaian Mandiri vs Penilaian Admin/Penguji')
                    ->description('Terdapat 2 penilaian: (1) Penilaian Mandiri yang diisi oleh calon pengurus via portal, dan (2) Penilaian resmi yang diverifikasi & dinilai oleh Admin/Penguji.')
                    ->icon('heroicon-o-scale')
                    ->schema([
                        // SUB-SECTION KIRI: PENILAIAN MANDIRI CALON PENGURUS
                        Forms\Components\Section::make('1. Penilaian Mandiri (Calon Pengurus)')
                            ->description('Hasil skor yang diinput langsung oleh calon peserta saat pengisian form kuesioner mandiri.')
                            ->schema([
                                Forms\Components\TextInput::make('skor_mandiri_integritas')
                                    ->label('Skor Integritas (Maks. 9)')
                                    ->readOnly()
                                    ->numeric()
                                    ->helperText('Syarat Terpenuhi: >= 9'),
                                Forms\Components\TextInput::make('skor_mandiri_reputasi')
                                    ->label('Skor Reputasi (Maks. 2)')
                                    ->readOnly()
                                    ->numeric()
                                    ->helperText('Syarat Terpenuhi: >= 2'),
                                Forms\Components\TextInput::make('skor_mandiri_kompetensi')
                                    ->label('Skor Kompetensi (Maks. 36)')
                                    ->readOnly()
                                    ->numeric()
                                    ->helperText('Syarat Terpenuhi: >= 27'),
                                Forms\Components\TextInput::make('skor_mandiri_inovasi')
                                    ->label('Skor Inovasi (Maks. 12)')
                                    ->readOnly()
                                    ->numeric()
                                    ->helperText('Syarat Terpenuhi: >= 9'),
                                Forms\Components\TextInput::make('status_mandiri')
                                    ->label('Status Kelayakan Mandiri')
                                    ->readOnly()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpan(1),

                        // SUB-SECTION KANAN: PENILAIAN ADMIN / PENGUJI
                        Forms\Components\Section::make('2. Penilaian dari Admin / Penguji')
                            ->description('Skor hasil verifikasi dan penilaian asesor. Nilai ini menjadi acuan resmi kelulusan Self Assessment.')
                            ->schema([
                                Forms\Components\Select::make('penguji_id')
                                    ->label('Tim Penguji / Asesor Penilai')
                                    ->options(User::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->columnSpanFull()
                                    ->helperText('Pilih penguji yang melakukan penilaian verifikasi ini.'),

                                Forms\Components\TextInput::make('skor_penguji_integritas')
                                    ->label('Skor Integritas Penguji (>= 9)*')
                                    ->numeric()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($set, $get) => self::kalkulasiPenguji($set, $get)),

                                Forms\Components\TextInput::make('skor_penguji_reputasi')
                                    ->label('Skor Reputasi Penguji (>= 2)*')
                                    ->numeric()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($set, $get) => self::kalkulasiPenguji($set, $get)),

                                Forms\Components\TextInput::make('skor_penguji_kompetensi')
                                    ->label('Skor Kompetensi Penguji (>= 27)*')
                                    ->numeric()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($set, $get) => self::kalkulasiPenguji($set, $get)),

                                Forms\Components\TextInput::make('skor_penguji_inovasi')
                                    ->label('Skor Inovasi Penguji (>= 9)*')
                                    ->numeric()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($set, $get) => self::kalkulasiPenguji($set, $get)),

                                Forms\Components\TextInput::make('status_penguji')
                                    ->label('Status Kelayakan Penguji (Otomatis)')
                                    ->readOnly()
                                    ->columnSpanFull()
                                    ->helperText('Otomatis Terpenuhi jika Integritas >= 9, Reputasi >= 2, Kompetensi >= 27, dan Inovasi >= 9.'),

                                Forms\Components\Textarea::make('catatan_penguji')
                                    ->label('Catatan & Rekomendasi Penguji')
                                    ->rows(2)
                                    ->columnSpanFull()
                                    ->placeholder('Tuliskan catatan verifikasi atau rekomendasi penguji terhadap hasil self assessment...'),
                            ])
                            ->columns(2)
                            ->columnSpan(1),
                    ])->columns(2),

                // HIDDEN SYNC FIELDS FOR BACKWARD COMPATIBILITY
                Forms\Components\Hidden::make('integritas_score'),
                Forms\Components\Hidden::make('reputasi_score'),
                Forms\Components\Hidden::make('kompetensi_score'),
                Forms\Components\Hidden::make('inovasi_score'),
                Forms\Components\Hidden::make('status_kelulusan'),

                // SECTION DETAIL SEMUA BUTIR FORM ASSESSMENT (TAB-BASED)
                Forms\Components\Section::make('Semua Nilai & Butir Kuesioner Assessment')
                    ->description('Berikut adalah rincian lengkap butir kuesioner: Di sebelah kiri menampilkan jawaban mandiri calon pengurus, dan di sebelah kanan penguji dapat memberikan skor verifikasi serta catatan.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Tabs::make('AssessmentTabs')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('Persyaratan Integritas')
                                    ->icon('heroicon-o-shield-check')
                                    ->badge('9 Butir')
                                    ->schema(self::getIntegritasSchema($integritasLabels)),

                                Forms\Components\Tabs\Tab::make('Reputasi Keuangan')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->badge('2 Butir')
                                    ->schema(self::getReputasiSchema($reputasiLabels)),

                                Forms\Components\Tabs\Tab::make('Persyaratan Kompetensi')
                                    ->icon('heroicon-o-academic-cap')
                                    ->badge('9 Butir')
                                    ->schema(self::getKompetensiSchema($kompetensiLabels)),

                                Forms\Components\Tabs\Tab::make('Kreativitas & Inovasi')
                                    ->icon('heroicon-o-light-bulb')
                                    ->badge('3 Butir')
                                    ->schema(self::getInovasiSchema($inovasiLabels)),

                                Forms\Components\Tabs\Tab::make('Klarifikasi Esai')
                                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                                    ->badge('15 Pertanyaan')
                                    ->schema(self::getKlarifikasiSchema($klarifikasiLabels)),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * Helper Schema: Integritas (9 Indikator)
     */
    protected static function getIntegritasSchema(array $labels): array
    {
        $schema = [];
        foreach ($labels as $i => $label) {
            $schema[] = Forms\Components\Fieldset::make("Butir Integritas " . ($i + 1))
                ->schema([
                    Forms\Components\Placeholder::make("pertanyaan_int_{$i}")
                        ->label("Indikator")
                        ->content(new HtmlString("<div class='font-bold text-slate-800 dark:text-slate-200 text-sm leading-snug'>{$label}</div>"))
                        ->columnSpanFull(),

                    Forms\Components\Placeholder::make("jawaban_mandiri_int_{$i}")
                        ->label("1. Penilaian Mandiri Calon Pengurus")
                        ->content(function (?SelfAssessment $record) use ($i) {
                            $item = $record?->jawaban_raw['integritas'][$i] ?? null;
                            $skor = $item['skor'] ?? null;
                            $uraian = $item['uraian'] ?? null;
                            $badge = ($skor == 1)
                                ? "<span class='inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300'>✓ Ya (Skor: 1)</span>"
                                : (($skor === 0 || $skor === '0')
                                    ? "<span class='inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300'>✗ Tidak (Skor: 0)</span>"
                                    : "<span class='text-xs text-slate-400 italic'>Belum dijawab</span>");
                            $uraianHtml = $uraian
                                ? "<div class='mt-2 p-2.5 bg-slate-50 dark:bg-slate-800 rounded-lg text-xs text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700'><b>Uraian:</b> " . e($uraian) . "</div>"
                                : "<div class='mt-1 text-xs text-slate-400 italic'>Tanpa uraian keterangan</div>";
                            return new HtmlString("<div class='p-3 bg-white dark:bg-slate-900 border rounded-xl'>{$badge}{$uraianHtml}</div>");
                        }),

                    Forms\Components\Group::make([
                        Forms\Components\Radio::make("penilaian_penguji_raw.integritas.{$i}.skor")
                            ->label("2. Penilaian / Verifikasi Penguji")
                            ->options([
                                1 => '1 - Terpenuhi (Ya)',
                                0 => '0 - Tidak Terpenuhi (Tidak)',
                            ])
                            ->inline()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, $get) => self::kalkulasiDariIndikator($set, $get)),

                        Forms\Components\TextInput::make("penilaian_penguji_raw.integritas.{$i}.catatan")
                            ->label("Catatan Penguji")
                            ->placeholder("Catatan verifikasi indikator ini..."),
                    ]),
                ])->columns(2);
        }
        return $schema;
    }

    /**
     * Helper Schema: Reputasi Keuangan (2 Indikator)
     */
    protected static function getReputasiSchema(array $labels): array
    {
        $schema = [];
        foreach ($labels as $i => $label) {
            $schema[] = Forms\Components\Fieldset::make("Butir Reputasi Keuangan " . ($i + 1))
                ->schema([
                    Forms\Components\Placeholder::make("pertanyaan_rep_{$i}")
                        ->label("Indikator")
                        ->content(new HtmlString("<div class='font-bold text-slate-800 dark:text-slate-200 text-sm leading-snug'>{$label}</div>"))
                        ->columnSpanFull(),

                    Forms\Components\Placeholder::make("jawaban_mandiri_rep_{$i}")
                        ->label("1. Penilaian Mandiri Calon Pengurus")
                        ->content(function (?SelfAssessment $record) use ($i) {
                            $item = $record?->jawaban_raw['reputasi'][$i] ?? null;
                            $skor = $item['skor'] ?? null;
                            $uraian = $item['uraian'] ?? null;
                            $badge = ($skor == 1)
                                ? "<span class='inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300'>✓ Ya (Skor: 1)</span>"
                                : (($skor === 0 || $skor === '0')
                                    ? "<span class='inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300'>✗ Tidak (Skor: 0)</span>"
                                    : "<span class='text-xs text-slate-400 italic'>Belum dijawab</span>");
                            $uraianHtml = $uraian
                                ? "<div class='mt-2 p-2.5 bg-slate-50 dark:bg-slate-800 rounded-lg text-xs text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700'><b>Uraian:</b> " . e($uraian) . "</div>"
                                : "<div class='mt-1 text-xs text-slate-400 italic'>Tanpa uraian keterangan</div>";
                            return new HtmlString("<div class='p-3 bg-white dark:bg-slate-900 border rounded-xl'>{$badge}{$uraianHtml}</div>");
                        }),

                    Forms\Components\Group::make([
                        Forms\Components\Radio::make("penilaian_penguji_raw.reputasi.{$i}.skor")
                            ->label("2. Penilaian / Verifikasi Penguji")
                            ->options([
                                1 => '1 - Terpenuhi (Ya)',
                                0 => '0 - Tidak Terpenuhi (Tidak)',
                            ])
                            ->inline()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, $get) => self::kalkulasiDariIndikator($set, $get)),

                        Forms\Components\TextInput::make("penilaian_penguji_raw.reputasi.{$i}.catatan")
                            ->label("Catatan Penguji")
                            ->placeholder("Catatan verifikasi reputasi keuangan..."),
                    ]),
                ])->columns(2);
        }
        return $schema;
    }

    /**
     * Helper Schema: Kompetensi (9 Indikator)
     */
    protected static function getKompetensiSchema(array $labels): array
    {
        $schema = [];
        $kompDesc = [
            1 => '1 (Sangat Kurang)',
            2 => '2 (Kurang)',
            3 => '3 (Cukup)',
            4 => '4 (Baik)',
        ];

        foreach ($labels as $i => $label) {
            $schema[] = Forms\Components\Fieldset::make("Butir Kompetensi " . ($i + 1))
                ->schema([
                    Forms\Components\Placeholder::make("pertanyaan_kom_{$i}")
                        ->label("Indikator")
                        ->content(new HtmlString("<div class='font-bold text-slate-800 dark:text-slate-200 text-sm leading-snug'>{$label}</div>"))
                        ->columnSpanFull(),

                    Forms\Components\Placeholder::make("jawaban_mandiri_kom_{$i}")
                        ->label("1. Penilaian Mandiri Calon Pengurus")
                        ->content(function (?SelfAssessment $record) use ($i, $kompDesc) {
                            $item = $record?->jawaban_raw['kompetensi'][$i] ?? null;
                            $skor = $item['skor'] ?? null;
                            $uraian = $item['uraian'] ?? null;
                            $desc = $kompDesc[$skor] ?? "Skor: {$skor}";
                            $badge = $skor
                                ? "<span class='inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300'>{$desc}</span>"
                                : "<span class='text-xs text-slate-400 italic'>Belum dijawab</span>";
                            $uraianHtml = $uraian
                                ? "<div class='mt-2 p-2.5 bg-slate-50 dark:bg-slate-800 rounded-lg text-xs text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700'><b>Uraian:</b> " . e($uraian) . "</div>"
                                : "<div class='mt-1 text-xs text-slate-400 italic'>Tanpa uraian keterangan</div>";
                            return new HtmlString("<div class='p-3 bg-white dark:bg-slate-900 border rounded-xl'>{$badge}{$uraianHtml}</div>");
                        }),

                    Forms\Components\Group::make([
                        Forms\Components\Radio::make("penilaian_penguji_raw.kompetensi.{$i}.skor")
                            ->label("2. Penilaian / Verifikasi Penguji")
                            ->options($kompDesc)
                            ->inline()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, $get) => self::kalkulasiDariIndikator($set, $get)),

                        Forms\Components\TextInput::make("penilaian_penguji_raw.kompetensi.{$i}.catatan")
                            ->label("Catatan Penguji")
                            ->placeholder("Catatan kompetensi peserta..."),
                    ]),
                ])->columns(2);
        }
        return $schema;
    }

    /**
     * Helper Schema: Kreativitas & Inovasi (3 Indikator)
     */
    protected static function getInovasiSchema(array $labels): array
    {
        $schema = [];
        $inoDesc = [
            1 => '1 (Kurang Sekali)',
            2 => '2 (Kurang)',
            3 => '3 (Cukup)',
            4 => '4 (Baik)',
        ];

        foreach ($labels as $i => $label) {
            $schema[] = Forms\Components\Fieldset::make("Butir Inovasi " . ($i + 1))
                ->schema([
                    Forms\Components\Placeholder::make("pertanyaan_ino_{$i}")
                        ->label("Indikator")
                        ->content(new HtmlString("<div class='font-bold text-slate-800 dark:text-slate-200 text-sm leading-snug'>{$label}</div>"))
                        ->columnSpanFull(),

                    Forms\Components\Placeholder::make("jawaban_mandiri_ino_{$i}")
                        ->label("1. Penilaian Mandiri Calon Pengurus")
                        ->content(function (?SelfAssessment $record) use ($i, $inoDesc) {
                            $item = $record?->jawaban_raw['inovasi'][$i] ?? null;
                            $skor = $item['skor'] ?? null;
                            $uraian = $item['uraian'] ?? null;
                            $desc = $inoDesc[$skor] ?? "Skor: {$skor}";
                            $badge = $skor
                                ? "<span class='inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-violet-100 text-violet-800 dark:bg-violet-900/50 dark:text-violet-300'>{$desc}</span>"
                                : "<span class='text-xs text-slate-400 italic'>Belum dijawab</span>";
                            $uraianHtml = $uraian
                                ? "<div class='mt-2 p-2.5 bg-slate-50 dark:bg-slate-800 rounded-lg text-xs text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700'><b>Uraian:</b> " . e($uraian) . "</div>"
                                : "<div class='mt-1 text-xs text-slate-400 italic'>Tanpa uraian keterangan</div>";
                            return new HtmlString("<div class='p-3 bg-white dark:bg-slate-900 border rounded-xl'>{$badge}{$uraianHtml}</div>");
                        }),

                    Forms\Components\Group::make([
                        Forms\Components\Radio::make("penilaian_penguji_raw.inovasi.{$i}.skor")
                            ->label("2. Penilaian / Verifikasi Penguji")
                            ->options($inoDesc)
                            ->inline()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, $get) => self::kalkulasiDariIndikator($set, $get)),

                        Forms\Components\TextInput::make("penilaian_penguji_raw.inovasi.{$i}.catatan")
                            ->label("Catatan Penguji")
                            ->placeholder("Catatan inovasi peserta..."),
                    ]),
                ])->columns(2);
        }
        return $schema;
    }

    /**
     * Helper Schema: Klarifikasi (15 Pertanyaan Esai)
     */
    protected static function getKlarifikasiSchema(array $labels): array
    {
        $schema = [];
        foreach ($labels as $i => $label) {
            $schema[] = Forms\Components\Fieldset::make("Klarifikasi " . ($i + 1))
                ->schema([
                    Forms\Components\Placeholder::make("pertanyaan_kla_{$i}")
                        ->label("Pertanyaan Klarifikasi Juklak")
                        ->content(new HtmlString("<div class='font-bold text-slate-800 dark:text-slate-200 text-sm leading-snug'>{$label}</div>"))
                        ->columnSpanFull(),

                    Forms\Components\Placeholder::make("jawaban_mandiri_kla_{$i}")
                        ->label("1. Jawaban Esai Calon Pengurus")
                        ->content(function (?SelfAssessment $record) use ($i) {
                            $raw = $record?->jawaban_raw['klarifikasi'][$i] ?? null;
                            if (is_array($raw)) {
                                $raw = $raw['jawaban'] ?? json_encode($raw);
                            }
                            $text = !empty($raw) ? e($raw) : "<span class='text-slate-400 italic'>Tidak ada jawaban</span>";
                            return new HtmlString("<div class='p-3.5 bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-800/40 rounded-xl text-xs leading-relaxed text-slate-800 dark:text-slate-200 whitespace-pre-wrap font-sans'>{$text}</div>");
                        }),

                    Forms\Components\Group::make([
                        Forms\Components\Select::make("penilaian_penguji_raw.klarifikasi.{$i}.status")
                            ->label("2. Status Verifikasi Klarifikasi Penguji")
                            ->options([
                                'Sesuai' => '✓ Sesuai / Memadai',
                                'Perlu Wawancara' => '⏳ Perlu Pendalaman Saat Wawancara',
                                'Diragukan' => '✗ Tidak Sesuai / Diragukan',
                            ])
                            ->default('Sesuai'),

                        Forms\Components\Textarea::make("penilaian_penguji_raw.klarifikasi.{$i}.catatan")
                            ->label("Catatan / Tanggapan Penguji")
                            ->rows(2)
                            ->placeholder("Catatan atau pertanyaan lanjutan untuk sesi wawancara..."),
                    ]),
                ])->columns(2);
        }
        return $schema;
    }

    /**
     * Hitung Ulang Total Nilai Penguji ketika Penguji mengubah radio button di tab indikator
     */
    public static function kalkulasiDariIndikator(callable $set, callable $get): void
    {
        $raw = $get('../../penilaian_penguji_raw') ?? $get('../penilaian_penguji_raw') ?? $get('penilaian_penguji_raw') ?? [];

        $integritas = 0;
        foreach ($raw['integritas'] ?? [] as $item) {
            $integritas += (int) ($item['skor'] ?? 0);
        }

        $reputasi = 0;
        foreach ($raw['reputasi'] ?? [] as $item) {
            $reputasi += (int) ($item['skor'] ?? 0);
        }

        $kompetensi = 0;
        foreach ($raw['kompetensi'] ?? [] as $item) {
            $kompetensi += (int) ($item['skor'] ?? 0);
        }

        $inovasi = 0;
        foreach ($raw['inovasi'] ?? [] as $item) {
            $inovasi += (int) ($item['skor'] ?? 0);
        }

        $isTerpenuhi = ($integritas >= 9 && $reputasi >= 2 && $kompetensi >= 27 && $inovasi >= 9);
        $status = $isTerpenuhi ? 'Terpenuhi' : 'Tidak Terpenuhi';

        // Set ke level form
        $set('../../skor_penguji_integritas', $integritas);
        $set('../../skor_penguji_reputasi', $reputasi);
        $set('../../skor_penguji_kompetensi', $kompetensi);
        $set('../../skor_penguji_inovasi', $inovasi);
        $set('../../status_penguji', $status);
        $set('../../integritas_score', $integritas);
        $set('../../reputasi_score', $reputasi);
        $set('../../kompetensi_score', $kompetensi);
        $set('../../inovasi_score', $inovasi);
        $set('../../status_kelulusan', $status);

        $set('skor_penguji_integritas', $integritas);
        $set('skor_penguji_reputasi', $reputasi);
        $set('skor_penguji_kompetensi', $kompetensi);
        $set('skor_penguji_inovasi', $inovasi);
        $set('status_penguji', $status);
        $set('integritas_score', $integritas);
        $set('reputasi_score', $reputasi);
        $set('kompetensi_score', $kompetensi);
        $set('inovasi_score', $inovasi);
        $set('status_kelulusan', $status);
    }

    /**
     * Hitung Ulang ketika Penguji mengubah TextInput skor variabel di sub-section penguji
     */
    public static function kalkulasiPenguji(callable $set, callable $get): void
    {
        $integritas = (int) $get('skor_penguji_integritas');
        $reputasi = (int) $get('skor_penguji_reputasi');
        $kompetensi = (int) $get('skor_penguji_kompetensi');
        $inovasi = (int) $get('skor_penguji_inovasi');

        $isTerpenuhi = ($integritas >= 9 && $reputasi >= 2 && $kompetensi >= 27 && $inovasi >= 9);
        $status = $isTerpenuhi ? 'Terpenuhi' : 'Tidak Terpenuhi';

        $set('status_penguji', $status);
        $set('status_kelulusan', $status);
        $set('integritas_score', $integritas);
        $set('reputasi_score', $reputasi);
        $set('kompetensi_score', $kompetensi);
        $set('inovasi_score', $inovasi);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('peserta.nama_lengkap')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Peserta'),

                Tables\Columns\TextColumn::make('peserta.koperasi.nama_koperasi')
                    ->searchable()
                    ->limit(25)
                    ->label('Koperasi'),

                Tables\Columns\TextColumn::make('skor_mandiri_total')
                    ->label('Skor Mandiri')
                    ->state(function (SelfAssessment $record): string {
                        $total = $record->skor_mandiri_integritas + $record->skor_mandiri_reputasi + $record->skor_mandiri_kompetensi + $record->skor_mandiri_inovasi;
                        return "{$total} / 59";
                    })
                    ->description(fn (SelfAssessment $record): string => $record->status_mandiri ?? '-'),

                Tables\Columns\TextColumn::make('skor_penguji_total')
                    ->label('Skor Penguji')
                    ->state(function (SelfAssessment $record): string {
                        $total = ($record->skor_penguji_integritas ?? $record->integritas_score) +
                                 ($record->skor_penguji_reputasi ?? $record->reputasi_score) +
                                 ($record->skor_penguji_kompetensi ?? $record->kompetensi_score) +
                                 ($record->skor_penguji_inovasi ?? $record->inovasi_score);
                        return "{$total} / 59";
                    })
                    ->description(fn (SelfAssessment $record): string => $record->status_penguji ?? $record->status_kelulusan ?? '-'),

                Tables\Columns\TextColumn::make('penguji.name')
                    ->label('Asesor Penilai')
                    ->placeholder('Belum Ditentukan')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status_kelulusan')
                    ->badge()
                    ->label('Hasil Akhir SA')
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
