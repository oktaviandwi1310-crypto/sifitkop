@php($pengguna = auth()->user())

@if ($pengguna && $pengguna->perluPeringatanSandi())
    <div style="background:#fffbeb;border:1px solid #fcd34d;color:#92400e;border-radius:0.5rem;padding:0.75rem 1rem;margin-bottom:1rem;font-size:0.875rem;">
        Kata sandi Anda akan kedaluwarsa dalam <strong>{{ $pengguna->sisaHariSandi() }} hari</strong>.
        <a href="{{ filament()->getProfileUrl() }}" style="text-decoration:underline;font-weight:600;">Ganti sekarang</a>
    </div>
@endif