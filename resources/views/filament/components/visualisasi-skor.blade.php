@php
    $sa = null;
    if (isset($record) && $record instanceof \App\Models\SelfAssessment) {
        $sa = $record;
    } elseif (isset($getRecord) && is_callable($getRecord)) {
        $sa = $getRecord();
    } elseif (isset($component) && method_exists($component, 'getRecord')) {
        $sa = $component->getRecord();
    }

    if (!$sa && isset($peserta) && $peserta) {
        $sa = \App\Models\SelfAssessment::where('peserta_id', $peserta->id)->first();
    }

    if (!$sa) {
        $sa = new \App\Models\SelfAssessment();
    }

    $skorMandiriIntegritas = (int) ($sa->skor_mandiri_integritas ?? $sa->integritas_score ?? 0);
    $skorMandiriReputasi = (int) ($sa->skor_mandiri_reputasi ?? $sa->reputasi_score ?? 0);
    $skorMandiriKompetensi = (int) ($sa->skor_mandiri_kompetensi ?? $sa->kompetensi_score ?? 0);
    $skorMandiriInovasi = (int) ($sa->skor_mandiri_inovasi ?? $sa->inovasi_score ?? 0);

    $totalMandiri = $skorMandiriIntegritas + $skorMandiriReputasi + $skorMandiriKompetensi + $skorMandiriInovasi;
    $maxTotal = 59;
    $percentMandiri = $maxTotal > 0 ? round(($totalMandiri / $maxTotal) * 100, 1) : 0;

    $skorPengujiIntegritas = (int) ($sa->skor_penguji_integritas ?? $sa->integritas_score ?? 0);
    $skorPengujiReputasi = (int) ($sa->skor_penguji_reputasi ?? $sa->reputasi_score ?? 0);
    $skorPengujiKompetensi = (int) ($sa->skor_penguji_kompetensi ?? $sa->kompetensi_score ?? 0);
    $skorPengujiInovasi = (int) ($sa->skor_penguji_inovasi ?? $sa->inovasi_score ?? 0);

    $totalPenguji = $skorPengujiIntegritas + $skorPengujiReputasi + $skorPengujiKompetensi + $skorPengujiInovasi;
    $percentPenguji = $maxTotal > 0 ? round(($totalPenguji / $maxTotal) * 100, 2) : 0;

    // Predikat Mandiri
    $predikatMandiri = $percentMandiri >= 90 ? 'AA' : ($percentMandiri >= 80 ? 'A' : ($percentMandiri >= 70 ? 'B' : ($percentMandiri >= 60 ? 'C' : 'D')));
    // Predikat Penguji
    $predikatPenguji = $percentPenguji >= 90 ? 'AA' : ($percentPenguji >= 80 ? 'A' : ($percentPenguji >= 70 ? 'B' : ($percentPenguji >= 60 ? 'C' : 'D')));

    // Per Category Percentages
    $pctIntegritasM = round(($skorMandiriIntegritas / 9) * 100, 1);
    $pctReputasiM   = round(($skorMandiriReputasi / 2) * 100, 1);
    $pctKompetensiM = round(($skorMandiriKompetensi / 36) * 100, 1);
    $pctInovasiM    = round(($skorMandiriInovasi / 12) * 100, 1);

    $pctIntegritasP = round(($skorPengujiIntegritas / 9) * 100, 1);
    $pctReputasiP   = round(($skorPengujiReputasi / 2) * 100, 1);
    $pctKompetensiP = round(($skorPengujiKompetensi / 36) * 100, 1);
    $pctInovasiP    = round(($skorPengujiInovasi / 12) * 100, 1);

    $chartPayload = [
        'labels' => ['INTEGRITAS', 'REPUTASI KEUANGAN', 'KOMPETENSI', 'KREATIVITAS & INOVASI'],
        'mandiri' => [$pctIntegritasM, $pctReputasiM, $pctKompetensiM, $pctInovasiM],
        'penguji' => [$pctIntegritasP, $pctReputasiP, $pctKompetensiP, $pctInovasiP],
        'mandiriRaw' => ["{$skorMandiriIntegritas}/9", "{$skorMandiriReputasi}/2", "{$skorMandiriKompetensi}/36", "{$skorMandiriInovasi}/12"],
        'pengujiRaw' => ["{$skorPengujiIntegritas}/9", "{$skorPengujiReputasi}/2", "{$skorPengujiKompetensi}/36", "{$skorPengujiInovasi}/12"],
    ];
