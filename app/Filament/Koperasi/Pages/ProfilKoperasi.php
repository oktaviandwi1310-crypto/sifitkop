<?php

namespace App\Filament\Koperasi\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms;
use App\Models\Koperasi;
use Illuminate\Support\Facades\Auth;

class ProfilKoperasi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Profil Koperasi';
    protected static ?string $title = 'Lengkapi Master Data Koperasi';
    protected static ?string $navigationGroup = 'Menu Utama';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.koperasi.pages.profil-koperasi';

    public ?array $data = [];

    public function mount(): void
    {
        $koperasi = Auth::user()->koperasi;
        if ($koperasi) {
            // Auto-tarik data pengurus dan pengawas dari API ODS jika masih kosong
            if (empty($koperasi->susunan_pengurus) && empty($koperasi->susunan_pengawas) && !empty($koperasi->nik)) {
                try {
                    $odsService = new \App\Services\OdsApiService();
                    $result = $odsService->getDetail($koperasi->nik);
                    if ($result['success'] && isset($result['data'][0])) {
                        $odsData = $result['data'][0];
                        $pengurusList = $odsData['Pengurus'] ?? [];
                        
                        $susunanPengurus = [];
                        $susunanPengawas = [];
                        
                        foreach ($pengurusList as $p) {
                            $nama = $p['Nama'] ?? '';
                            $jabatanRaw = strtolower($p['IdJabatan'] ?? '');
                            $statusKepengurusan = strtolower($p['StatusKepengurusan'] ?? '');
                            
                            if ($statusKepengurusan !== 'aktif') continue; // Hanya ambil yang aktif
                            
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
                        
                        if (!empty($susunanPengurus) || !empty($susunanPengawas)) {
                            $koperasi->update([
                                'susunan_pengurus' => empty($susunanPengurus) ? null : $susunanPengurus,
                                'susunan_pengawas' => empty($susunanPengawas) ? null : $susunanPengawas,
                            ]);
                            $koperasi->refresh(); // Update the model instance with saved data
                        }
                    }
                } catch (\Exception $e) {
                    // Abaikan error api, biarkan form kosong agar bisa diisi manual
                }
            }

            $this->form->fill($koperasi->toArray());
        } else {
            $this->form->fill([]);
        }
    }

    public function form(Form $form): Form
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
                                    ->label('NIK Koperasi')
                                    ->disabled(fn () => Auth::user()->koperasi !== null), // NIK hanya bisa diisi jika data baru
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
                                Forms\Components\Select::make('klasifikasi_koperasi')
                                    ->options([
                                        'Primer Kabupaten/Kota' => 'Primer Kabupaten/Kota',
                                        'Primer Provinsi' => 'Primer Provinsi',
                                        'Primer Nasional' => 'Primer Nasional',
                                        'Sekunder Kabupaten/Kota' => 'Sekunder Kabupaten/Kota',
                                        'Sekunder Provinsi' => 'Sekunder Provinsi',
                                        'Sekunder Nasional' => 'Sekunder Nasional',
                                        'Koperasi Primer Provinsi' => 'Koperasi Primer Provinsi',
                                        'Koperasi Sekunder Provinsi' => 'Koperasi Sekunder Provinsi',
                                        'Lainnya' => 'Lainnya',
                                    ])
                                    ->label('Jenis Koperasi')
                                    ->disabled(fn () => !empty(Auth::user()->koperasi?->klasifikasi_koperasi) || Auth::user()->koperasi?->status_ods !== 'Aktif (Baru)')
                                    ->dehydrated()
                                    ->helperText(fn () => (!empty(Auth::user()->koperasi?->klasifikasi_koperasi) || Auth::user()->koperasi?->status_ods !== 'Aktif (Baru)') ? 'Jenis koperasi ditarik langsung dari ODS dan tidak dapat diubah.' : ''),
                                Forms\Components\Textarea::make('alamat')
                                    ->columnSpanFull(),
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
            ])
            ->statePath('data');
    }

    public function save()
    {
        $user = Auth::user();
        $koperasi = $user->koperasi;
        
        if ($koperasi) {
            $koperasi->update($this->form->getState());
        } else {
            $koperasi = \App\Models\Koperasi::create($this->form->getState());
            $user->update(['koperasi_id' => $koperasi->id]);
        }
            
        \Filament\Notifications\Notification::make()
            // ->title('Profil Koperasi berhasil disimpan')
            ->success()
            ->send();
    }
}
