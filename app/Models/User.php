<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'koperasi_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'password'            => 'hashed',
            'password_changed_at' => 'datetime',
            'locked_until'        => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Setiap kali sandi berubah (registrasi, ganti sandi, reset), catat waktunya.
        // Kecuali password_changed_at sengaja diisi manual (dipakai reset admin).
        static::saving(function (User $user) {
            if ($user->isDirty('password') && ! $user->isDirty('password_changed_at')) {
                $user->password_changed_at = now();
            }
        });
    }

    public function koperasi()
    {
        return $this->belongsTo(Koperasi::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return in_array($this->role, ['admin', 'penguji']);
        }

        if ($panel->getId() === 'koperasi') {
            return $this->role === 'koperasi';
        }

        return false;
    }

    // ---------- 1.3 Masa berlaku sandi ----------

    public function sandiKedaluwarsaPada(): ?\Illuminate\Support\Carbon
    {
        return $this->password_changed_at
            ?->copy()
            ->addDays(config('keamanan.sandi.masa_berlaku_hari'));
    }

    /** Sisa hari sebelum kedaluwarsa (0 atau negatif = sudah habis). */
    public function sisaHariSandi(): int
    {
        $batas = $this->sandiKedaluwarsaPada();

        return $batas ? (int) ceil(now()->diffInDays($batas, false)) : 0;
    }

    /** password_changed_at kosong dianggap kedaluwarsa (mis. akun hasil seeder). */
    public function sandiKedaluwarsa(): bool
    {
        return $this->password_changed_at === null || $this->sisaHariSandi() <= 0;
    }

    public function perluPeringatanSandi(): bool
    {
        return ! $this->sandiKedaluwarsa()
            && $this->sisaHariSandi() <= config('keamanan.sandi.peringatan_hari');
    }

    // ---------- 1.4 Penguncian akun ----------

    public function sedangTerkunci(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

    public function sisaMenitKunci(): int
    {
        if (! $this->sedangTerkunci()) {
            return 0;
        }

        return max(1, (int) ceil(now()->diffInSeconds($this->locked_until, false) / 60));
    }

    public function catatLoginGagal(): void
    {
        $percobaan = $this->failed_login_attempts;

        // Masa kunci sudah lewat: mulai hitung dari awal.
        if ($this->locked_until !== null && $this->locked_until->isPast()) {
            $percobaan = 0;
            $this->locked_until = null;
        }

        $percobaan++;

        if ($percobaan >= config('keamanan.login.maks_percobaan')) {
            $this->locked_until = now()->addMinutes(config('keamanan.login.kunci_menit'));
            $percobaan = 0;
        }

        $this->failed_login_attempts = $percobaan;
        $this->saveQuietly();
    }

    public function resetPercobaanLogin(): void
    {
        if ($this->failed_login_attempts > 0 || $this->locked_until !== null) {
            $this->forceFill(['failed_login_attempts' => 0, 'locked_until' => null])->saveQuietly();
        }
    }
}