@endphp

<div 
    x-data="visualisasiSkorData({{ json_encode($chartPayload) }})"
    class="w-full rounded-2xl border border-slate-200/90 dark:border-white/10 bg-white dark:bg-slate-900 shadow-sm p-5 sm:p-6 mb-6"
    style="font-family: inherit;"
>
    <!-- TOP HEADER BAR: TITLE + MODE SWITCHER BUTTONS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 dark:border-white/10 mb-6">
        <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    Visualisasi Skor
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Komparasi Hasil Asesmen Mandiri Peserta vs Asesmen Tim Penguji</p>
            </div>
        </div>

        <!-- TOGGLE BUTTONS (BAR, RADAR, LINE) EXACT MATCH TO SCREENSHOT -->
        <div class="inline-flex items-center p-1 rounded-xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700 shadow-inner self-start sm:self-auto">
            <!-- BAR BUTTON -->
            <button 
                type="button"
                @click="switchMode('bar')"
                :class="mode === 'bar' ? 'bg-blue-600 text-white shadow-sm font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white font-medium'"
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs transition-all duration-200"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h14" />
                </svg>
                Bar
            </button>

            <!-- RADAR BUTTON -->
            <button 
                type="button"
                @click="switchMode('radar')"
                :class="mode === 'radar' ? 'bg-blue-600 text-white shadow-sm font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white font-medium'"
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs transition-all duration-200"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                </svg>
                Radar
            </button>

            <!-- LINE BUTTON -->
            <button 
                type="button"
                @click="switchMode('line')"
                :class="mode === 'line' ? 'bg-blue-600 text-white shadow-sm font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white font-medium'"
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs transition-all duration-200"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
                Line
            </button>
        </div>
    </div>

    <!-- MAIN BODY: LEFT STAT CARDS + RIGHT CHART -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
        
        <!-- LEFT COLUMN: 2 GRADIENT STAT CARDS (4 OF 12 COLS) -->
        <div class="lg:col-span-4 flex flex-col gap-4">
            
            <!-- CARD 1: ASESMEN MANDIRI (ROYAL BLUE GRADIENT) -->
            <div 
                class="relative rounded-2xl p-5 sm:p-6 text-white shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg"
                style="background: linear-gradient(135deg, #4f6ff6 0%, #3b82f6 50%, #60a5fa 100%);"
            >
                <div class="flex items-center justify-center gap-2 text-white text-sm font-semibold tracking-wide mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Asesmen Mandiri</span>
                </div>

                <!-- Big Score Number -->
                <div class="flex items-center justify-center my-1">
                    <span class="text-5xl sm:text-6xl font-black tracking-tight text-white drop-shadow-sm">
                        {{ number_format($percentMandiri, 0) }}
                    </span>
                </div>

                <!-- Predikat Badge -->
                <div class="flex flex-col items-center justify-center mt-2 mb-3">
                    <span class="px-5 py-1 rounded-xl bg-white text-slate-900 font-extrabold text-sm shadow-md tracking-wider">
                        {{ $predikatMandiri }}
                    </span>
                    <span class="text-[11px] font-semibold text-white/90 mt-1 uppercase tracking-widest">Predikat</span>
                </div>

                <!-- Bottom Progress Bar -->
                <div class="mt-2">
                    <div class="w-full bg-black/20 h-2 rounded-full overflow-hidden p-0.5">
                        <div class="bg-white h-full rounded-full transition-all duration-700" style="width: {{ min(100, $percentMandiri) }}%;"></div>
                    </div>
                </div>
            </div>

            <!-- CARD 2: ASESMEN EVALUATOR (PINK / MAGENTA GRADIENT) -->
            <div 
                class="relative rounded-2xl p-5 sm:p-6 text-white shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg"
                style="background: linear-gradient(135deg, #f43f8e 0%, #ec4899 50%, #fb7185 100%);"
            >
                <div class="flex items-center justify-center gap-2 text-white text-sm font-semibold tracking-wide mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Asesmen Evaluator</span>
                </div>

                <!-- Big Score Number with Indonesian Comma Decimal -->
                <div class="flex items-center justify-center my-1">
                    <span class="text-5xl sm:text-6xl font-black tracking-tight text-white drop-shadow-sm">
                        {{ number_format($percentPenguji, 2, ',', '.') }}
                    </span>
                </div>

                <!-- Predikat Badge -->
                <div class="flex flex-col items-center justify-center mt-2 mb-3">
                    <span class="px-5 py-1 rounded-xl bg-white text-rose-700 font-extrabold text-sm shadow-md tracking-wider">
                        {{ $predikatPenguji }}
                    </span>
                    <span class="text-[11px] font-semibold text-white/90 mt-1 uppercase tracking-widest">Predikat</span>
                </div>

                <!-- Bottom Progress Bar -->
                <div class="mt-2">
                    <div class="w-full bg-black/20 h-2 rounded-full overflow-hidden p-0.5">
                        <div class="bg-white h-full rounded-full transition-all duration-700" style="width: {{ min(100, $percentPenguji) }}%;"></div>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: INTERACTIVE CHART (8 OF 12 COLS) -->
        <div class="lg:col-span-8 flex flex-col justify-center">
            
            <!-- Top Chart Series Legend -->
            <div class="flex items-center justify-center gap-6 mb-3 text-xs font-bold text-slate-700 dark:text-slate-300">
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded-full shadow-sm" style="background-color: #5b86e5;"></span>
                    <span>Asesmen Mandiri</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded-full shadow-sm" style="background-color: #f857a6;"></span>
                    <span>Asesmen Evaluator</span>
                </div>
            </div>

            <!-- Canvas Container -->
            <div class="relative w-full h-[320px] sm:h-[350px] flex items-center justify-center">
                <canvas x-ref="chartCanvas" class="w-full h-full"></canvas>
            </div>

        </div>

    </div>

    <!-- BOTTOM INFO BAR: EXACT MATCH TO CYAN BOX IN SCREENSHOT -->
    <div class="mt-6 p-3.5 rounded-xl border border-cyan-200/90 dark:border-cyan-800/50 bg-[#e0f7fa] dark:bg-cyan-950/30 text-xs text-cyan-950 dark:text-cyan-200 flex flex-wrap items-center justify-between gap-3 font-medium">
        <div class="flex flex-wrap items-center gap-2">
            <svg class="w-4 h-4 text-cyan-700 dark:text-cyan-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-bold">Keterangan:</span>
            <span class="inline-flex items-center gap-1.5 ml-1">
                <span class="w-3 h-3 rounded-sm" style="background-color: #5b86e5;"></span> Asesmen Mandiri
            </span>
            <span class="inline-flex items-center gap-1.5 ml-2">
                <span class="w-3 h-3 rounded-sm" style="background-color: #f857a6;"></span> Asesmen Evaluator
            </span>
            <span class="text-cyan-600/60 dark:text-cyan-400/60 ml-1">|</span>
            <span class="text-cyan-900 dark:text-cyan-300">Klik pada grafik untuk melihat detail nilai</span>
        </div>
        <div class="text-[11px] font-semibold text-cyan-800 dark:text-cyan-300">
            Juklak UKK Deputi Bidang Perkoperasian
        </div>
    </div>
