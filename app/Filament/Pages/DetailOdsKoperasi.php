<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Services\OdsApiService;
use Livewire\Attributes\Url;
use Filament\Notifications\Notification;

class DetailOdsKoperasi extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = 'Detail Koperasi ODS';
    
    // Sembunyikan dari menu sidebar
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.detail-ods-koperasi';

    #[Url]
    public $kabupaten = '';

    #[Url]
    public $bentuk = '';

    #[Url]
    public $group = '';

    public $search = '';
    public int $limit = 50;
    public $isLoaded = false;
    
    public $selectedKoperasi = null;

    public function mount()
    {
        if (empty($this->kabupaten) || empty($this->bentuk)) {
            return redirect()->to('/admin/rekap-ods-koperasi');
        }
    }

    public function loadData()
    {
        $this->isLoaded = true;
    }

    public function loadMore()
    {
        $this->limit += 50;
    }

    public function updatedSearch()
    {
        $this->limit = 50;
    }

    public function openDetail($nik)
    {
        $list = $this->getBaseFilteredData();
        $this->selectedKoperasi = collect($list)->firstWhere('NIK', $nik);
        $this->dispatch('open-modal', id: 'detail-koperasi-modal');
    }

    protected function getBaseFilteredData()
    {
        ini_set('memory_limit', '-1');
        $ods = new OdsApiService();
        $response = $ods->getProfile();

        if ($response['success']) {
            $allData = $response['data'];
            
            $filtered = [];
            foreach ($allData as $item) {
                if (stripos($item['Kabupaten'] ?? '', $this->kabupaten) !== false) {
                    $match = false;
                    $groupField = $this->group ? str_replace(' ', '_', $this->group) : null;
                    
                    if ($groupField && isset($item[$groupField])) {
                        if (stripos($item[$groupField] ?? '', $this->bentuk) !== false) {
                            $match = true;
                        }
                    } else {
                        // Fallback
                        if (stripos($item['Bentuk_Koperasi'] ?? '', $this->bentuk) !== false) $match = true;
                        elseif (stripos($item['Kelompok_Koperasi'] ?? '', $this->bentuk) !== false) $match = true;
                        elseif (stripos($item['Sektor_Usaha'] ?? '', $this->bentuk) !== false) $match = true;
                        elseif (stripos($item['Jenis_Koperasi'] ?? '', $this->bentuk) !== false) $match = true;
                    }
                    
                    if ($match) {
                        $filtered[] = $item;
                    }
                }
            }
            return $filtered;
        }
        return [];
    }

    #[\Livewire\Attributes\Computed]
    public function getFilteredListProperty()
    {
        $list = $this->getBaseFilteredData();

        if (!empty($this->search)) {
            $list = array_filter($list, function($item) {
                $search = strtolower($this->search);
                return str_contains(strtolower($item['Nama_Koperasi'] ?? ''), $search) ||
                       str_contains(strtolower($item['NIK'] ?? ''), $search);
            });
        }

        return $list;
    }

    #[\Livewire\Attributes\Computed]
    public function getPaginatedListProperty()
    {
        return collect($this->filteredList)->take($this->limit)->values();
    }
}
