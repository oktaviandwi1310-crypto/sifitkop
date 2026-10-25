<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WajibGantiSandi
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->sandiKedaluwarsa()) {
            return $next($request);
        }

        // Halaman ganti sandi dan logout harus tetap bisa diakses (cegah loop).
        if ($request->routeIs('filament.*.auth.profile', 'filament.*.auth.logout')) {
            return $next($request);
        }

        Notification::make()
            ->title('Masa berlaku kata sandi telah habis')
            ->body('Silakan ganti kata sandi Anda untuk melanjutkan.')
            ->warning()
            ->send();

        return redirect()->to(filament()->getProfileUrl());
    }
}
