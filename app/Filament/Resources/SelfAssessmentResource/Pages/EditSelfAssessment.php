<?php

namespace App\Filament\Resources\SelfAssessmentResource\Pages;

use App\Filament\Resources\SelfAssessmentResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSelfAssessment extends EditRecord
{
    protected static string $resource = SelfAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('salin_mandiri')
                ->label('Salin Nilai Mandiri ke Penguji')
                ->icon('heroicon-o-document-duplicate')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Salin Skor Mandiri ke Penilaian Penguji?')
                ->modalDescription('Tindakan ini akan mengisi skor penguji (Integritas, Reputasi, Kompetensi, Inovasi, serta butir indikator) dengan nilai yang diisi oleh calon pengurus sebagai dasar verifikasi.')
                ->action(function () {
                    $record = $this->getRecord();
                    $integritas = $record->skor_mandiri_integritas ?? $record->integritas_score ?? 0;
                    $reputasi = $record->skor_mandiri_reputasi ?? $record->reputasi_score ?? 0;
                    $kompetensi = $record->skor_mandiri_kompetensi ?? $record->kompetensi_score ?? 0;
                    $inovasi = $record->skor_mandiri_inovasi ?? $record->inovasi_score ?? 0;
                    $status = $record->status_mandiri ?? $record->status_kelulusan ?? 'Terpenuhi';

                    $this->data['skor_penguji_integritas'] = $integritas;
                    $this->data['skor_penguji_reputasi'] = $reputasi;
                    $this->data['skor_penguji_kompetensi'] = $kompetensi;
                    $this->data['skor_penguji_inovasi'] = $inovasi;
                    $this->data['status_penguji'] = $status;
                    $this->data['integritas_score'] = $integritas;
                    $this->data['reputasi_score'] = $reputasi;
                    $this->data['kompetensi_score'] = $kompetensi;
                    $this->data['inovasi_score'] = $inovasi;
                    $this->data['status_kelulusan'] = $status;

                    if (!empty($record->jawaban_raw)) {
                        $raw = $record->jawaban_raw;
                        $penilaianPenguji = [
                            'integritas' => [],
                            'reputasi' => [],
                            'kompetensi' => [],
                            'inovasi' => [],
                            'klarifikasi' => [],
                        ];

                        foreach ($raw['integritas'] ?? [] as $idx => $item) {
                            $penilaianPenguji['integritas'][$idx] = [
                                'skor' => (int) ($item['skor'] ?? 1),
                                'catatan' => $this->data['penilaian_penguji_raw']['integritas'][$idx]['catatan'] ?? '',
                            ];
                        }

                        foreach ($raw['reputasi'] ?? [] as $idx => $item) {
                            $penilaianPenguji['reputasi'][$idx] = [
                                'skor' => (int) ($item['skor'] ?? 1),
                                'catatan' => $this->data['penilaian_penguji_raw']['reputasi'][$idx]['catatan'] ?? '',
                            ];
                        }

                        foreach ($raw['kompetensi'] ?? [] as $idx => $item) {
                            $penilaianPenguji['kompetensi'][$idx] = [
                                'skor' => (int) ($item['skor'] ?? 4),
                                'catatan' => $this->data['penilaian_penguji_raw']['kompetensi'][$idx]['catatan'] ?? '',
                            ];
                        }

                        foreach ($raw['inovasi'] ?? [] as $idx => $item) {
                            $penilaianPenguji['inovasi'][$idx] = [
                                'skor' => (int) ($item['skor'] ?? 3),
                                'catatan' => $this->data['penilaian_penguji_raw']['inovasi'][$idx]['catatan'] ?? '',
                            ];
                        }

                        foreach ($raw['klarifikasi'] ?? [] as $idx => $item) {
                            $penilaianPenguji['klarifikasi'][$idx] = [
                                'status' => $this->data['penilaian_penguji_raw']['klarifikasi'][$idx]['status'] ?? 'Sesuai',
                                'catatan' => $this->data['penilaian_penguji_raw']['klarifikasi'][$idx]['catatan'] ?? '',
                            ];
                        }

                        $this->data['penilaian_penguji_raw'] = $penilaianPenguji;
                    }

                    $this->form->fill($this->data);

                    Notification::make()
                        ->title('Berhasil Menyalin Nilai Mandiri')
                        ->body('Seluruh skor dan butir indikator mandiri telah disalin ke penilaian penguji.')
                        ->success()
                        ->send();
                }),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // 1. Pastikan nilai mandiri terisi jika masih kosong (migrasi data lama)
        if (empty($data['skor_mandiri_integritas']) && !empty($data['integritas_score'])) {
            $data['skor_mandiri_integritas'] = $data['integritas_score'];
            $data['skor_mandiri_reputasi'] = $data['reputasi_score'];
            $data['skor_mandiri_kompetensi'] = $data['kompetensi_score'];
            $data['skor_mandiri_inovasi'] = $data['inovasi_score'];
            $data['status_mandiri'] = $data['status_kelulusan'];
        }

        // 2. Default nilai penguji jika belum diisi
        if ($data['skor_penguji_integritas'] === null) {
            $data['skor_penguji_integritas'] = $data['skor_mandiri_integritas'] ?? $data['integritas_score'] ?? 0;
            $data['skor_penguji_reputasi'] = $data['skor_mandiri_reputasi'] ?? $data['reputasi_score'] ?? 0;
            $data['skor_penguji_kompetensi'] = $data['skor_mandiri_kompetensi'] ?? $data['kompetensi_score'] ?? 0;
            $data['skor_penguji_inovasi'] = $data['skor_mandiri_inovasi'] ?? $data['inovasi_score'] ?? 0;
            $data['status_penguji'] = $data['status_mandiri'] ?? $data['status_kelulusan'] ?? 'Terpenuhi';
        }

        // 3. Default penguji penilai (user login atau asesor default)
        if (empty($data['penguji_id'])) {
              $data['penguji_id'] = auth()->id() ?? 1;
            // $data['penguji_id'] = auth()->id() ?? 1;
        }

        // 4. Inisialisasi struktur penilaian_penguji_raw jika masih kosong
        if (empty($data['penilaian_penguji_raw']) && !empty($data['jawaban_raw'])) {
            $raw = $data['jawaban_raw'];
            $penilaianPenguji = [
                'integritas' => [],
                'reputasi' => [],
                'kompetensi' => [],
                'inovasi' => [],
                'klarifikasi' => [],
            ];

            foreach ($raw['integritas'] ?? [] as $idx => $item) {
                $penilaianPenguji['integritas'][$idx] = [
                    'skor' => (int) ($item['skor'] ?? 1),
                    'catatan' => '',
                ];
            }

            foreach ($raw['reputasi'] ?? [] as $idx => $item) {
                $penilaianPenguji['reputasi'][$idx] = [
                    'skor' => (int) ($item['skor'] ?? 1),
                    'catatan' => '',
                ];
            }

            foreach ($raw['kompetensi'] ?? [] as $idx => $item) {
                $penilaianPenguji['kompetensi'][$idx] = [
                    'skor' => (int) ($item['skor'] ?? 4),
                    'catatan' => '',
                ];
            }

            foreach ($raw['inovasi'] ?? [] as $idx => $item) {
                $penilaianPenguji['inovasi'][$idx] = [
                    'skor' => (int) ($item['skor'] ?? 3),
                    'catatan' => '',
                ];
            }

            foreach ($raw['klarifikasi'] ?? [] as $idx => $item) {
                $penilaianPenguji['klarifikasi'][$idx] = [
                    'status' => 'Sesuai',
                    'catatan' => '',
                ];
            }

            $data['penilaian_penguji_raw'] = $penilaianPenguji;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hitung ulang status kelayakan dari skor penguji
        $integritas = (int) ($data['skor_penguji_integritas'] ?? 0);
        $reputasi = (int) ($data['skor_penguji_reputasi'] ?? 0);
        $kompetensi = (int) ($data['skor_penguji_kompetensi'] ?? 0);
        $inovasi = (int) ($data['skor_penguji_inovasi'] ?? 0);

        $isTerpenuhi = ($integritas >= 9 && $reputasi >= 2 && $kompetensi >= 27 && $inovasi >= 9);
        $status = $isTerpenuhi ? 'Terpenuhi' : 'Tidak Terpenuhi';
        $data['status_penguji'] = $status;

        // Sinkronisasi dengan skor resmi sistem agar modul wawancara & rekapitulasi tetap akurat
        $data['integritas_score'] = $integritas;
        $data['reputasi_score'] = $reputasi;
        $data['kompetensi_score'] = $kompetensi;
        $data['inovasi_score'] = $inovasi;
        $data['status_kelulusan'] = $status;

        return $data;
    }
}
