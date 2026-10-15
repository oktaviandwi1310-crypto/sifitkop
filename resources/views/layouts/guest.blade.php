<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Penilaian Mandiri (Self Assessment) - SIFITKOP</title>
    
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 antialiased text-slate-800">

    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
                        S
                    </div>
                    <span class="font-bold text-lg text-slate-900">SIFITKOP Jawa Tengah</span>
                </div>
            </div>
        </header>

        <main class="py-10">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
