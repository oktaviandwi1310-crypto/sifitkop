<?php
// app/Filament/Resources/KoperasiPesertaResource/RelationManagers/PesertasRelationManager.php

namespace App\Filament\Resources\KoperasiPesertaResource\RelationManagers;

use App\Filament\Resources\PesertaResource;
use App\Models\Peserta;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PesertasRelationManager extends RelationManager
{
    protected static string $relationship = 'pesertas';

    protected static ?string $title = 'Daftar Peserta UKK';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_lengkap')
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama Peserta')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jabatan_usulan')
                    ->label('Usulan')
                    ->badge(),
                Tables\Columns\TextColumn::make('status_verifikasi')
                    ->label('Verifikasi')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'Pending' => 'Belum Verifikasi',
                        default => (string) $state,
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Revisi' => 'info',
                        'Terverifikasi' => 'success',
                        'Ditolak' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status_sa')
                    ->label('Status SA')
                    ->badge()
                    ->state(fn (Peserta $record) => \App\Models\SelfAssessment::where('peserta_id', $record->id)->exists() ? 'Selesai' : 'Belum')
                    ->color(fn ($state) => $state === 'Selesai' ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('jadwal_wawancara')
                    ->label('Jadwal Wawancara')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('skor_wawancara')
                    ->label('Skor Wawancara')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_kelulusan')
                    ->label('Hasil Akhir')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'LULUS' => 'success',
                        'TIDAK LULUS' => 'danger',
                        default => 'gray',
                    }),
            ])
            // Klik baris / nama -> detail peserta (halaman edit PesertaResource yang sudah ada)
            ->recordUrl(fn (Peserta $record) => PesertaResource::getUrl('edit', ['record' => $record]))
            ->actions([
                Tables\Actions\Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn (Peserta $record) => PesertaResource::getUrl('edit', ['record' => $record])),

                Tables\Actions\Action::make('cetak_ba')
                    ->label('Cetak BA')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->action(function (Peserta $record) {
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ba', [
                            'koperasi' => $record->koperasi,
                            'daftar_peserta' => Peserta::where('koperasi_id', $record->koperasi_id)->get(),
                            'tim_penguji' => \App\Models\Penguji::whereIn('id', $record->penguji_id ?? [])->get(),
                            'tanggal_pleno' => now(),
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "BA_UKK_{$record->nik_peserta}.pdf"
                        );
                    }),
            ]);
    }
}