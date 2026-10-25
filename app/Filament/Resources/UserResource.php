<?php

namespace App\Filament\Resources;

use App\Filament\Actions\ResetSandiAction;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Support\KebijakanSandi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Management User';
    protected static ?string $modelLabel = 'User';
    protected static ?string $pluralModelLabel = 'Management User';

    // Hanya admin yang boleh mengelola akun pengguna.
    public static function canViewAny(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                // Enum role sesuai tabel: admin, penguji, koperasi
                Forms\Components\Select::make('role')
                    ->label('Role Akses')
                    ->options([
                        'admin' => 'Admin',
                        'penguji' => 'Penguji',
                        'koperasi' => 'Koperasi',
                    ])
                    ->default('koperasi')
                    ->required()
                    ->live(),

                // Relasi ke Koperasi (muncul jika memilih role 'koperasi')
                Forms\Components\Select::make('koperasi_id')
                    ->label('Koperasi')
                    ->relationship('koperasi', 'nama_koperasi')
                    ->searchable()
                    ->nullable()
                    ->visible(fn (Forms\Get $get) => $get('role') === 'koperasi'),

                // Hash dilakukan otomatis oleh cast 'hashed' di model User.
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->helperText('Minimal 8 karakter, kombinasi huruf besar, huruf kecil, dan angka. Tidak boleh sama dengan username. Kosongkan saat edit jika tidak ingin mengubah.')
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->rule(KebijakanSandi::aturanDasar())
                    ->rule(fn (Forms\Get $get) => KebijakanSandi::tidakBolehSamaDenganUsername($get('username'))),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'penguji' => 'warning',
                        'koperasi' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('koperasi.nama_koperasi')
                    ->label('Koperasi')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('password_changed_at')
                    ->label('Sandi Terakhir Diganti')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Wajib ganti')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status_akun')
                    ->label('Status')
                    ->badge()
                    ->state(fn (User $record): string => $record->sedangTerkunci() ? 'Terkunci' : 'Normal')
                    ->color(fn (string $state): string => $state === 'Terkunci' ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'penguji' => 'Penguji',
                        'koperasi' => 'Koperasi',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Akun sendiri tidak ditampilkan tombol reset (ganti lewat halaman Ganti Sandi).
                ResetSandiAction::make()
                    ->visible(fn (User $record): bool => Auth::user()?->role === 'admin' && $record->id !== Auth::id()),

                Tables\Actions\Action::make('bukaKunci')
                    ->label('Buka Kunci')
                    ->icon('heroicon-o-lock-open')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => Auth::user()?->role === 'admin' && $record->sedangTerkunci())
                    ->action(function (User $record): void {
                        abort_unless(Auth::user()?->role === 'admin', 403);

                        $record->forceFill([
                            'failed_login_attempts' => 0,
                            'locked_until'          => null,
                        ])->saveQuietly();

                        Notification::make()
                            ->title('Kunci akun dibuka')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}