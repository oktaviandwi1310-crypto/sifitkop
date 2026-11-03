<?php

namespace App\Filament\Resources\KoperasiWawancaraResource\RelationManagers;

use App\Filament\Resources\KoperasiWawancaraResource;
use App\Filament\Resources\WawancaraResource;
use App\Models\Peserta;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WawancarasRelationManager extends RelationManager
{
    protected static string $relationship = 'pesertas';

    protected static ?string $title = 'Daftar Peserta Wawancara';

    // Supaya tombol aksi (Beri / Edit Nilai, Berita Acara, dll.) tetap aktif di halaman view
    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        // Memakai tabel WawancaraResource apa adanya (kolom dan tombol sama persis)
        return WawancaraResource::table($table)
            ->heading('Daftar Peserta Wawancara')
            ->modifyQueryUsing(fn (Builder $query) => KoperasiWawancaraResource::scopePeserta($query))
            ->recordUrl(fn (Peserta $record) => WawancaraResource::getUrl('edit', ['record' => $record]));
    }
}