</div>

<script>
(function() {
    function initVisualisasiSkor() {
        if (typeof Alpine === 'undefined' || Alpine._visualisasiSkorRegistered) return;
        Alpine._visualisasiSkorRegistered = true;

        Alpine.data('visualisasiSkorData', (payload) => ({
            mode: 'bar',
            chartInstance: null,
            data: payload,

            init() {
                const loadScript = (src) => new Promise((resolve) => {
                    if (document.querySelector(`script[src="${src}"]`)) {
                        return resolve();
                    }
                    const script = document.createElement('script');
                    script.src = src;
                    script.onload = () => resolve();
                    script.onerror = () => resolve();
                    document.head.appendChild(script);
                });

                Promise.all([
                    (typeof Chart === 'undefined') ? loadScript('https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js') : Promise.resolve(),
                    (typeof ChartDataLabels === 'undefined') ? loadScript('https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js') : Promise.resolve()
                ]).then(() => {
                    this.$nextTick(() => this.renderChart());
                });

                // Watch theme mode changes on document
                const observer = new MutationObserver(() => {
                    if (this.chartInstance) {
                        this.renderChart();
                    }
                });
                observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            },

            switchMode(newMode) {
                this.mode = newMode;
                this.renderChart();
            },

            renderChart() {
                if (typeof Chart === 'undefined' || !this.$refs.chartCanvas) return;

                if (this.chartInstance) {
                    this.chartInstance.destroy();
                    this.chartInstance = null;
                }

                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#cbd5e1' : '#334155';
                const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';
                const tooltipBg = isDark ? 'rgba(15, 23, 42, 0.95)' : 'rgba(15, 23, 42, 0.9)';
                const pluginsList = (typeof ChartDataLabels !== 'undefined') ? [ChartDataLabels] : [];

                const ctx = this.$refs.chartCanvas.getContext('2d');

                if (this.mode === 'bar') {
                    // HORIZONTAL BAR CHART (Sesuai Screenshot 1)
                    this.chartInstance = new Chart(ctx, {
                        type: 'bar',
                        plugins: pluginsList,
                        data: {
                            labels: this.data.labels,
                            datasets: [
                                {
                                    label: 'Asesmen Mandiri',
                                    data: this.data.mandiri,
                                    backgroundColor: '#5b86e5',
                                    borderRadius: 8,
                                    barPercentage: 0.82,
                                    categoryPercentage: 0.75,
                                },
                                {
                                    label: 'Asesmen Evaluator',
                                    data: this.data.penguji,
                                    backgroundColor: '#f857a6',
                                    borderRadius: 8,
                                    barPercentage: 0.82,
                                    categoryPercentage: 0.75,
                                }
                            ]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                datalabels: {
                                    display: true,
                                    anchor: 'start',
                                    align: 'right',
                                    offset: 6,
                                    color: '#ffffff',
                                    font: { weight: 'bold', size: 10 },
                                    formatter: (val) => val > 0 ? String(val).replace('.', ',') : ''
                                },
                                tooltip: {
                                    backgroundColor: tooltipBg,
                                    titleColor: '#ffffff',
                                    bodyColor: '#e2e8f0',
                                    padding: 12,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: (ctx) => {
                                            const raw = ctx.datasetIndex === 0 
                                                ? this.data.mandiriRaw[ctx.dataIndex] 
                                                : this.data.pengujiRaw[ctx.dataIndex];
                                            return ` ${ctx.dataset.label}: ${ctx.raw}% (${raw} Poin)`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    min: 0,
                                    max: 100,
                                    grid: { color: gridColor },
                                    ticks: {
                                        color: textColor,
                                        stepSize: 10,
                                        callback: (val) => val + '%'
                                    }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: {
                                        color: textColor,
                                        font: { weight: 'bold', size: 10.5 }
                                    }
                                }
                            }
                        }
                    });
                } else if (this.mode === 'radar') {
                    // RADAR / SPIDER CHART (Sesuai Screenshot 2)
                    this.chartInstance = new Chart(ctx, {
                        type: 'radar',
                        plugins: pluginsList,
                        data: {
                            labels: this.data.labels,
                            datasets: [
                                {
                                    label: 'Asesmen Mandiri',
                                    data: this.data.mandiri,
                                    borderColor: '#5b86e5',
                                    backgroundColor: 'rgba(91, 134, 229, 0.25)',
                                    pointBackgroundColor: '#5b86e5',
                                    pointBorderColor: '#ffffff',
                                    pointRadius: 4.5,
                                    borderWidth: 2.5
                                },
                                {
                                    label: 'Asesmen Evaluator',
                                    data: this.data.penguji,
                                    borderColor: '#f857a6',
                                    backgroundColor: 'rgba(248, 87, 166, 0.25)',
                                    pointBackgroundColor: '#f857a6',
                                    pointBorderColor: '#ffffff',
                                    pointRadius: 4.5,
                                    borderWidth: 2.5
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                datalabels: {
                                    display: true,
                                    align: (ctx) => ctx.datasetIndex === 0 ? 'top' : 'bottom',
                                    offset: 4,
                                    color: textColor,
                                    font: { weight: 'bold', size: 10 },
                                    formatter: (val) => val > 0 ? String(val).replace('.', ',') : '0'
                                },
                                tooltip: {
                                    backgroundColor: tooltipBg,
                                    titleColor: '#ffffff',
                                    bodyColor: '#e2e8f0',
                                    padding: 12,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: (ctx) => {
                                            const raw = ctx.datasetIndex === 0 
                                                ? this.data.mandiriRaw[ctx.dataIndex] 
                                                : this.data.pengujiRaw[ctx.dataIndex];
                                            return ` ${ctx.dataset.label}: ${ctx.raw}% (${raw})`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                r: {
                                    min: 0,
                                    max: 100,
                                    ticks: {
                                        stepSize: 20,
                                        backdropColor: 'transparent',
                                        color: textColor,
                                        callback: (val) => val + '%'
                                    },
                                    grid: { color: gridColor },
                                    angleLines: { color: gridColor },
                                    pointLabels: {
                                        color: textColor,
                                        font: { weight: 'bold', size: 10 }
                                    }
                                }
                            }
                        }
                    });
                } else if (this.mode === 'line') {
                    // SPLINE LINE CHART (Sesuai Screenshot 3)
                    this.chartInstance = new Chart(ctx, {
                        type: 'line',
                        plugins: pluginsList,
                        data: {
                            labels: this.data.labels,
                            datasets: [
                                {
                                    label: 'Asesmen Mandiri',
                                    data: this.data.mandiri,
                                    borderColor: '#5b86e5',
                                    backgroundColor: 'rgba(91, 134, 229, 0.1)',
                                    tension: 0.45,
                                    fill: false,
                                    pointBackgroundColor: '#5b86e5',
                                    pointBorderColor: '#ffffff',
                                    pointRadius: 5.5,
                                    borderWidth: 3
                                },
                                {
                                    label: 'Asesmen Evaluator',
                                    data: this.data.penguji,
                                    borderColor: '#f857a6',
                                    backgroundColor: 'rgba(248, 87, 166, 0.1)',
                                    tension: 0.45,
                                    fill: false,
                                    pointBackgroundColor: '#f857a6',
                                    pointBorderColor: '#ffffff',
                                    pointRadius: 5.5,
                                    borderWidth: 3
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                datalabels: {
                                    display: true,
                                    align: (ctx) => ctx.datasetIndex === 0 ? 'top' : 'bottom',
                                    offset: 5,
                                    color: textColor,
                                    font: { weight: 'bold', size: 10 },
                                    formatter: (val) => val > 0 ? String(val).replace('.', ',') : '0'
                                },
                                tooltip: {
                                    backgroundColor: tooltipBg,
                                    titleColor: '#ffffff',
                                    bodyColor: '#e2e8f0',
                                    padding: 12,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: (ctx) => {
                                            const raw = ctx.datasetIndex === 0 
                                                ? this.data.mandiriRaw[ctx.dataIndex] 
                                                : this.data.pengujiRaw[ctx.dataIndex];
                                            return ` ${ctx.dataset.label}: ${ctx.raw}% (${raw})`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    min: 0,
                                    max: 100,
                                    grid: { color: gridColor },
                                    ticks: {
                                        color: textColor,
                                        stepSize: 20,
                                        callback: (val) => val + '%'
                                    }
                                },
                                x: {
                                    grid: { color: gridColor },
                                    ticks: {
                                        color: textColor,
                                        font: { weight: 'bold', size: 10.5 }
                                    }
                                }
                            }
                        }
                    });
                }
            }
        }));
    }

    if (typeof Alpine !== 'undefined') {
        initVisualisasiSkor();
    } else {
        document.addEventListener('alpine:init', initVisualisasiSkor);
    }
})();
</script>
