<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kesalahan Sistem - SIFITKOP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-xl w-full text-center">
        <div class="relative">
            <div class="text-[12rem] font-extrabold text-red-50 tracking-tighter leading-none mb-4">500</div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="bg-white p-4 rounded-2xl shadow-lg border border-red-50 transform rotate-6">
                    <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-800 mb-4 mt-8">Terjadi Kesalahan Sistem</h1>
        <p class="text-lg text-slate-500 mb-10 leading-relaxed">Maaf, kami sedang mengalami masalah teknis pada peladen kami. Tim teknis telah diberitahu dan sedang memperbaikinya. Harap coba lagi beberapa saat.</p>
        <div class="flex justify-center gap-4">
            <button onclick="window.location.reload()" class="inline-flex justify-center items-center px-8 py-4 text-sm font-semibold rounded-xl text-white bg-red-600 hover:bg-red-700 shadow-xl shadow-red-200/50 transition-all hover:-translate-y-0.5">
                Coba Muat Ulang
            </button>
            <a href="/" class="inline-flex justify-center items-center px-8 py-4 text-sm font-semibold rounded-xl text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 shadow-sm transition-all hover:-translate-y-0.5">
                Ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
