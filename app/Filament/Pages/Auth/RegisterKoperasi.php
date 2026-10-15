<?php

namespace App\Filament\Pages\Auth;

use App\Models\Koperasi;
use App\Models\User;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

use Filament\Support\Enums\MaxWidth;

class RegisterKoperasi extends BaseRegister
{
    public function getMaxWidth(): MaxWidth | string | null
    {
        return MaxWidth::FourExtraLarge;
    }

    public function getHeading(): string | Htmlable
    {
        return 'Registrasi Akun Koperasi';
    }

    public function getSubheading(): string | Htmlable
    {
        return new HtmlString('Daftarkan akun koperasi Anda dengan memasukkan <strong>Nomor Induk Koperasi (NIK)</strong>.');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Cari Data Koperasi')
                    ->description('Masukkan Nomor Induk Koperasi (NIK) yang terdaftar untuk menarik data otomatis.')
                    ->schema([
                        TextInput::make('nik')
                            ->label('Nomor Induk Koperasi (NIK)')
                            ->placeholder('Contoh: 3301010020001')
                            ->required()
                            ->autofocus()
                            ->live(onBlur: true)
                            ->suffixAction(
                                FormAction::make('cekNik')
                                    ->icon('heroicon-m-magnifying-glass')
                                    ->tooltip('Cari Data Koperasi')
                                    ->action(function (Get $get, Set $set, ?string $state) {
                                        $this->searchKoperasi($state, $set, $get);
                                    })
                            )
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                $this->searchKoperasi($state, $set, $get);
                            })
                            ->helperText('Tekan Enter, klik ikon cari, atau pindah kolom untuk mengambil data koperasi.')
                            ->columnSpanFull(),

                        Hidden::make('is_new_koperasi')->default(false),
                        
                        Hidden::make('koperasi_id')
                            ->required(fn (Get $get) => ! $get('is_new_koperasi'))
                            ->validationMessages([
                                'required' => 'Data Koperasi belum dipilih atau NIK tidak valid. Pastikan NIK terdaftar.',
                            ]),

                        Placeholder::make('koperasi_preview')
                            ->label('Informasi Koperasi')
                            ->visible(fn (Get $get) => filled($get('koperasi_id')) && ! $get('is_new_koperasi'))
                            ->columnSpanFull()
                            ->content(function (Get $get) {
                                $koperasi = \App\Models\Koperasi::find($get('koperasi_id'));
                                if (! $koperasi) return '';

                                $nama = e($koperasi->nama_koperasi);
                                $badanHukum = e($koperasi->badan_hukum ?? '-');
                                $kabKota = e($koperasi->kabupaten_kota ?? '-');
                                $alamat = e($koperasi->alamat ?? '-');
                                $status = e($koperasi->status_ods ?? 'Aktif');

                                return new HtmlString("
                                    <div class='p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl space-y-2'>
                                        <div class='flex items-center justify-between'>
                                            <span class='font-bold text-emerald-900 dark:text-emerald-200 text-base'>{$nama}</span>
                                            <span class='px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200'>{$status}</span>
                                        </div>
                                        <div class='text-xs text-slate-600 dark:text-slate-300 space-y-1'>
                                            <div><strong>Badan Hukum:</strong> {$badanHukum}</div>
                                            <div><strong>Wilayah:</strong> {$kabKota}</div>
                                            <div><strong>Alamat:</strong> {$alamat}</div>
                                        </div>
                                    </div>
                                ");
                            }),

                        TextInput::make('nama_koperasi')
                            ->label('Nama Koperasi')
                            ->placeholder('Contoh: Koperasi Serba Usaha Sejahtera')
                            ->required(fn (Get $get) => $get('is_new_koperasi'))
                            ->visible(fn (Get $get) => $get('is_new_koperasi'))
                            ->columnSpanFull(),

                        TextInput::make('badan_hukum')
                            ->label('Nomor Badan Hukum (Opsional)')
                            ->placeholder('Contoh: 1234/BH/1990')
                            ->visible(fn (Get $get) => $get('is_new_koperasi')),

                        TextInput::make('kabupaten_kota')
                            ->label('Kabupaten / Kota (Opsional)')
                            ->placeholder('Contoh: KOTA SEMARANG')
                            ->visible(fn (Get $get) => $get('is_new_koperasi')),

                        Textarea::make('alamat')
                            ->label('Alamat Lengkap (Opsional)')
                            ->rows(2)
                            ->visible(fn (Get $get) => $get('is_new_koperasi'))
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Kredensial Akun Pengurus')
                    ->description('Lengkapi data akun untuk login ke Portal Koperasi SIFITKOP.')
                    ->columns(2)
                    ->schema([
                        $this->getNameFormComponent()
                            ->label('Nama PIC / Pengurus')
                            ->placeholder('Nama Lengkap Penanggung Jawab Koperasi')
                            ->columnSpanFull(),
                        
                        TextInput::make('username')
                            ->label('Username Login')
                            ->placeholder('Contoh: koperasi_sejahtera')
                            ->required()
                            ->alphaDash()
                            ->unique($this->getUserModel(), 'username')
                            ->maxLength(50)
                            ->helperText('Huruf, angka, strip (-) atau underscore (_).'),

                        $this->getEmailFormComponent()
                            ->label('Email Koperasi / PIC')
                            ->placeholder('email@koperasi.com'),

                        $this->getPasswordFormComponent()
                            ->label('Kata Sandi'),

                        $this->getPasswordConfirmationFormComponent()
                            ->label('Konfirmasi Kata Sandi'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function searchKoperasi(?string $nik, Set $set, Get $get): void
    {
        $nik = trim($nik ?? '');

        if (empty($nik)) {
            $this->resetKoperasiData($set);
            return;
        }

        $koperasi = Koperasi::where('nik', $nik)->first();

        if (! $koperasi) {
            $this->resetKoperasiData($set);
            $set('is_new_koperasi', true);

            Notification::make()
                ->title('NIK Belum Terdaftar')
                ->body("Silakan isi data koperasi Anda secara manual di bawah ini.")
                ->info()
                ->send();

            return;
        }

        // Cek apakah koperasi sudah memiliki user akun
        $existingUser = User::where('koperasi_id', $koperasi->id)->first();
        if ($existingUser) {
            $this->resetKoperasiData($set);

            Notification::make()
                ->title('Koperasi Sudah Memiliki Akun')
                ->body("Koperasi '{$koperasi->nama_koperasi}' sudah terdaftar dengan username '{$existingUser->username}'. Silakan langsung login.")
                ->warning()
                ->persistent()
                ->send();

            return;
        }

        // Set data koperasi ke form
        $set('koperasi_id', $koperasi->id);
        $set('is_new_koperasi', false);
        $set('nama_koperasi', $koperasi->nama_koperasi);
        $set('badan_hukum', $koperasi->badan_hukum);
        $set('kabupaten_kota', $koperasi->kabupaten_kota);
        $set('alamat', $koperasi->alamat);
        $set('status_ods', $koperasi->status_ods ?? 'Aktif');

        // Otomatis isi nama jika ada pengurus (Ketua)
        if (! empty($koperasi->susunan_pengurus) && is_array($koperasi->susunan_pengurus)) {
            foreach ($koperasi->susunan_pengurus as $pengurus) {
                if (isset($pengurus['jabatan']) && strtolower($pengurus['jabatan']) === 'ketua' && ! empty($pengurus['nama'])) {
                    if (empty($get('name'))) {
                        $set('name', $pengurus['nama']);
                    }
                    break;
                }
            }
        }

        Notification::make()
            ->title('Data Koperasi Ditemukan!')
            ->body("Koperasi '{$koperasi->nama_koperasi}' berhasil diverifikasi. Silakan lengkapi akun pengurus.")
            ->success()
            ->send();
    }

    protected function resetKoperasiData(Set $set): void
    {
        $set('koperasi_id', null);
        $set('is_new_koperasi', false);
        $set('nama_koperasi', null);
        $set('badan_hukum', null);
        $set('kabupaten_kota', null);
        $set('alamat', null);
        $set('status_ods', null);
    }

    protected function handleRegistration(array $data): Model
    {
        $koperasiId = $data['koperasi_id'] ?? null;
        $isNewKoperasi = $data['is_new_koperasi'] ?? false;

        if ($isNewKoperasi) {
            // Cek pencegahan ganda (race condition)
            $koperasi = Koperasi::where('nik', $data['nik'])->first();
            if ($koperasi) {
                throw ValidationException::withMessages([
                    'data.nik' => 'Koperasi dengan NIK ini sudah terdaftar oleh pengguna lain secara bersamaan.',
                ]);
            }

            // Buat Koperasi baru
            $koperasi = Koperasi::create([
                'nik' => $data['nik'],
                'nama_koperasi' => $data['nama_koperasi'],
                'badan_hukum' => $data['badan_hukum'] ?? null,
                'kabupaten_kota' => $data['kabupaten_kota'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'status_ods' => 'Aktif (Baru)',
            ]);
        } else {
            if (! $koperasiId) {
                throw ValidationException::withMessages([
                    'data.nik' => 'Silakan masukkan NIK Koperasi yang valid terlebih dahulu.',
                ]);
            }

            $koperasi = Koperasi::find($koperasiId);
            if (! $koperasi) {
                throw ValidationException::withMessages([
                    'data.nik' => 'Data Koperasi tidak valid.',
                ]);
            }

            if (User::where('koperasi_id', $koperasi->id)->exists()) {
                throw ValidationException::withMessages([
                    'data.nik' => 'Koperasi ini sudah memiliki akun terdaftar.',
                ]);
            }
        }

        // Buat user untuk Koperasi
        return User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'koperasi',
            'koperasi_id' => $koperasi->id,
        ]);
    }
}
