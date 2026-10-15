<x-filament-widgets::widget>
    <x-filament::section>
        @if(!$isAktif)
            <div class="fi-section-header bg-danger-50 text-danger-600 rounded-lg p-4 border border-danger-200 mb-4 flex items-start gap-4">
                <x-heroicon-o-exclamation-triangle class="w-8 h-8 text-danger-500 mt-1" />
                <div>
                    <h3 class="text-lg font-bold">Status Koperasi Tidak Aktif</h3>
                    <p class="text-sm mt-1">Koperasi Anda saat ini terdeteksi sebagai <b>TIDAK AKTIF</b> dalam sistem ODS. Anda <b>TIDAK DAPAT</b> mendaftarkan calon peserta UKK hingga status ini diperbaiki.<br>Silakan hubungi admin SIFITKOP / Dinas Koperasi setempat untuk melakukan pembaruan status aktif Koperasi Anda.</p>
                </div>
            </div>
        @endif

        @if(!$isLengkap)
            <div class="fi-section-header bg-warning-50 text-warning-600 rounded-lg p-4 border border-warning-200 flex items-start gap-4">
                <x-heroicon-o-document-magnifying-glass class="w-8 h-8 text-warning-500 mt-1" />
                <div>
                    <h3 class="text-lg font-bold">Profil & Dokumen Belum Lengkap</h3>
                    <p class="text-sm mt-1">Sistem mendeteksi bahwa profil Koperasi Anda belum lengkap, terutama pada bagian <b>Dokumen Persyaratan</b> (PAD, RAT, Laporan Keuangan, GoAML, NPWP).<br>Harap lengkapi semua dokumen yang diwajibkan melalui menu <a href="{{ url('/koperasi/profil-koperasi') }}" class="font-bold underline text-warning-700 hover:text-warning-800">Profil Koperasi</a> agar Anda dapat melakukan pengajuan UKK untuk peserta Anda.</p>
                </div>
            </div>
        @endif
        
        @if($isAktif && $isLengkap)
            <div class="fi-section-header bg-success-50 text-success-600 rounded-lg p-4 border border-success-200 flex items-start gap-4">
                <x-heroicon-o-check-badge class="w-8 h-8 text-success-500 mt-1" />
                <div>
                    <h3 class="text-lg font-bold">Sistem Siap Digunakan</h3>
                    <p class="text-sm mt-1">Koperasi Anda berstatus <b>Aktif</b> dan semua profil & dokumen persyaratan telah lengkap. Anda sudah bisa mulai mendaftarkan calon peserta UKK melalui menu <b>Pengajuan UKK (Peserta)</b>.</p>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
