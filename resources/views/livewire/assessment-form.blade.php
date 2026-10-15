<div class="min-h-screen bg-slate-50 py-10 font-sans">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @if($isSubmitted)
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden mb-8 transform transition-all duration-500 hover:shadow-2xl">
                <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 p-10 text-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
                    <div class="relative z-10 inline-flex items-center justify-center w-24 h-24 rounded-full bg-white shadow-lg mb-6 border-4 border-emerald-200">
                        <svg class="h-12 w-12 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-4xl font-extrabold text-white mb-4 tracking-tight drop-shadow-md relative z-10">Self Assessment Selesai!</h3>
                    <p class="text-emerald-50 text-xl max-w-2xl mx-auto relative z-10 font-light">
                        Terima kasih. Hasil Penilaian Mandiri dan Dokumen Paparan Anda telah berhasil dikirim dan tersimpan dengan aman di sistem Dinas Koperasi Jawa Tengah.
                    </p>
                </div>

                @if($peserta->status_verifikasi == 'Terverifikasi' && $peserta->jadwal_wawancara)
                    <div class="p-10 bg-white">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="p-3 bg-blue-100 rounded-2xl">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Informasi Wawancara UKK</h3>
                        </div>
                        
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-3xl p-8 mb-6 shadow-inner">
                            <p class="text-blue-800 font-medium text-lg mb-2">Jadwal Wawancara Anda:</p>
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-3xl font-black text-blue-900 drop-shadow-sm">{{ \Carbon\Carbon::parse($peserta->jadwal_wawancara)->translatedFormat('l, d F Y - H:i') }} WIB</p>
                            </div>
                        </div>

                        @if($peserta->dokumen_paparan)
                            <div class="mb-4 p-6 border-2 border-emerald-100 bg-emerald-50 rounded-2xl flex items-center justify-between hover:border-emerald-200 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="p-2 bg-emerald-100 rounded-lg">
                                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div>
                                        <span class="block text-emerald-900 font-bold text-lg">Materi paparan sudah diunggah.</span>
                                        <span class="block text-emerald-700 text-sm mt-1">Dokumen ini akan digunakan oleh penguji saat sesi wawancara.</span>
                                    </div>
                                </div>
                                <a href="/storage/{{ $peserta->dokumen_paparan }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-emerald-200 text-emerald-700 hover:bg-emerald-50 font-bold text-sm rounded-xl shadow-sm transition-all hover:shadow-md">
                                    Lihat Dokumen
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-8 bg-slate-50 border-t border-slate-100 text-center">
                        <div class="inline-flex items-center gap-3 px-6 py-3 bg-amber-100 text-amber-800 rounded-full font-medium text-sm">
                            <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Menunggu verifikasi dan penjadwalan wawancara oleh Admin Dinas
                        </div>
                    </div>
                @endif
            </div>
        @else

            <!-- WIZARD UI DENGAN ALPINE.JS -->
            <div x-data="{ step: @entangle('currentStep'), totalSteps: 6 }" class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 overflow-hidden mb-10 transform transition-all">
                
                <!-- Premium Header -->
                <div class="relative bg-gradient-to-br from-slate-900 via-blue-950 to-indigo-900 px-8 py-10 text-white overflow-hidden">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
                    <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
                    
                    <div class="relative z-10 max-w-3xl mx-auto text-center">
                        <span class="inline-block px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-blue-100 text-xs font-bold uppercase tracking-widest mb-4 shadow-xl">
                            SIFITKOP Jawa Tengah
                        </span>
                        <h1 class="text-3xl md:text-4xl font-extrabold mb-3 tracking-tight drop-shadow-lg leading-tight">Penilaian Mandiri <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-cyan-200">(Self Assessment)</span></h1>
                        
                        <!-- WIZARD STEPPER -->
                        <div class="mt-8 relative max-w-2xl mx-auto">
                            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded-full bg-slate-700">
                                <div :style="'width: ' + ((step / totalSteps) * 100) + '%'" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-blue-400 to-cyan-300 transition-all duration-500 rounded-full"></div>
                            </div>
                            <div class="flex justify-between text-xs font-bold text-blue-200 uppercase tracking-widest">
                                <span :class="{'text-white': step >= 1}">Integritas</span>
                                <span :class="{'text-white': step >= 2}">Reputasi</span>
                                <span :class="{'text-white': step >= 3}">Kompetensi</span>
                                <span :class="{'text-white': step >= 4}">Inovasi</span>
                                <span :class="{'text-white': step >= 5}">Klarifikasi</span>
                                <span :class="{'text-white': step >= 6}">Selesai</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- User Info Bar -->
                <div class="bg-white px-8 py-4 border-b border-slate-100 shadow-sm relative z-20 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-xl flex items-center justify-center border border-blue-200 shadow-inner">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold">Nama Calon</p>
                            <p class="text-base font-black text-slate-900">{{ $peserta->nama_lengkap }}</p>
                        </div>
                    </div>
                    <div class="hidden md:block w-px h-8 bg-slate-200"></div>
                    <div>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold">Asal Koperasi</p>
                        <p class="text-sm font-bold text-slate-800">{{ $peserta->koperasi->nama_koperasi }}</p>
                    </div>
                    <div class="hidden md:block w-px h-8 bg-slate-200"></div>
                    <div>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mb-0.5">Jabatan Usulan</p>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-black bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md">
                            {{ $peserta->jabatan_usulan }}
                        </span>
                    </div>
                </div>

                <form wire:submit.prevent="submit" class="bg-slate-50/50 min-h-[500px]">
                    
                    @if ($errors->any())
                        <div class="mx-8 mt-6 bg-red-50 text-red-700 p-4 rounded-xl text-sm border border-red-200 shadow-sm flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <div>
                                <strong class="font-bold block text-sm mb-1">Terdapat isian yang belum lengkap atau terlewat!</strong>
                                <span class="block text-xs">Silakan periksa langkah sebelumnya (Anda dapat kembali dengan menekan tombol 'Kembali'). Pastikan setiap pertanyaan terisi dan dokumen paparan terpilih.</span>
                            </div>
                        </div>
                    @endif

                    <div class="p-8 relative">
                        
                        <!-- STEP 1: INTEGRITAS -->
                        <div x-show="step === 1" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                            <section class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-2 h-full bg-blue-500 rounded-l-[2rem]"></div>
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                                        <span class="text-xl font-black">1</span>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Persyaratan Integritas</h2>
                                        <p class="text-slate-500 text-sm mt-1">Pilih "Ya" atau "Tidak" beserta Uraian keterangannya.</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-6">
                                    @php
                                        $integritasItems = [
                                            'int_1' => ['label' => 'A. Cakap melakukan perbuatan hukum.'],
                                            'int_2' => ['label' => 'B.1. Tidak sedang menjalani hukuman pidana yang telah berkekuatan hukum tetap.'],
                                            'int_3' => ['label' => 'B.2. Tidak pernah dihukum karena melakukan tindak pidana yang merugikan koperasi/keuangan negara (5 tahun terakhir).'],
                                            'int_4' => ['label' => 'C. Memiliki komitmen mematuhi peraturan perundang-undangan dan mendukung kebijakan Kementerian.'],
                                            'int_5' => ['label' => 'D.1. Tidak pernah melanggar komitmen terhadap Koperasi (dalam 3 tahun terakhir).'],
                                            'int_6' => ['label' => 'D.2. Tidak mengulangi perbuatan yang dilarang bagi pengurus.'],
                                            'int_7' => ['label' => 'D.3. Tidak melakukan perbuatan yang memberikan keuntungan tidak wajar yang merugikan.'],
                                            'int_8' => ['label' => 'D.4. Tidak melakukan perbuatan yang melanggar ketentuan, kewenangan, dan tanggung jawab.'],
                                            'int_9' => ['label' => 'D.5. Tidak pernah dinyatakan tidak mampu atau gagal menjalankan tugas jabatannya sebelumnya.']
                                        ];
                                    @endphp
                                    
                                    @foreach($integritasItems as $model => $item)
                                    <div class="group border border-slate-200 rounded-2xl p-5 transition-all hover:border-blue-300 hover:shadow-sm bg-slate-50/30">
                                        <p class="font-bold text-slate-800 text-base mb-3">{{ $item['label'] }}</p>
                                        <div class="flex flex-col md:flex-row gap-4">
                                            <div class="flex gap-2 md:w-1/3">
                                                <label class="cursor-pointer flex-1">
                                                    <input type="radio" wire:model.defer="{{ $model }}" value="1" class="peer sr-only">
                                                    <div class="rounded-xl border-2 border-slate-200 py-2 text-center peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 transition-all font-bold text-slate-500 hover:bg-slate-100 shadow-sm peer-checked:shadow-md">
                                                        Ya
                                                    </div>
                                                </label>
                                                <label class="cursor-pointer flex-1">
                                                    <input type="radio" wire:model.defer="{{ $model }}" value="0" class="peer sr-only">
                                                    <div class="rounded-xl border-2 border-slate-200 py-2 text-center peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500 transition-all font-bold text-slate-500 hover:bg-slate-100 shadow-sm peer-checked:shadow-md">
                                                        Tidak
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="md:w-2/3">
                                                <textarea wire:model.defer="uraian_{{ $model }}" class="w-full text-sm rounded-xl border-2 border-slate-200 bg-white focus:border-blue-500 focus:ring-0 p-3 transition-colors placeholder:text-slate-400" rows="1" placeholder="Uraian/Keterangan..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </section>
                        </div>

                        <!-- STEP 2: REPUTASI KEUANGAN -->
                        <div x-show="step === 2" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                            <section class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-2 h-full bg-emerald-500 rounded-l-[2rem]"></div>
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                                        <span class="text-xl font-black">2</span>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Persyaratan Reputasi Keuangan</h2>
                                        <p class="text-slate-500 text-sm mt-1">Status kredit dan reputasi keuangan 5 tahun terakhir.</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-6">
                                    @php
                                        $reputasiItems = [
                                            'rep_1' => ['label' => '1. Tidak memiliki kredit macet/pembiayaan macet (berdasarkan SLIK OJK/dibuktikan dengan Surat Pernyataan).'],
                                            'rep_2' => ['label' => '2. Tidak pernah dinyatakan pailit dalam waktu 5 tahun sebelum pencalonan.']
                                        ];
                                    @endphp
                                    
                                    @foreach($reputasiItems as $model => $item)
                                    <div class="group border border-slate-200 rounded-2xl p-5 transition-all hover:border-emerald-300 hover:shadow-sm bg-slate-50/30">
                                        <p class="font-bold text-slate-800 text-base mb-1">{{ $item['label'] }}</p>
                                        <p class="text-[11px] font-semibold text-emerald-600 bg-emerald-100/50 inline-block px-2 py-0.5 rounded border border-emerald-200 mb-4">
                                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Buktinya telah digabungkan di dalam "Dokumen Pendukung Peserta" saat didaftarkan oleh Koperasi.
                                        </p>
                                        <div class="flex flex-col md:flex-row gap-4">
                                            <div class="flex gap-2 md:w-1/3">
                                                <label class="cursor-pointer flex-1">
                                                    <input type="radio" wire:model.defer="{{ $model }}" value="1" class="peer sr-only">
                                                    <div class="rounded-xl border-2 border-slate-200 py-2 text-center peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 transition-all font-bold text-slate-500 hover:bg-slate-100 shadow-sm peer-checked:shadow-md">
                                                        Ya
                                                    </div>
                                                </label>
                                                <label class="cursor-pointer flex-1">
                                                    <input type="radio" wire:model.defer="{{ $model }}" value="0" class="peer sr-only">
                                                    <div class="rounded-xl border-2 border-slate-200 py-2 text-center peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500 transition-all font-bold text-slate-500 hover:bg-slate-100 shadow-sm peer-checked:shadow-md">
                                                        Tidak
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="md:w-2/3">
                                                <textarea wire:model.defer="uraian_{{ $model }}" class="w-full text-sm rounded-xl border-2 border-slate-200 bg-white focus:border-emerald-500 focus:ring-0 p-3 transition-colors placeholder:text-slate-400" rows="1" placeholder="Uraian/Keterangan..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </section>
                        </div>

                        <!-- STEP 3: KOMPETENSI -->
                        <div x-show="step === 3" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                            <section class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-2 h-full bg-indigo-500 rounded-l-[2rem]"></div>
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                                        <span class="text-xl font-black">3</span>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Persyaratan Kompetensi</h2>
                                        <p class="text-slate-500 text-sm mt-1">Pilih skor kompetensi (1: Kurang Sekali, 4: Baik) dan isikan Uraian.</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-6">
                                    @php
                                        $kompetensiList = [
                                            'kom_1' => 'Pengetahuan manajemen, SDM, kelembagaan, permodalan, dan akuntansi Koperasi.',
                                            'kom_2' => 'Kemampuan dalam mencapai tujuan organisasi Koperasi secara berkesinambungan.',
                                            'kom_3' => 'Kemampuan mengelola sumber daya manusia (SDM).',
                                            'kom_4' => 'Pemahaman terhadap Undang-Undang Perkoperasian dan peraturan turunannya.',
                                            'kom_5' => 'Pemahaman UU Tindak Pidana Pencucian Uang dan/atau UU lain yang terkait.',
                                            'kom_6' => 'Kemampuan merumuskan dan mengawal realisasi visi, misi, dan target.',
                                            'kom_7' => 'Kemampuan menetapkan target, prioritas, serta strategi mitigasi risiko.',
                                            'kom_8' => 'Kemampuan merumuskan kebijakan yang relevan.',
                                            'kom_9' => 'Keahlian dan pengalaman di bidang koperasi/keuangan minimum 2 tahun.',
                                        ];
                                    @endphp
                                    
                                    @foreach($kompetensiList as $model => $label)
                                    <div class="group border border-slate-200 rounded-2xl p-5 transition-all hover:border-indigo-300 hover:shadow-sm bg-slate-50/30">
                                        <p class="font-bold text-slate-800 text-base mb-3">{{ $loop->iteration }}. {{ $label }}</p>
                                        <div class="flex flex-col md:flex-row gap-4">
                                            <div class="md:w-[45%]">
                                                <div class="flex gap-2 h-full">
                                                    @foreach([1 => 'Sangat Kurang', 2 => 'Kurang', 3 => 'Cukup', 4 => 'Baik'] as $val => $desc)
                                                        <label class="cursor-pointer flex-1 h-full">
                                                            <input type="radio" wire:model.defer="{{ $model }}" value="{{ $val }}" class="peer sr-only">
                                                            <div class="h-full flex flex-col items-center justify-center rounded-xl border-2 border-slate-200 p-2 text-center peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 transition-all hover:bg-slate-100 shadow-sm peer-checked:shadow-md">
                                                                <span class="block text-xl font-black mb-0.5">{{ $val }}</span>
                                                                <span class="block text-[9px] uppercase tracking-wider opacity-80 font-bold leading-tight">{{ $desc }}</span>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="md:w-[55%]">
                                                <textarea wire:model.defer="uraian_{{ $model }}" class="w-full h-full min-h-[4rem] text-sm rounded-xl border-2 border-slate-200 bg-white focus:border-indigo-500 focus:ring-0 p-3 transition-colors placeholder:text-slate-400" placeholder="Tuliskan uraian atau keterangan di sini..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </section>
                        </div>

                        <!-- STEP 4: INOVASI -->
                        <div x-show="step === 4" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                            <section class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-2 h-full bg-violet-500 rounded-l-[2rem]"></div>
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600">
                                        <span class="text-xl font-black">4</span>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Kreativitas dan Inovasi</h2>
                                        <p class="text-slate-500 text-sm mt-1">Pilih skor kreativitas (1: Kurang Sekali, 4: Baik) dan isikan Uraian.</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-6">
                                    @php
                                        $inovasiList = [
                                            'ino_1' => 'Kemampuan dalam merencanakan pemanfaatan teknologi informasi untuk pengawasan/pengelolaan koperasi.',
                                            'ino_2' => 'Kemampuan dalam mengembangkan bisnis dan merespons tren pasar secara adaptif.',
                                            'ino_3' => 'Dukungan terhadap digitalisasi layanan dan operasional Koperasi (Core Banking System / Mobile).',
                                        ];
                                    @endphp
                                    
                                    @foreach($inovasiList as $model => $label)
                                    <div class="group border border-slate-200 rounded-2xl p-5 transition-all hover:border-violet-300 hover:shadow-sm bg-slate-50/30">
                                        <p class="font-bold text-slate-800 text-base mb-3">{{ $loop->iteration }}. {{ $label }}</p>
                                        <div class="flex flex-col md:flex-row gap-4">
                                            <div class="md:w-[45%]">
                                                <div class="flex gap-2 h-full">
                                                    @foreach([1 => 'Sangat Kurang', 2 => 'Kurang', 3 => 'Cukup', 4 => 'Baik'] as $val => $desc)
                                                        <label class="cursor-pointer flex-1 h-full">
                                                            <input type="radio" wire:model.defer="{{ $model }}" value="{{ $val }}" class="peer sr-only">
                                                            <div class="h-full flex flex-col items-center justify-center rounded-xl border-2 border-slate-200 p-2 text-center peer-checked:bg-violet-600 peer-checked:text-white peer-checked:border-violet-600 transition-all hover:bg-slate-100 shadow-sm peer-checked:shadow-md">
                                                                <span class="block text-xl font-black mb-0.5">{{ $val }}</span>
                                                                <span class="block text-[9px] uppercase tracking-wider opacity-80 font-bold leading-tight">{{ $desc }}</span>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="md:w-[55%]">
                                                <textarea wire:model.defer="uraian_{{ $model }}" class="w-full h-full min-h-[4rem] text-sm rounded-xl border-2 border-slate-200 bg-white focus:border-violet-500 focus:ring-0 p-3 transition-colors placeholder:text-slate-400" placeholder="Tuliskan uraian atau keterangan di sini..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </section>
                        </div>
                        
                        <!-- STEP 5: KLARIFIKASI -->
                        <div x-show="step === 5" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                            <section class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-2 h-full bg-amber-500 rounded-l-[2rem]"></div>
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                                        <span class="text-xl font-black">5</span>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Klarifikasi Penilaian Mandiri</h2>
                                        <p class="text-slate-500 text-sm mt-1">Uraikan jawaban Anda secara detail (Ketik 'Tidak Ada' jika tidak relevan).</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-6">
                                    @php
                                        $klarifikasiList = [
                                            'klarifikasi_1' => 'Jelaskan kewajiban dan tanggung jawab Saudara jika nanti terpilih sebagai Pengurus dan Pengawas Koperasi.',
                                            'klarifikasi_2' => 'Jelaskan mengenai produk/usaha Koperasi saat ini.',
                                            'klarifikasi_3' => 'Jelaskan mengenai perizinan yang telah dimiliki oleh Koperasi.',
                                            'klarifikasi_4' => 'Jelaskan mengenai mekanisme dan peraturan mengenai pembagian Sisa Hasil Usaha (SHU) di Koperasi.',
                                            'klarifikasi_5' => 'Jelaskan pemahaman Saudara mengenai mekanisme pengambilan keputusan di Koperasi.',
                                            'klarifikasi_6' => 'Jelaskan mekanisme pengawasan internal yang dilakukan di koperasi.',
                                            'klarifikasi_7' => 'Jelaskan pemahaman Saudara mengenai peraturan yang harus dimiliki oleh Koperasi dan bagaimana proses pengesahannya.',
                                            'klarifikasi_8' => 'Apakah Saudara pernah bertindak sebagai Pengurus dan Pengawas Koperasi yang dipailitkan/PKPU?',
                                            'klarifikasi_9' => 'Informasikan secara detail mengenai Simpanan Pokok, Simpanan Wajib ataupun Simpanan Lainnya di Koperasi.',
                                            'klarifikasi_10' => 'Jelaskan pemahaman Saudara mengenai modal penyertaan pada Koperasi.',
                                            'klarifikasi_11' => 'Apakah Saudara/i merangkap jabatan sebagai pengurus pada KSP/KSPPS lain?',
                                            'klarifikasi_12' => 'Apakah Saudara/i merangkap jabatan sebagai pengawas pada KSP/KSPPS lain?',
                                            'klarifikasi_13' => 'Apakah saudara dapat menjelaskan informasi negatif yang telah kami terima dari pemberitaan atau pengaduan?',
                                            'klarifikasi_14' => 'Apakah Saudara/i memiliki hubungan semenda dengan pengurus dan pengawas di KSP/KSPPS?',
                                            'klarifikasi_15' => 'Jelaskan alasan saudara tidak direkomendasikan di dalam rapat anggota sebagai pengurus atau pengawas.'
                                        ];
                                    @endphp
                                    
                                    @foreach($klarifikasiList as $model => $label)
                                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 hover:border-amber-300 transition-colors">
                                        <p class="font-bold text-slate-800 text-sm mb-3">{{ $loop->iteration }}. {{ $label }}</p>
                                        <div class="relative">
                                            <textarea wire:model.defer="{{ $model }}" class="w-full text-sm rounded-xl border-2 border-slate-200 bg-white focus:border-amber-500 focus:ring-0 p-3 transition-colors placeholder:text-slate-400" rows="2" placeholder="Ketik uraian jawaban secara rinci di sini..."></textarea>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </section>
                        </div>

                        <!-- STEP 6: UPLOAD PAPARAN & SUBMIT -->
                        <div x-show="step === 6" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                            <section class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-2 h-full bg-cyan-500 rounded-l-[2rem]"></div>
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-12 h-12 rounded-xl bg-cyan-100 flex items-center justify-center text-cyan-600">
                                        <span class="text-xl font-black">6</span>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Upload Dokumen Paparan</h2>
