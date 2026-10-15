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

<style>
.vs-root {
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    padding: 1.125rem 1.25rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    font-family: inherit;
    box-sizing: border-box;
    width: 100%;
}
.dark .vs-root {
    background-color: #0f172a;
    border-color: rgba(255, 255, 255, 0.1);
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3);
}

.vs-header {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 0.875rem;
    gap: 1rem;
}
.dark .vs-header {
    border-bottom-color: rgba(255, 255, 255, 0.08);
}

.vs-header-left {
    display: flex;
    align-items: center;
    gap: 0.625rem;
}
.vs-header-icon {
    padding: 0.45rem;
    border-radius: 0.625rem;
    background-color: #eff6ff;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
}
.dark .vs-header-icon {
    background-color: rgba(37, 99, 235, 0.2);
    color: #60a5fa;
}

.vs-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    line-height: 1.25;
}
.dark .vs-title {
    color: #f8fafc;
}
.vs-subtitle {
    font-size: 0.725rem;
    color: #64748b;
    margin: 0;
}
.dark .vs-subtitle {
    color: #94a3b8;
}

.vs-toggle-group {
    display: inline-flex;
    background-color: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 0.2rem;
    gap: 0.2rem;
}
.dark .vs-toggle-group {
    background-color: #1e293b;
    border-color: #334155;
}

.vs-toggle-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #475569;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}
.dark .vs-toggle-btn {
    color: #94a3b8;
}
.vs-toggle-btn:hover {
    color: #0f172a;
}
.dark .vs-toggle-btn:hover {
    color: #ffffff;
}
.vs-toggle-btn.active {
    background-color: #2563eb !important;
    color: #ffffff !important;
    font-weight: 700;
    box-shadow: 0 1px 2px rgba(37, 99, 235, 0.3);
}

/* 2 COLUMNS COMPACT LAYOUT */
.vs-content-2col {
    display: flex;
    flex-direction: row;
    gap: 1.25rem;
    align-items: stretch;
    width: 100%;
}

/* LEFT COLUMN: CARDS (~260px) */
.vs-col-cards {
    flex: 0 0 250px;
    width: 250px;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    justify-content: space-between;
}

.vs-card {
    border-radius: 0.75rem;
    padding: 0.75rem 0.875rem;
    color: #ffffff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-sizing: border-box;
    flex: 1;
}

.vs-card-mandiri {
    background: linear-gradient(135deg, #4f6ff6 0%, #3b82f6 50%, #60a5fa 100%);
}
.vs-card-evaluator {
    background: linear-gradient(135deg, #f43f8e 0%, #ec4899 50%, #fb7185 100%);
}

.vs-card-top {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    font-size: 0.75rem;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.95);
    letter-spacing: 0.025em;
}

.vs-card-score {
    font-size: 2.15rem;
    font-weight: 900;
    line-height: 1;
    text-align: center;
    margin: 0.25rem 0;
    color: #ffffff;
    letter-spacing: -0.025em;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
}

.vs-card-badge-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.25rem;
}

.vs-card-badge {
    display: inline-block;
    padding: 0.1rem 1rem;
    border-radius: 9999px;
    background-color: #ffffff;
    color: #1e293b;
    font-size: 0.75rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
    line-height: 1.2;
}
.vs-badge-pink {
    color: #e11d48;
}

.vs-card-badge-label {
    font-size: 0.55rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: rgba(255, 255, 255, 0.85);
    margin-top: 0.125rem;
}

.vs-card-progress-track {
    width: 100%;
    height: 4px;
    border-radius: 9999px;
    background-color: rgba(0, 0, 0, 0.2);
    overflow: hidden;
    margin-top: 0.2rem;
}
.vs-card-progress-fill {
    height: 100%;
    border-radius: 9999px;
    background-color: #ffffff;
    transition: width 0.6s ease-in-out;
}

