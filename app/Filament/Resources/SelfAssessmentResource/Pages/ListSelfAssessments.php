<?php

namespace App\Filament\Resources\SelfAssessmentResource\Pages;

use App\Filament\Resources\SelfAssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSelfAssessments extends ListRecords
{
    protected static string $resource = SelfAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getSubheading(): ?\Illuminate\Contracts\Support\Htmlable
    {
        return new \Illuminate\Support\HtmlString('
            <div class="mt-2 p-4 bg-blue-50/90 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/60 rounded-2xl text-xs text-slate-700 dark:text-slate-300 flex items-start gap-3 shadow-sm">
                <div class="text-blue-600 dark:text-blue-400 text-lg mt-0.5 shrink-0">ℹ️</div>
                <div class="space-y-1">
                    <div class="font-bold text-blue-900 dark:text-blue-200 text-sm">Keterangan Perhitungan Skor (Juklak UKK 2024):</div>
                    <div class="text-slate-600 dark:text-slate-300 leading-relaxed">
                        • <strong>Skor Maksimal Kuesioner Self Assessment adalah 59 Poin</strong> (Integritas: 9, Reputasi: 2, Kompetensi: 36, Inovasi: 12) dengan batas minimal terpenuhi adalah <strong>47 Poin</strong>.<br>
                        • <strong>Target Kelulusan Tahap 1 (&ge; 64 Poin)</strong> diperoleh setelah Skor Self Assessment digabungkan dengan <strong>Skor Verifikasi Dokumen Admin (Maks. 17 Poin)</strong> pada menu Peserta UKK.
                    </div>
                </div>
            </div>
        ');
    }

    public function getFooter(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.custom.sticky-header-css');
    }
}
