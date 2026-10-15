<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Services\OdsApiService;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Actions\Action;

class RekapOdsKoperasi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cloud';
    protected static ?string $navigationLabel = 'Daftar Koperasi (Live ODS)';
    protected static ?string $title = 'Data Rekap Koperasi (Live ODS)';
    protected static ?string $navigationGroup = 'Integrasi ODS';

    protected static string $view = 'filament.pages.rekap-ods-koperasi';

    public int $limit = 50;
    
    public ?array $filterData = [
        'tahun' => '2026',
        'kabupaten' => '',
        'kelompok' => ''
    ];

    public function mount()
    {
        $this->form->fill($this->filterData);
    }

    public function loadMore()
    {
        $this->limit += 50;
    }

    public function updatedFilterData()
    {
        $this->limit = 50; // Reset limit
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(12)->schema([
                    Select::make('tahun')
                        ->label('Tahun Rekap')
                        ->options([
                            '2023' => '2023',
                            '2024' => '2024',
                            '2025' => '2025',
                            '2026' => '2026',
                        ])
                        ->default('2026')
                        ->live()
                        ->columnSpan(['default' => 12, 'md' => 3]),
                        
                    Select::make('kabupaten')
                        ->label('Filter Kabupaten/Kota')
                        ->placeholder('Cari Kabupaten...')
                        ->searchable()
                        ->options(function () {
                            $list = $this->getKoperasiData();
                            $kabupatens = [];
                            foreach ($list as $item) {
                                $kab = $item['Kabupaten'] ?? '';
                                if ($kab) {
                                    $kabupatens[$kab] = $kab;
                                }
                            }
                            ksort($kabupatens);
                            return $kabupatens;
                        })
                        ->live()
                        ->columnSpan(['default' => 12, 'md' => 4]),
                        
                    Select::make('kelompok')
                        ->label('Filter Bentuk Koperasi')
                        ->placeholder('Pilih Bentuk Koperasi')
                        ->options([
                            'Primer Kabupaten/Kota' => 'Primer Kabupaten/Kota',
                            'Primer Nasional' => 'Primer Nasional',
                            'Primer Provinsi' => 'Primer Provinsi',
                            'Sekunder Kabupaten/Kota' => 'Sekunder Kabupaten/Kota',
                        ])
                        ->live()
                        ->columnSpan(['default' => 12, 'md' => 5]),
                ])
            ])
            ->statePath('filterData');
    }

    protected function getKoperasiData()
    {
        $tahun = $this->filterData['tahun'] ?? '2026';
        $cacheKey = 'rekap_ods_data_' . $tahun;
        
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 600, function () use ($tahun) {
            $ods = new OdsApiService();
            $response = $ods->getRekap($tahun);
            $profileResponse = $ods->getProfile();

            if ($response['success']) {
                $data = $response['data'];
                $data = isset($data['data']) ? $data['data'] : $data;
            } else {
                $data = []; // Fallback empty
            }
                
            $actualCounts = [];
            if (isset($profileResponse['success']) && $profileResponse['success']) {
                $pData = $profileResponse['data'];
                $pData = isset($pData['data']) ? $pData['data'] : $pData;
                foreach ($pData as $prof) {
                    $prov = strtoupper(trim(preg_replace('/^\d{2}\s*-\s*/', '', $prof['Provinsi'] ?? 'JAWA TENGAH')));
                    $kab = strtoupper(trim($prof['Kabupaten'] ?? ''));
                    // Hapus prefix angka agar seragam: "33.01 - KAB. CILACAP" -> "KAB. CILACAP"
                    $kabClean = trim(preg_replace('/^\d{2}\.\d{2}\s*-\s*/', '', $kab));
                    
                    $bentuk = strtoupper(trim($prof['Bentuk_Koperasi'] ?? ''));
                    if ($bentuk) $actualCounts[$kabClean]['BENTUK KOPERASI'][$bentuk] = ($actualCounts[$kabClean]['BENTUK KOPERASI'][$bentuk] ?? 0) + 1;
                    
                    $jenis = strtoupper(trim($prof['Jenis_Koperasi'] ?? ''));
                    if ($jenis) $actualCounts[$kabClean]['JENIS KOPERASI'][$jenis] = ($actualCounts[$kabClean]['JENIS KOPERASI'][$jenis] ?? 0) + 1;
                    
                    $kelompok = strtoupper(trim($prof['Kelompok_Koperasi'] ?? ''));
                    if ($kelompok) $actualCounts[$kabClean]['KELOMPOK KOPERASI'][$kelompok] = ($actualCounts[$kabClean]['KELOMPOK KOPERASI'][$kelompok] ?? 0) + 1;
                    
                    $sektor = strtoupper(trim($prof['Sektor_Usaha'] ?? ''));
                    if ($sektor) $actualCounts[$kabClean]['SEKTOR USAHA'][$sektor] = ($actualCounts[$kabClean]['SEKTOR USAHA'][$sektor] ?? 0) + 1;

                    $actualCounts[$kabClean]['_PROVINSI'] = $prov;
                    $actualCounts[$kabClean]['_KAB_ORIGINAL'] = $kab; // Simpan nama aslinya
                }
            }

            $finalData = [];
            
            // Loop semua grup yang kita ekstrak dari Profil
            foreach ($actualCounts as $kabClean => $groups) {
                $prov = $groups['_PROVINSI'] ?? 'JAWA TENGAH';
                $kabOriginal = $groups['_KAB_ORIGINAL'] ?? $kabClean;
                
                foreach (['BENTUK KOPERASI'] as $groupName) {
                    if (isset($groups[$groupName])) {
                        foreach ($groups[$groupName] as $detail => $countAktif) {
                            
                            // Cari apakah API Rekap Kemenkop punya data 'Tidak Aktif' untuk kombinasi ini
                            $tidakAktif = 0;
                            if (!empty($data)) {
                                foreach ($data as $apiItem) {
                                    $apiKab = trim(preg_replace('/^\d{2}\.\d{2}\s*-\s*/', '', strtoupper(trim($apiItem['Kabupaten'] ?? ''))));
                                    $apiGroup = strtoupper(trim($apiItem['GroupRekap'] ?? ''));
                                    $apiDetail = strtoupper(trim($apiItem['DetailRekap'] ?? ''));
                                    
                                    if ($apiKab === $kabClean && $apiGroup === $groupName && $apiDetail === $detail) {
                                        $tidakAktif = (int)($apiItem['jumlahKoperasiTidakAktif'] ?? 0);
                                        break;
                                    }
                                }
                            }
                            
                            $finalData[] = [
                                'Provinsi' => $prov,
                                'Kabupaten' => $kabClean,
                                'GroupRekap' => ucwords(strtolower($groupName)),
                                'DetailRekap' => ucwords(strtolower($detail)),
                                'jumlahKoperasiAktif' => $countAktif,
                                'jumlahKoperasiTidakAktif' => $tidakAktif,
                                'jumlahKoperasi' => $countAktif + $tidakAktif
                            ];
                        }
                    }
                }
            }
            
            // Urutkan berdasarkan Grup Kategori, lalu Detailnya (Bentuk), baru Kabupatennya
            return collect($finalData)->sortBy([
                ['GroupRekap', 'asc'],
                ['DetailRekap', 'asc'],
                ['Kabupaten', 'asc'],
            ])->values()->toArray();
        });
    }

    #[\Livewire\Attributes\Computed]
    public function getFilteredKoperasiListProperty()
    {
        $list = $this->getKoperasiData();
        $kabupaten = $this->filterData['kabupaten'] ?? '';
        $kelompok = $this->filterData['kelompok'] ?? '';

        if (!empty($kabupaten)) {
            $list = array_filter($list, function($item) use ($kabupaten) {
                return stripos($item['Kabupaten'] ?? '', $kabupaten) !== false;
            });
        }

        if (!empty($kelompok)) {
            $list = array_filter($list, function($item) use ($kelompok) {
                return stripos($item['DetailRekap'] ?? '', $kelompok) !== false;
            });
        }

        return $list;
    }

    public function getSummaryProperty()
    {
        $list = $this->filteredKoperasiList;
        $total = 0;
        $aktif = 0;
        $tidakAktif = 0;

        foreach ($list as $item) {
            $total += (int)($item['jumlahKoperasi'] ?? 0);
            $aktif += (int)($item['jumlahKoperasiAktif'] ?? 0);
            $tidakAktif += (int)($item['jumlahKoperasiTidakAktif'] ?? 0);
        }

        return [
            'total' => $total,
            'aktif' => $aktif,
            'tidakAktif' => $tidakAktif,
        ];
    }

    #[\Livewire\Attributes\Computed]
    public function getPaginatedKoperasiListProperty()
    {
        $filtered = collect($this->filteredKoperasiList);
        return $filtered->take($this->limit)->values();
    }
}