/* RIGHT COLUMN: CHART */
.vs-col-chart {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.vs-chart-legend {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1.25rem;
    font-size: 0.75rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 0.35rem;
}
.dark .vs-chart-legend {
    color: #cbd5e1;
}

.vs-legend-item {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}
.vs-legend-dot {
    width: 0.55rem;
    height: 0.55rem;
    border-radius: 9999px;
    display: inline-block;
}

.vs-chart-wrapper {
    position: relative;
    width: 100%;
    height: 240px;
    max-height: 240px;
}

/* FOOTER INFO BAR */
.vs-footer-info {
    margin-top: 0.75rem;
    padding: 0.45rem 0.75rem;
    border-radius: 0.5rem;
    border: 1px solid #b2ebf2;
    background-color: #e0f7fa;
    color: #083344;
    font-size: 0.725rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}
.dark .vs-footer-info {
    border-color: rgba(6, 182, 212, 0.3);
    background-color: rgba(6, 182, 212, 0.12);
    color: #a5f3fc;
}

.vs-footer-left {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.35rem;
}
.vs-info-icon {
    width: 0.85rem;
    height: 0.85rem;
    color: #0891b2;
    flex-shrink: 0;
}
.dark .vs-info-icon {
    color: #22d3ee;
}
.vs-info-bold {
    font-weight: 700;
}
.vs-square {
    display: inline-block;
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 2px;
}
.vs-legend-inline {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}
.vs-info-sep {
    color: rgba(8, 145, 178, 0.4);
    margin: 0 0.125rem;
}
.vs-footer-right {
    font-size: 0.65rem;
    font-weight: 600;
    color: #0e7490;
}
.dark .vs-footer-right {
    color: #67e8f9;
}

/* RESPONSIVE ON SMALL MOBILE ONLY */
@media (max-width: 640px) {
    .vs-content-2col {
        flex-direction: column;
    }
    .vs-col-cards {
        flex: 1 1 auto;
        width: 100%;
        flex-direction: row;
    }
    .vs-col-cards .vs-card {
        flex: 1;
    }
    .vs-chart-wrapper {
        height: 200px;
    }
}
</style>

<div 
    x-data="visualisasiSkorData({{ json_encode($chartPayload) }})"
    class="vs-root"
>
    <!-- TOP HEADER BAR: TITLE + MODE SWITCHER BUTTONS -->
    <div class="vs-header">
        <div class="vs-header-left">
            <div class="vs-header-icon">
                <svg style="width: 1.15rem; height: 1.15rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div>
                <h3 class="vs-title">Visualisasi Skor</h3>
                <p class="vs-subtitle">Komparasi Hasil Asesmen Mandiri Peserta vs Asesmen Tim Penguji</p>
            </div>
        </div>

        <!-- TOGGLE BUTTONS (BAR, RADAR, LINE) -->
        <div class="vs-toggle-group">
            <button 
                type="button"
                @click="switchMode('bar')"
                :class="mode === 'bar' ? 'active' : ''"
                class="vs-toggle-btn"
            >
                <svg style="width: 0.85rem; height: 0.85rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h14" />
                </svg>
                Bar
            </button>

            <button 
                type="button"
                @click="switchMode('radar')"
                :class="mode === 'radar' ? 'active' : ''"
                class="vs-toggle-btn"
            >
                <svg style="width: 0.85rem; height: 0.85rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                </svg>
                Radar
            </button>

            <button 
                type="button"
                @click="switchMode('line')"
                :class="mode === 'line' ? 'active' : ''"
                class="vs-toggle-btn"
            >
                <svg style="width: 0.85rem; height: 0.85rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
                Line
            </button>
        </div>
    </div>

    <!-- 2 COLUMNS STRICT LAYOUT: LEFT STAT CARDS + RIGHT CHART -->
    <div class="vs-content-2col">
        
        <!-- KOLOM 1: 2 KARTU SKOR (250px) -->
        <div class="vs-col-cards">
            
            <!-- CARD 1: ASESMEN MANDIRI (ROYAL BLUE GRADIENT) -->
            <div class="vs-card vs-card-mandiri">
                <div class="vs-card-top">
                    <svg style="width: 0.85rem; height: 0.85rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Asesmen Mandiri</span>
                </div>

                <!-- Big Score Number -->
                <div class="vs-card-score">
                    {{ number_format($percentMandiri, 0) }}
                </div>

                <!-- Predikat Badge -->
                <div class="vs-card-badge-wrap">
                    <span class="vs-card-badge">{{ $predikatMandiri }}</span>
                    <span class="vs-card-badge-label">PREDIKAT</span>
                </div>

                <!-- Bottom Progress Bar -->
                <div class="vs-card-progress-track">
                    <div class="vs-card-progress-fill" style="width: {{ min(100, $percentMandiri) }}%;"></div>
                </div>
            </div>

            <!-- CARD 2: ASESMEN EVALUATOR (PINK GRADIENT) -->
            <div class="vs-card vs-card-evaluator">
                <div class="vs-card-top">
                    <svg style="width: 0.85rem; height: 0.85rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Asesmen Evaluator</span>
                </div>

                <!-- Big Score Number with Indonesian Comma Decimal -->
                <div class="vs-card-score">
                    {{ number_format($percentPenguji, 2, ',', '.') }}
                </div>

                <!-- Predikat Badge -->
                <div class="vs-card-badge-wrap">
                    <span class="vs-card-badge vs-badge-pink">{{ $predikatPenguji }}</span>
                    <span class="vs-card-badge-label">PREDIKAT</span>
                </div>

                <!-- Bottom Progress Bar -->
                <div class="vs-card-progress-track">
                    <div class="vs-card-progress-fill" style="width: {{ min(100, $percentPenguji) }}%;"></div>
                </div>
            </div>

        </div>

        <!-- KOLOM 2: GRAFIK (FLEX: 1) -->
        <div class="vs-col-chart">
            
            <!-- Top Chart Series Legend -->
            <div class="vs-chart-legend">
                <div class="vs-legend-item">
                    <span class="vs-legend-dot" style="background-color: #5b86e5;"></span>
                    <span>Asesmen Mandiri</span>
                </div>
                <div class="vs-legend-item">
                    <span class="vs-legend-dot" style="background-color: #f857a6;"></span>
                    <span>Asesmen Evaluator</span>
                </div>
            </div>

            <!-- Canvas Container -->
            <div class="vs-chart-wrapper">
                <canvas x-ref="chartCanvas" style="width: 100%; height: 100%;"></canvas>
            </div>

        </div>

    </div>

    <!-- BOTTOM INFO BAR -->
    <div class="vs-footer-info">
        <div class="vs-footer-left">
            <svg class="vs-info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="vs-info-bold">Keterangan:</span>
            <span class="vs-legend-inline">
                <span class="vs-square" style="background-color: #5b86e5;"></span> Asesmen Mandiri
            </span>
            <span class="vs-legend-inline">
                <span class="vs-square" style="background-color: #f857a6;"></span> Asesmen Evaluator
            </span>
            <span class="vs-info-sep">|</span>
            <span>Klik pada grafik untuk melihat detail nilai</span>
        </div>
        <div class="vs-footer-right">
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
                                    borderRadius: 6,
                                    barPercentage: 0.78,
                                    categoryPercentage: 0.75,
                                },
                                {
                                    label: 'Asesmen Evaluator',
                                    data: this.data.penguji,
                                    backgroundColor: '#f857a6',
                                    borderRadius: 6,
                                    barPercentage: 0.78,
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
                                    padding: 10,
                                    cornerRadius: 6,
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
                                        font: { size: 10 },
                                        callback: (val) => val + '%'
                                    }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: {
                                        color: textColor,
                                        font: { weight: 'bold', size: 10 }
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
                                    pointRadius: 4,
                                    borderWidth: 2
                                },
                                {
                                    label: 'Asesmen Evaluator',
                                    data: this.data.penguji,
                                    borderColor: '#f857a6',
                                    backgroundColor: 'rgba(248, 87, 166, 0.25)',
                                    pointBackgroundColor: '#f857a6',
                                    pointBorderColor: '#ffffff',
                                    pointRadius: 4,
                                    borderWidth: 2
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
                                    offset: 3,
                                    color: textColor,
                                    font: { weight: 'bold', size: 9.5 },
                                    formatter: (val) => val > 0 ? String(val).replace('.', ',') : '0'
                                },
                                tooltip: {
                                    backgroundColor: tooltipBg,
                                    titleColor: '#ffffff',
                                    bodyColor: '#e2e8f0',
                                    padding: 10,
                                    cornerRadius: 6,
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
                                        font: { size: 9 },
                                        callback: (val) => val + '%'
                                    },
                                    grid: { color: gridColor },
                                    angleLines: { color: gridColor },
                                    pointLabels: {
                                        color: textColor,
                                        font: { weight: 'bold', size: 9.5 }
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
                                    pointRadius: 4.5,
                                    borderWidth: 2.5
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
                                    font: { weight: 'bold', size: 9.5 },
                                    formatter: (val) => val > 0 ? String(val).replace('.', ',') : '0'
                                },
                                tooltip: {
                                    backgroundColor: tooltipBg,
                                    titleColor: '#ffffff',
                                    bodyColor: '#e2e8f0',
                                    padding: 10,
                                    cornerRadius: 6,
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
                                        font: { size: 9.5 },
                                        callback: (val) => val + '%'
                                    }
                                },
                                x: {
                                    grid: { color: gridColor },
                                    ticks: {
                                        color: textColor,
                                        font: { weight: 'bold', size: 9.5 }
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
