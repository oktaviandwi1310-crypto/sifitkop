<?php

namespace App\Filament\Resources\WawancaraResource\Pages;

use App\Filament\Resources\WawancaraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWawancara extends EditRecord
{
    protected static string $resource = WawancaraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 1. Hitung Wawancara (6 Poin, skala 1-4)
        $wawancaraTotal = 0;
        for ($i = 1; $i <= 6; $i++) {
            $wawancaraTotal += (int) ($data['wawancara_' . $i] ?? 0);
        }
        $data['skor_wawancara'] = $wawancaraTotal;

        // Auto-assign penguji_id jika kosong dan penguji sedang login
        if (empty($data['penguji_id']) && \Illuminate\Support\Facades\Auth::user()->role === 'penguji') {
            $data['penguji_id'] = \Illuminate\Support\Facades\Auth::id();
        }

        // 2. Ambil Skor Admin
        $skorAdmin = $this->record->skor_admin ?? 0;
        
        // 3. Ambil Skor Self Assessment (SA) dari relasi
        $sa = \App\Models\SelfAssessment::where('peserta_id', $this->record->id)->first();
        $skorSa = $sa ? ($sa->integritas_score + $sa->reputasi_score + $sa->kompetensi_score + $sa->inovasi_score) : 0;
        
        // 4. Hitung Total Akhir
        $skorTahap1 = $skorAdmin + $skorSa;
        $totalAkhir = $skorTahap1 + $wawancaraTotal;
        $data['skor_total'] = $totalAkhir;

        // 5. Syarat Kelulusan Mutlak Juklak UKK Nomor 1 Tahun 2024:
        // - Admin + SA (Tahap 1) minimal 64 (Terpenuhi)
        // - Wawancara minimal 18 (Terpenuhi) -> SYARAT GUGUR: Jika < 18 TETAP TIDAK LULUS walau total >= 82
        // - Total Akhir minimal 82 (LULUS)
        $lulusTahap1 = $skorTahap1 >= 64;
        $lulusWawancara = $wawancaraTotal >= 18;
        $lulusTotal = $totalAkhir >= 82;

        // SYARAT GUGUR: Wawancara < 18 (jelek) = MUTLAK TIDAK LULUS
        if ($lulusTahap1 && $lulusWawancara && $lulusTotal) {
            $data['status_kelulusan'] = 'LULUS';
        } else {
            $data['status_kelulusan'] = 'TIDAK LULUS';
        }

        return $data;
    }

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        $status = $this->record->status_kelulusan;
        $skorWawancara = $this->record->skor_wawancara;
        $skorTotal = $this->record->skor_total;
        
        $msg = "Skor Wawancara: {$skorWawancara}/24. Total Skor: {$skorTotal}/100. Keputusan Akhir: {$status}.";
        if ($skorWawancara < 18) {
            $msg .= " (Peserta dinyatakan TIDAK LULUS karena nilai wawancara belum mencapai standar minimal 18).";
        }

        return \Filament\Notifications\Notification::make()
            ->title('Nilai Wawancara Berhasil Disimpan')
            ->body($msg)
            ->color($status === 'LULUS' ? 'success' : 'danger');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
