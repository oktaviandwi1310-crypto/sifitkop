<x-filament-panels::page>
    <div class="space-y-4">
        <x-filament::card>
            {{ $this->form }}
        </x-filament::card>

        <!-- Stats Overview -->
        <div class="flex flex-col lg:flex-row gap-4 w-full">
            <div class="flex-1">
                <x-filament::card class="relative overflow-hidden w-full h-full">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-primary-500/10 rounded-xl">
                            <x-heroicon-o-building-office class="w-8 h-8 text-primary-500" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Keseluruhan</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->summary['total'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </x-filament::card>
            </div>

            <div class="flex-1">
                <x-filament::card class="relative overflow-hidden w-full h-full">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-success-500/10 rounded-xl">
                            <x-heroicon-o-check-circle class="w-8 h-8 text-success-500" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Aktif</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->summary['aktif'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </x-filament::card>
            </div>

            <div class="flex-1">
                <x-filament::card class="relative overflow-hidden w-full h-full">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-danger-500/10 rounded-xl">
                            <x-heroicon-o-x-circle class="w-8 h-8 text-danger-500" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tidak Aktif</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->summary['tidakAktif'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </x-filament::card>
            </div>

            <div class="flex-1">
                <x-filament::card class="relative overflow-hidden w-full h-full">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-warning-500/10 rounded-xl">
                            <x-heroicon-o-exclamation-triangle class="w-8 h-8 text-warning-500" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Dalam Pengawasan</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">-</p>
                            <!-- <p class="text-xs text-warning-600 dark:text-warning-400 mt-1">Tidak disuplai API</p> -->
                        </div>
                    </div>
                </x-filament::card>
            </div>
        </div>

        <x-filament::card>
            <div class="relative overflow-x-auto overflow-y-auto rounded-xl" style="max-height: 75vh;">
                <!-- Loading Overlay -->
                <div wire:loading.flex class="absolute inset-0 z-10 items-center justify-center bg-white/70 dark:bg-gray-900/70 backdrop-blur-sm rounded-xl">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-primary-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Mensinkronkan Data ODS (Harap Tunggu)...</span>
                    </div>
                </div>

                <table class="w-full text-left divide-y divide-gray-200 dark:divide-white/5 table-auto">
                    <thead class="shadow-sm" style="position: sticky; top: 0; z-index: 20;">
                        <tr class="bg-gray-100 dark:bg-gray-800">
                            <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 w-16 text-center">No.</th>
                            <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Kabupaten/Kota</th>
                            <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Kategori Koperasi</th>
                            <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Jml Koperasi</th>
                            <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Aktif</th>
                            <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Tidak Aktif</th>
                            <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-right">Total Aset</th>
                            <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody wire:key="tbody-{{ $this->filterData['tahun'] ?? '2026' }}-{{ $this->filterData['kabupaten'] ?? 'all' }}-{{ $this->filterData['kelompok'] ?? 'all' }}-{{ $this->limit }}" class="divide-y divide-gray-200 dark:divide-white/5">
                        @forelse($this->paginatedKoperasiList as $index => $item)
                            <tr wire:key="row-{{ $this->filterData['tahun'] ?? '2026' }}-{{ $item['Kabupaten'] ?? 'x' }}-{{ $item['DetailRekap'] ?? 'x' }}-{{ $loop->index }}" class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                <td class="px-4 py-3 text-sm text-center text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-bold">{{ $item['Kabupaten'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-block px-2 py-0.5 mb-1 text-xs font-medium bg-gray-100 text-gray-600 rounded dark:bg-gray-700 dark:text-gray-300">{{ $item['GroupRekap'] ?? '-' }}</span><br>
                                    <span class="font-semibold">{{ $item['DetailRekap'] ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; padding: 0.125rem 0.625rem; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; background-color: #eff6ff; color: #1d4ed8; box-shadow: inset 0 0 0 1px rgba(37,99,235,0.2);">
                                        {{ $item['jumlahKoperasi'] ?? '0' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; padding: 0.125rem 0.625rem; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; background-color: #f0fdf4; color: #15803d; box-shadow: inset 0 0 0 1px rgba(22,163,74,0.2);">
                                        {{ $item['jumlahKoperasiAktif'] ?? '0' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; padding: 0.125rem 0.625rem; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; background-color: #fef2f2; color: #b91c1c; box-shadow: inset 0 0 0 1px rgba(220,38,38,0.2);">
                                        {{ $item['jumlahKoperasiTidakAktif'] ?? '0' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-right whitespace-nowrap">
                                    Rp {{ number_format((float)($item['jumlahAset'] ?? 0), 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <a href="{!! \App\Filament\Pages\DetailOdsKoperasi::getUrl(['kabupaten' => $item['Kabupaten'] ?? '', 'bentuk' => $item['DetailRekap'] ?? '', 'group' => $item['GroupRekap'] ?? '']) !!}" class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-semibold transition-colors duration-200 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:bg-white/5 dark:border-white/10 dark:hover:bg-white/10 dark:text-white">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">
                                    <div wire:loading.remove>Belum ada data dari API atau data kosong pada tahun ini.</div>
                                    <div wire:loading wire:target="fetchData">Memuat data *live* dari server ODS Kemenkop...</div>
                                </td>
                            </tr>
                        @endforelse

                        @if(count($this->filteredKoperasiList) > $this->limit)
                            <tr x-data x-intersect="$wire.loadMore()">
                                <td colspan="8" class="px-4 py-4 text-center text-sm text-gray-500 font-medium">
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
                    @if(count($this->filteredKoperasiList) > 0)
                        @php
                            $sumJmlKoperasi = collect($this->filteredKoperasiList)->sum('jumlahKoperasi');
                            $sumAktif = collect($this->filteredKoperasiList)->sum('jumlahKoperasiAktif');
                            $sumTidakAktif = collect($this->filteredKoperasiList)->sum('jumlahKoperasiTidakAktif');
                            $sumAset = collect($this->filteredKoperasiList)->sum('jumlahAset');
                        @endphp
                        <tfoot class="bg-gray-200 dark:bg-gray-800 font-bold shadow-[0_-1px_2px_rgba(0,0,0,0.05)] border-t border-gray-300 dark:border-white/10" style="position: sticky; bottom: 0; z-index: 20;">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-sm text-right">TOTAL KESELURUHAN</td>
                                <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; padding: 0.125rem 0.625rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.375rem; background-color: #eff6ff; color: #1d4ed8; box-shadow: inset 0 0 0 1px rgba(37,99,235,0.2);">
                                        {{ $sumJmlKoperasi }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; padding: 0.125rem 0.625rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.375rem; background-color: #f0fdf4; color: #15803d; box-shadow: inset 0 0 0 1px rgba(22,163,74,0.2);">
                                        {{ $sumAktif }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; padding: 0.125rem 0.625rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.375rem; background-color: #fef2f2; color: #b91c1c; box-shadow: inset 0 0 0 1px rgba(220,38,38,0.2);">
                                        {{ $sumTidakAktif }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-right whitespace-nowrap">
                                    Rp {{ number_format((float)($sumAset ?? 0), 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>
