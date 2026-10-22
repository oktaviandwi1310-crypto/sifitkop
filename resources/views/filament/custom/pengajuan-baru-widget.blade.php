<x-filament-widgets::widget>
    <x-filament::section
        heading="Pengajuan Peserta UKK Baru"
        description="Peserta yang belum diverifikasi, dikelompokkan per koperasi."
        icon="heroicon-o-bell-alert"
        icon-color="warning"
    >
        <div class="divide-y divide-gray-200 dark:divide-white/10">
            @foreach ($items as $koperasi)
                <div class="flex items-center justify-between gap-4 py-3">
                    <div>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ $koperasi->nama_koperasi }} mengajukan {{ $koperasi->pesertas_pending_count }} peserta UKK
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            NIK Koperasi: {{ $koperasi->nik }}
                        </p>
                    </div>

                    <x-filament::button
                        tag="a"
                        size="sm"
                        :href="\App\Filament\Resources\KoperasiResource::getUrl('edit', ['record' => $koperasi->id])"
                    >
                        Lihat Detail Koperasi
                    </x-filament::button>
                </div>
            @endforeach
        </div>

        @if ($total > $items->count())
            <p class="pt-3 text-xs text-gray-500 dark:text-gray-400">
                dan {{ $total - $items->count() }} koperasi lainnya
            </p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>