@php
    $sa = \App\Models\SelfAssessment::where('peserta_id', $peserta->id)->first();
@endphp

@if(!$sa)
    <x-filament::section>
        <div class="flex items-center gap-3 text-danger-600 dark:text-danger-400">
            <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
            <span class="font-medium text-sm">Peserta ini belum mengisi Form Self Assessment (Penilaian Mandiri).</span>
        </div>
    </x-filament::section>
@else
    @php
        $totalSA = $sa->integritas_score + $sa->reputasi_score + $sa->kompetensi_score + $sa->inovasi_score;
        $maxSA = 59;
        $percentage = round(($totalSA / $maxSA) * 100);
        $scoreColor = $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger');
    @endphp

    <div class="space-y-6">
        
        <!-- Score Banner -->
        <x-filament::section class="border-t-4" style="border-top-color: rgb(var(--{{ $scoreColor }}-500));">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <x-filament::badge size="lg" color="{{ $scoreColor }}">
                        <x-heroicon-o-chart-bar class="w-6 h-6" />
                    </x-filament::badge>
                    <div>
                        <h3 class="text-lg font-bold">Total Skor Self Assessment (UKK)</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            @if($sa->penguji)
                                Diverifikasi oleh Asesor: <span class="font-bold text-primary-600 dark:text-primary-400">{{ $sa->penguji->name }}</span> (Status: {{ $sa->status_penguji ?? $sa->status_kelulusan }})
                            @else
                                Berdasarkan isian mandiri peserta (Belum diverifikasi penguji)
                            @endif
                        </p>
                    </div>
                </div>
                <div class="text-center sm:text-right">
                    <div class="flex items-baseline justify-center sm:justify-end gap-2">
                        <span class="text-4xl font-black text-{{ $scoreColor }}-600 dark:text-{{ $scoreColor }}-400" style="color: rgb(var(--{{ $scoreColor }}-600));">{{ $totalSA }}</span>
                        <span class="text-xl font-bold text-gray-400 dark:text-gray-500">/ {{ $maxSA }}</span>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Category Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $categories = [
                    ['title' => 'Integritas', 'score' => $sa->integritas_score, 'max' => 9, 'icon' => 'heroicon-o-shield-check', 'color' => 'info'],
                    ['title' => 'Reputasi', 'score' => $sa->reputasi_score, 'max' => 2, 'icon' => 'heroicon-o-currency-dollar', 'color' => 'success'],
                    ['title' => 'Kompetensi', 'score' => $sa->kompetensi_score, 'max' => 36, 'icon' => 'heroicon-o-academic-cap', 'color' => 'primary'],
                    ['title' => 'Inovasi', 'score' => $sa->inovasi_score, 'max' => 12, 'icon' => 'heroicon-o-light-bulb', 'color' => 'warning'],
                ];
            @endphp

            @foreach($categories as $cat)
                <x-filament::section class="text-center p-0">
                    <div class="flex flex-col items-center justify-center p-4">
                        <x-filament::badge color="{{ $cat['color'] }}" class="mb-3">
                            @svg($cat['icon'], 'w-5 h-5')
                        </x-filament::badge>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">{{ $cat['title'] }}</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $cat['score'] }}</span>
                            <span class="text-xs font-bold text-gray-400 dark:text-gray-500">/ {{ $cat['max'] }}</span>
                        </div>
                    </div>
                </x-filament::section>
            @endforeach
        </div>
        
        <!-- Clarification Section -->
        <x-filament::section icon="heroicon-o-chat-bubble-bottom-center-text">
            <x-slot name="heading">
                Detail Jawaban Klarifikasi (Esai)
            </x-slot>
            
            <div class="space-y-6 mt-4">
                @if(isset($sa->jawaban_raw['klarifikasi']) && is_array($sa->jawaban_raw['klarifikasi']))
                    @php
                        $klarifikasiQuestions = [
                            'Jelaskan kewajiban dan tanggung jawab Saudara jika nanti terpilih...',
                            'Jelaskan mengenai produk/usaha Koperasi saat ini.',
                            'Jelaskan mengenai perizinan yang telah dimiliki oleh Koperasi.',
                            'Jelaskan mengenai mekanisme pembagian SHU di Koperasi.',
                            'Jelaskan mekanisme pengambilan keputusan di Koperasi.',
                            'Jelaskan mekanisme pengawasan internal yang dilakukan.',
                            'Jelaskan peraturan yang harus dimiliki Koperasi dan pengesahannya.',
                            'Apakah pernah bertindak sebagai Pengurus/Pengawas yang dipailitkan?',
                            'Informasikan detail Simpanan Pokok, Simpanan Wajib, dll.',
                            'Jelaskan pemahaman mengenai modal penyertaan pada Koperasi.',
                            'Apakah merangkap jabatan pengurus pada KSP/KSPPS lain?',
                            'Apakah merangkap jabatan pengawas pada KSP/KSPPS lain?',
                            'Jelaskan informasi negatif dari pemberitaan/pengaduan (jika ada).',
                            'Apakah memiliki hubungan semenda dengan pengurus/pengawas lain?',
                            'Jelaskan alasan jika tidak direkomendasikan dalam RAT.'
                        ];
                    @endphp
                    @foreach($sa->jawaban_raw['klarifikasi'] as $index => $jawaban)
                        <div class="pl-4 border-l-2 border-primary-500 dark:border-primary-400">
                            <p class="text-xs font-bold text-primary-600 dark:text-primary-400 mb-1 uppercase tracking-wider">
                                Pertanyaan {{ $index + 1 }}
                            </p>
                            <p class="text-sm font-medium mb-2" style="opacity: 0.75;">
                                {{ $klarifikasiQuestions[$index] ?? 'Pertanyaan Klarifikasi' }}
                            </p>
                            <div class="p-3 rounded-xl border" style="background-color: rgba(100, 100, 100, 0.1); border-color: rgba(100, 100, 100, 0.2);">
                                <p class="text-sm whitespace-pre-wrap leading-relaxed">{{ empty($jawaban) || strtolower($jawaban) === 'tidak ada' ? '-' : $jawaban }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-6">
                        <x-heroicon-o-document-magnifying-glass class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada data jawaban klarifikasi yang tersimpan.</p>
                    </div>
                @endif
            </div>
        </x-filament::section>
        
    </div>
@endif