<<<<<<< HEAD
                                        <p class="text-slate-500 text-sm mt-1">Dokumen presentasi diunggah bersama form ini. (Opsional)</p>
=======
                                        <p class="text-slate-500 text-sm mt-1">Dokumen presentasi wajib diunggah bersama form ini.</p>
>>>>>>> 76773c195479ecc60c50740d854a1c878c65a710
                                    </div>
                                </div>
                                
                                <div class="border-2 border-dashed border-cyan-300 bg-cyan-50/50 rounded-3xl p-10 text-center hover:bg-cyan-50 transition-colors">
                                    <div class="w-20 h-20 mx-auto bg-white rounded-full flex items-center justify-center shadow-sm mb-6 border border-cyan-100">
                                        <svg class="w-10 h-10 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-slate-800 mb-2">Pilih File Presentasi (PDF/PPT/PPTX)</h3>
                                    <p class="text-slate-500 mb-8 max-w-md mx-auto">Dokumen paparan ini akan ditinjau oleh Admin dan dinilai oleh Penguji pada saat sesi wawancara. (Maksimal 10MB)</p>
                                    
                                    <div class="relative max-w-sm mx-auto">
                                        <input type="file" wire:model="dokumen_paparan" accept=".pdf,.ppt,.pptx" class="block w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-cyan-600 file:text-white hover:file:bg-cyan-700 cursor-pointer transition-colors shadow-md">
                                    </div>
                                    @error('dokumen_paparan') 
                                        <div class="mt-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm font-bold inline-block">{{ $message }}</div> 
                                    @enderror
                                </div>

                                <!-- SUBMIT BUTTON -->
                                <div class="pt-10 flex flex-col items-center">
                                    <button type="submit" class="group relative inline-flex items-center justify-center gap-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-lg py-5 px-12 rounded-full shadow-[0_0_40px_rgba(79,70,229,0.3)] hover:shadow-[0_0_60px_rgba(79,70,229,0.5)] transition-all transform hover:-translate-y-1 w-full sm:w-auto" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="submit" class="flex items-center gap-3">
                                            Kirim Keseluruhan Data
                                            <svg class="w-6 h-6 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </span>
                                        <span wire:loading wire:target="submit" class="flex items-center gap-3">
                                            <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            Sedang Menyimpan...
                                        </span>
                                    </button>
                                    <p class="text-slate-400 text-sm mt-4 text-center max-w-lg">Data yang dikirim akan langsung tersimpan dan diteruskan ke Admin Dinas.</p>
                                </div>
                            </section>
                        </div>

                        <!-- WIZARD NAVIGATION CONTROLS -->
                        <div class="mt-8 flex items-center justify-between">
                            <button type="button" x-show="step > 1" wire:click="previousStep" x-on:click="window.scrollTo({top: 0, behavior: 'smooth'})" class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-slate-200 text-slate-700 hover:bg-slate-50 font-bold rounded-xl transition-all shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                Kembali
                            </button>
                            <div x-show="step === 1" class="w-[120px]"></div> <!-- Spacer to keep Next button on right when back button is hidden -->
                            
                            <button type="button" x-show="step < totalSteps" wire:click="nextStep" x-on:click="window.scrollTo({top: 0, behavior: 'smooth'})" class="inline-flex items-center gap-2 px-8 py-3 bg-blue-600 text-white hover:bg-blue-700 font-bold rounded-xl transition-all shadow-md hover:shadow-lg relative" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="nextStep" class="flex items-center gap-2">
                                    Selanjutnya
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </span>
                                <span wire:loading wire:target="nextStep" class="flex items-center gap-2">
                                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Memeriksa...
                                </span>
                            </button>
                        </div>

                    </div>
                </form>
            </div>
            
        @endif
    </div>
</div>
