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
                    ->description('Skala penilaian 1 (Kurang Sekali) hingga 4 (Sangat Baik). Batas kelulusan wawancara adalah >= 18. Syarat Gugur: Jika skor wawancara < 18, peserta tetap TIDAK LULUS walaupun nilai lain lulus.')
                    ->schema([
                        Forms\Components\Select::make('wawancara_1')
                            ->label('1. Pengetahuan Kondisi Koperasi')
                            ->options([1=>'1 (Kurang Sekali)',2=>'2 (Kurang)',3=>'3 (Cukup)',4=>'4 (Baik)'])
                            ->required()
                            ->live()
                            ->native(false),
                        Forms\Components\Select::make('wawancara_2')
                            ->label('2. Pemahaman Manajemen Keuangan Koperasi')
                            ->options([1=>'1 (Kurang Sekali)',2=>'2 (Kurang)',3=>'3 (Cukup)',4=>'4 (Baik)'])
                            ->required()
                            ->live()
                            ->native(false),
                        Forms\Components\Select::make('wawancara_3')
                            ->label('3. Kecakapan dan Keahlian Bidang Perkoperasian')
                            ->options([1=>'1 (Kurang Sekali)',2=>'2 (Kurang)',3=>'3 (Cukup)',4=>'4 (Baik)'])
                            ->required()
                            ->live()
                            ->native(false),
                        Forms\Components\Select::make('wawancara_4')
                            ->label('4. Kecakapan dan Keahlian Membangun Relasi')
                            ->options([1=>'1 (Kurang Sekali)',2=>'2 (Kurang)',3=>'3 (Cukup)',4=>'4 (Baik)'])
                            ->required()
                            ->live()
                            ->native(false),
                        Forms\Components\Select::make('wawancara_5')
                            ->label('5. Kemampuan Menyusun Strategi Bisnis')
                            ->options([1=>'1 (Kurang Sekali)',2=>'2 (Kurang)',3=>'3 (Cukup)',4=>'4 (Baik)'])
                            ->required()
                            ->live()
                            ->native(false),
                        Forms\Components\Select::make('wawancara_6')
                            ->label('6. Kemampuan Membangun Keunggulan & Kesinambungan')
                            ->options([1=>'1 (Kurang Sekali)',2=>'2 (Kurang)',3=>'3 (Cukup)',4=>'4 (Baik)'])
                            ->required()
                            ->live()
                            ->native(false),

                        Forms\Components\Placeholder::make('kalkulasi_kelulusan_live')
                            ->label('Hasil & Status Kelulusan Wawancara (Juklak UKK 2024)')
                            ->columnSpanFull()
                            ->content(function ($get, ?Peserta $record) {
                                $w1 = (int) ($get('wawancara_1') ?? 0);
                                $w2 = (int) ($get('wawancara_2') ?? 0);
                                $w3 = (int) ($get('wawancara_3') ?? 0);
                                $w4 = (int) ($get('wawancara_4') ?? 0);
                                $w5 = (int) ($get('wawancara_5') ?? 0);
                                $w6 = (int) ($get('wawancara_6') ?? 0);
                                
                                $totalWawancara = $w1 + $w2 + $w3 + $w4 + $w5 + $w6;
                                
                                $skorAdmin = $record?->skor_admin ?? 0;
                                $sa = $record ? \App\Models\SelfAssessment::where('peserta_id', $record->id)->first() : null;
                                $skorSa = $sa ? ($sa->integritas_score + $sa->reputasi_score + $sa->kompetensi_score + $sa->inovasi_score) : 0;
                                $skorTahap1 = $skorAdmin + $skorSa;
                                $grandTotal = $skorTahap1 + $totalWawancara;
                                
                                $lulusTahap1 = $skorTahap1 >= 64;
                                $lulusWawancara = $totalWawancara >= 18;
                                $lulusTotal = $grandTotal >= 82;
                                
                                $isLulus = $lulusTahap1 && $lulusWawancara && $lulusTotal;
                                
                                $wawancaraBadge = $totalWawancara >= 18 
                                    ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">✓ TERPENUHI (&ge; 18)</span>'
                                    : ($totalWawancara > 0 
                                        ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">✗ TIDAK TERPENUHI (&lt; 18) — SYARAT GUGUR</span>' 
                                        : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">Belum Dinilai</span>');
                                        
                                $statusAkhirBadge = $isLulus
                                    ? '<span class="inline-flex items-center px-4 py-1.5 rounded-xl text-sm font-black bg-emerald-600 text-white shadow-sm">✓ LULUS UKK</span>'
                                    : '<span class="inline-flex items-center px-4 py-1.5 rounded-xl text-sm font-black bg-rose-600 text-white shadow-sm">✗ TIDAK LULUS</span>';
                                    
                                $alasanGugur = '';
                                if (!$isLulus && $totalWawancara > 0) {
                                    if (!$lulusWawancara) {
                                        $alasanGugur = '<div class="text-xs font-bold text-rose-600 dark:text-rose-400 mt-2">⚠️ SYARAT GUGUR: Skor Wawancara (' . $totalWawancara . '/24) di bawah 18 (Jelek/Kurang). Peserta TIDAK LULUS walaupun nilai administrasi tinggi atau total kumulatif mencapai target.</div>';
                                    } elseif (!$lulusTahap1) {
                                        $alasanGugur = '<div class="text-xs font-bold text-rose-600 dark:text-rose-400 mt-2">⚠️ Catatan: Skor Dokumen & Self Assessment (' . $skorTahap1 . '/76) belum mencapai batas minimal 64.</div>';
                                    } elseif (!$lulusTotal) {
                                        $alasanGugur = '<div class="text-xs font-bold text-rose-600 dark:text-rose-400 mt-2">⚠️ Catatan: Total Skor Kumulatif (' . $grandTotal . '/100) belum mencapai batas minimum kelulusan 82.</div>';
                                    }
                                }
                                
                                return new \Illuminate\Support\HtmlString('
                                    <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 dark:border-slate-800 dark:bg-slate-900/50 space-y-3">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <div class="bg-white dark:bg-slate-800 p-3 rounded-lg border border-slate-100 dark:border-slate-700">
                                                <div class="text-xs font-semibold text-slate-500 uppercase">1. Skor Tahap 1 (Admin + SA)</div>
                                                <div class="text-xl font-black text-slate-800 dark:text-slate-100 mt-1">' . $skorTahap1 . ' <span class="text-xs text-slate-400 font-normal">/ 76 (Min: 64)</span></div>
                                                <div class="mt-1 text-xs font-medium text-slate-500">' . ($lulusTahap1 ? '<span class="text-emerald-600 font-bold">✓ Terpenuhi</span>' : '<span class="text-rose-600 font-bold">✗ Belum Terpenuhi</span>') . '</div>
                                            </div>
                                            <div class="bg-white dark:bg-slate-800 p-3 rounded-lg border border-slate-100 dark:border-slate-700">
                                                <div class="text-xs font-semibold text-slate-500 uppercase">2. Skor Wawancara (6 Poin)</div>
                                                <div class="text-xl font-black text-slate-800 dark:text-slate-100 mt-1">' . $totalWawancara . ' <span class="text-xs text-slate-400 font-normal">/ 24 (Min: 18)</span></div>
                                                <div class="mt-1">' . $wawancaraBadge . '</div>
                                            </div>
                                            <div class="bg-white dark:bg-slate-800 p-3 rounded-lg border border-slate-100 dark:border-slate-700">
                                                <div class="text-xs font-semibold text-slate-500 uppercase">3. Total Skor Kumulatif</div>
                                                <div class="text-xl font-black text-slate-800 dark:text-slate-100 mt-1">' . $grandTotal . ' <span class="text-xs text-slate-400 font-normal">/ 100 (Min: 82)</span></div>
                                                <div class="mt-1 text-xs font-medium text-slate-500">Ambang Kelulusan: &ge; 82</div>
                                            </div>
                                        </div>
                                        <div class="pt-2 border-t border-slate-200 dark:border-slate-700 flex flex-col md:flex-row md:items-center justify-between gap-2">
                                            <div>' . $alasanGugur . '</div>
                                            <div class="shrink-0">' . $statusAkhirBadge . '</div>
                                        </div>
                                    </div>
                                ');
                            }),
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
                    ->placeholder('Belum Ditugaskan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('skor_wawancara')
                    ->label('Skor Wawancara')
                    ->badge()
                    ->state(function ($record) {
                        return $record->wawancara_1 + $record->wawancara_2 + $record->wawancara_3 + $record->wawancara_4 + $record->wawancara_5 + $record->wawancara_6;
                    })
                    ->color(fn ($state) => $state >= 18 ? 'success' : ($state == 0 ? 'gray' : 'danger'))
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'Belum Dinilai' : "{$state} / 24 (" . ($state >= 18 ? 'Terpenuhi' : 'Gugur') . ")"),
                Tables\Columns\TextColumn::make('skor_total')
                    ->label('Total Kumulatif')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? "{$state} / 100" : '-'),
                Tables\Columns\TextColumn::make('status_kelulusan')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'LULUS' => 'success',
                        'TIDAK LULUS' => 'danger',
                        default => 'gray',
                    })
                    ->description(function ($record) {
                        $wTotal = $record->wawancara_1 + $record->wawancara_2 + $record->wawancara_3 + $record->wawancara_4 + $record->wawancara_5 + $record->wawancara_6;
                        if ($record->status_kelulusan === 'TIDAK LULUS' && $wTotal > 0 && $wTotal < 18) {
                            return 'Gugur: Nilai Wawancara < 18';
                        }
                        return null;
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
                    
                Tables\Actions\EditAction::make()
                    ->label('Beri / Edit Nilai')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary'),

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
    // Jika login sebagai penguji, tampilkan yang di-assign ke dia atau yang belum di-assign
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('status_verifikasi', 'Terverifikasi');
        
        if (Auth::user()->role === 'penguji') {
            $query->where(function ($q) {
                $q->where('penguji_id', Auth::user()->id)
                  ->orWhereNull('penguji_id');
            });
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
