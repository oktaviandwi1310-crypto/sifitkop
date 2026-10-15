<x-filament-panels::page>
    <div class="space-y-4" wire:init="loadData">
        
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xl font-bold tracking-tight">
                {{ $kabupaten }} - {{ $bentuk }}
            </h2>
            <x-filament::button color="gray" tag="a" href="{{ url('/admin/rekap-ods-koperasi') }}">
                Kembali
            </x-filament::button>
        </div>

        @if(!$isLoaded)
            <x-filament::card>
                <div class="flex items-center justify-center p-10 space-x-3 text-primary-600">
                    <x-filament::loading-indicator class="h-8 w-8" />
                    <span class="font-medium text-lg">Mengambil dan Menganalisis 35.000+ data (Mohon tunggu)...</span>
                </div>
            </x-filament::card>
        @else
            <!-- Filter Section -->
            <x-filament::card>
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div class="w-full md:w-1/2">
                        <label class="block text-sm font-medium mb-1">Cari Koperasi</label>
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="Cari NIK atau Nama Koperasi..."
                            />
                        </x-filament::input.wrapper>
                    </div>
                    <div class="text-sm text-warning-600 dark:text-warning-400 bg-warning-50 dark:bg-warning-500/10 px-4 py-2 rounded-lg border border-warning-200 dark:border-warning-700 flex items-center space-x-2">
                        <x-heroicon-s-information-circle class="w-5 h-5 flex-shrink-0" />
                        <span><strong>Catatan API:</strong> Data rinci yang dikirimkan oleh API ODS Kemenkop Pusat hanya memuat koperasi yang berstatus <strong>Aktif</strong>. Data Koperasi "Tidak Aktif" hanya tercatat sebagai angka pada tabel Rekap Statistik.</span>
                    </div>
                </div>
            </x-filament::card>

            <!-- Data Table -->
            <x-filament::card>
                <div class="overflow-x-auto overflow-y-auto rounded-xl" style="max-height: 70vh;">
                    <table class="w-full text-left divide-y divide-gray-200 dark:divide-white/5 table-auto relative">
                        <thead class="bg-gray-50 dark:bg-gray-800 shadow-sm" style="position: sticky; top: 0; z-index: 20;">
                            <tr>
                                <th class="px-4 py-3 text-sm font-medium">No.</th>
                                <th class="px-4 py-3 text-sm font-medium">NIK</th>
                                <th class="px-4 py-3 text-sm font-medium">Nama Koperasi</th>
                                <th class="px-4 py-3 text-sm font-medium">Badan Hukum</th>
                                <th class="px-4 py-3 text-sm font-medium">Alamat Lengkap</th>
                                <th class="px-4 py-3 text-sm font-medium">Status</th>
                                <th class="px-4 py-3 text-sm font-medium text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            @forelse($this->paginatedList as $index => $det)
                                <tr wire:key="row-{{ $det['NIK'] ?? $index }}" class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                    <td class="px-4 py-2 text-xs text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 font-mono text-xs">{{ $det['NIK'] ?? '-' }}</td>
                                    <td class="px-4 py-2 font-bold text-sm">{{ $det['Nama_Koperasi'] ?? '-' }}</td>
                                    <td class="px-4 py-2 text-xs">{{ $det['Nomor_Badan_Hukum_Pendirian'] ?? '-' }}</td>
                                    <td class="px-4 py-2 text-xs">{{ $det['Alamat'] ?? '-' }}, {{ $det['Desa'] ?? '-' }}, {{ $det['Kecamatan'] ?? '-' }}</td>
                                    <td class="px-4 py-2 text-xs whitespace-nowrap">
                                        <x-filament::badge color="{{ strtolower($det['StatusKoperasi'] ?? '') === 'aktif' ? 'success' : 'warning' }}">
                                            {{ $det['StatusKoperasi'] ?? '-' }}
                                        </x-filament::badge>
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <button wire:click="openDetail('{{ $det['NIK'] ?? '' }}')" class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors border border-primary-200 dark:bg-primary-500/10 dark:text-primary-400 dark:border-primary-500/20 dark:hover:bg-primary-500/20">
                                            Lihat
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        Data tidak ditemukan.
                                    </td>
                                </tr>
                            @endforelse

                            @if(count($this->filteredList) > $this->limit)
                                <tr x-data x-intersect="$wire.loadMore()">
                                    <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500 font-medium">
                                        <div wire:loading wire:target="loadMore" class="flex items-center justify-center space-x-2">
                                            <svg class="w-5 h-5 text-primary-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span>Memuat data selanjutnya...</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        
                        @if(count($this->filteredList) > 0)
                            <tfoot class="bg-gray-100 dark:bg-gray-800 font-bold shadow-[0_-1px_2px_rgba(0,0,0,0.05)] border-t border-gray-300 dark:border-white/10" style="position: sticky; bottom: 0; z-index: 20;">
                                <tr>
                                    <td colspan="6" class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 text-center">
                                        Total Data Koperasi: {{ count($this->filteredList) }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </x-filament::card>
        @endif
    </div>

    <!-- Modal Detail Koperasi Menggunakan Native Filament Modal -->
    <x-filament::modal id="detail-koperasi-modal" width="4xl" alignment="center">
        <x-slot name="heading">
            Detail Profil Koperasi
        </x-slot>

        @if($selectedKoperasi)
            <div class="overflow-y-auto pr-2" style="max-height: 70vh;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    
                    <!-- Grup 1: Identitas -->
                    <div class="space-y-4">
                        <h4 class="font-bold text-primary-600 dark:text-primary-400 border-b border-gray-200 dark:border-gray-700 pb-2">Identitas Koperasi</h4>
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div class="text-gray-500">Nama Koperasi</div>
                            <div class="col-span-2 font-semibold">{{ $selectedKoperasi['Nama_Koperasi'] ?? '-' }}</div>
                            
                            <div class="text-gray-500">NIK</div>
                            <div class="col-span-2">{{ $selectedKoperasi['NIK'] ?? '-' }}</div>
                            
                            <div class="text-gray-500">Bentuk</div>
                            <div class="col-span-2">{{ $selectedKoperasi['Bentuk_Koperasi'] ?? '-' }}</div>
                            
                            <div class="text-gray-500">Jenis</div>
                            <div class="col-span-2">{{ $selectedKoperasi['Jenis_Koperasi'] ?? '-' }}</div>
                            
                            <div class="text-gray-500">Kelompok</div>
                            <div class="col-span-2">{{ $selectedKoperasi['Kelompok_Koperasi'] ?? '-' }}</div>
                            
                            <div class="text-gray-500">Sektor Usaha</div>
                            <div class="col-span-2">{{ $selectedKoperasi['Sektor_Usaha'] ?? '-' }}</div>
                        </div>
                    </div>

                    <!-- Grup 2: Legalitas -->
                    <div class="space-y-4">
                        <h4 class="font-bold text-primary-600 dark:text-primary-400 border-b border-gray-200 dark:border-gray-700 pb-2">Legalitas & Status</h4>
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div class="text-gray-500">Status</div>
                            <div class="col-span-2 font-bold {{ strtolower($selectedKoperasi['StatusKoperasi'] ?? '') === 'aktif' ? 'text-success-600' : 'text-warning-600' }}">
                                {{ $selectedKoperasi['StatusKoperasi'] ?? '-' }}
                            </div>
                            
                            <div class="text-gray-500">NPWP</div>
                            <div class="col-span-2">{{ $selectedKoperasi['NPWP'] ?? '-' }}</div>
                            
                            <div class="text-gray-500">No. Badan Hukum</div>
                            <div class="col-span-2">{{ $selectedKoperasi['Nomor_Badan_Hukum_Pendirian'] ?? '-' }}</div>
                            
                            <div class="text-gray-500">Tgl. Badan Hukum</div>
                            <div class="col-span-2">{{ $selectedKoperasi['Tanggal_Badan_Hukum_Pendirian'] ?? '-' }}</div>
                            
                            <div class="text-gray-500">Sertifikat</div>
                            <div class="col-span-2">{{ $selectedKoperasi['Status_Sertifikat'] ?? '-' }} ({{ $selectedKoperasi['Tanggal_Berlaku_Sertifikat'] ?? '-' }})</div>
                            
                            <div class="text-gray-500">Grade / KUK</div>
                            <div class="col-span-2">{{ $selectedKoperasi['Grade'] ?? '-' }} / {{ $selectedKoperasi['KUK'] ?? '-' }}</div>
                        </div>
                    </div>

                    <!-- Grup 3: Alamat & Manajemen -->
                    <div class="col-span-1 md:col-span-2 space-y-4">
                        <h4 class="font-bold text-primary-600 dark:text-primary-400 border-b border-gray-200 dark:border-gray-700 pb-2">Lokasi & Manajemen</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <div class="text-gray-500 mb-1">Alamat Lengkap</div>
                                <div class="font-medium">
                                    {{ $selectedKoperasi['Alamat'] ?? '-' }}<br>
                                    Kel. {{ $selectedKoperasi['Kelurahan'] ?? $selectedKoperasi['Desa'] ?? '-' }}, 
                                    Kec. {{ $selectedKoperasi['Kecamatan'] ?? '-' }}<br>
                                    {{ $selectedKoperasi['Kabupaten'] ?? '-' }}, 
                                    {{ $selectedKoperasi['Provinsi'] ?? '-' }} {{ $selectedKoperasi['Kodepos'] ?? '' }}
                                </div>
                            </div>
                            <div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="text-gray-500">Pola Pengelolaan</div>
                                    <div class="col-span-2 font-medium">{{ $selectedKoperasi['Pola_Pengelolaan'] ?? '-' }}</div>

                                    <div class="text-gray-500">No. Perubahan AD</div>
                                    <div class="col-span-2">{{ $selectedKoperasi['Nomor_Perubahan_Anggaran_Dasar'] ?? '-' }}</div>

                                    <div class="text-gray-500">Tgl. Perubahan AD</div>
                                    <div class="col-span-2">{{ $selectedKoperasi['Tanggal_Perubahan_Anggaran_Dasar'] ?? '-' }}</div>

                                    <div class="text-gray-500">RAT Terakhir</div>
                                    <div class="col-span-2 font-medium">{{ $selectedKoperasi['Tanggal_RAT_Terakhir'] ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endif

        <x-slot name="footer">
            <div class="flex justify-end w-full">
                <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'detail-koperasi-modal' })">
                    Tutup
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal>
</x-filament-panels::page>
