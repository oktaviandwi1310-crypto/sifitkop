<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Wawancara UKK</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; margin: 2cm; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-8 { margin-bottom: 2rem; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
        th, td { border: 1px solid black; padding: 6px; text-align: left; }
        .no-border td { border: none; padding: 4px; }
        .signature-area { margin-top: 3rem; width: 100%; }
        .signature-box { width: 50%; float: right; text-align: center; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="text-center mb-8">
        <h3 class="mb-2">BERITA ACARA</h3>
        <h4 class="mb-2">UJI KELAYAKAN DAN KEPATUTAN (UKK) KOPERASI</h4>
        <p>Nomor: BA-UKK/{{ date('Y') }}/{{ str_pad($peserta->id, 4, '0', STR_PAD_LEFT) }}</p>
    </div>

    <p>Pada hari ini, tanggal <strong>{{ \Carbon\Carbon::parse($peserta->jadwal_wawancara)->translatedFormat('d F Y') }}</strong>, telah dilaksanakan Uji Kelayakan dan Kepatutan (Wawancara) terhadap calon pengurus/pengawas koperasi:</p>

    <table class="no-border mb-4">
        <tr>
            <td style="width: 30%;">Nama Lengkap</td>
            <td style="width: 5%;">:</td>
            <td><strong>{{ $peserta->nama_lengkap }}</strong></td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>:</td>
            <td>{{ $peserta->nik_peserta }}</td>
        </tr>
        <tr>
            <td>Koperasi Asal</td>
            <td>:</td>
            <td>{{ optional($peserta->koperasi)->nama_koperasi }}</td>
        </tr>
        <tr>
            <td>Jabatan Usulan</td>
            <td>:</td>
            <td>{{ $peserta->jabatan_usulan }}</td>
        </tr>
    </table>

    <p>Dengan hasil penilaian wawancara sebagai berikut:</p>
    <table>
        <thead>
            <tr>
                <th style="width: 10%; text-align: center;">No</th>
                <th>Aspek Penilaian</th>
                <th style="width: 20%; text-align: center;">Skor (1-4)</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="text-center">1</td><td>Visi & Misi Perkoperasian</td><td class="text-center">{{ $peserta->wawancara_1 }}</td></tr>
            <tr><td class="text-center">2</td><td>Pemahaman Regulasi</td><td class="text-center">{{ $peserta->wawancara_2 }}</td></tr>
            <tr><td class="text-center">3</td><td>Kepemimpinan & Etika</td><td class="text-center">{{ $peserta->wawancara_3 }}</td></tr>
            <tr><td class="text-center">4</td><td>Rencana Strategis & Inovasi</td><td class="text-center">{{ $peserta->wawancara_4 }}</td></tr>
            <tr><td class="text-center">5</td><td>Solusi Permasalahan (Problem Solving)</td><td class="text-center">{{ $peserta->wawancara_5 }}</td></tr>
            <tr><td class="text-center">6</td><td>Komitmen & Integritas</td><td class="text-center">{{ $peserta->wawancara_6 }}</td></tr>
            <tr>
                <td colspan="2" class="font-bold text-center">TOTAL SKOR WAWANCARA</td>
                <td class="font-bold text-center">{{ $peserta->skor_wawancara }}</td>
            </tr>
        </tbody>
    </table>
    
    @php
        $sa = \App\Models\SelfAssessment::where('peserta_id', $peserta->id)->first();
        $skorSa = $sa ? ($sa->integritas_score + $sa->reputasi_score + $sa->kompetensi_score + $sa->inovasi_score) : 0;
        $skorTahap1 = $peserta->skor_admin + $skorSa;
        $grandTotal = $skorTahap1 + $peserta->skor_wawancara;
    @endphp

    <h4 class="mb-2">REKAPITULASI PENILAIAN AKHIR</h4>
    <table>
        <tbody>
            <tr>
                <td style="width: 80%;">A. Total Skor Verifikasi Dokumen & Self Assessment (Minimal Lolos = 64)</td>
                <td class="text-center font-bold" style="width: 20%;">{{ $skorTahap1 }}</td>
            </tr>
            <tr>
                <td>B. Total Skor Wawancara Penguji (Minimal Lolos = 18)</td>
                <td class="text-center font-bold">{{ $peserta->skor_wawancara }}</td>
            </tr>
            <tr>
                <td class="font-bold text-right uppercase">Total Kumulatif (A + B)</td>
                <td class="text-center font-bold">{{ $grandTotal }}</td>
            </tr>
        </tbody>
    </table>

    <p class="mb-4">
        Berdasarkan ambang batas kelulusan Juklak (Total Kumulatif &ge; 82), maka peserta dinyatakan: <br>
        <strong>&raquo; {{ strtoupper($peserta->status_kelulusan ?? 'BELUM DITETAPKAN') }} &laquo;</strong>
    </p>

    <p>Demikian Berita Acara ini dibuat dengan sebenar-benarnya untuk dipergunakan sebagaimana mestinya.</p>

    <div class="signature-area clearfix">
        <div class="signature-box">
            <p>Semarang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p class="mb-8">Tim Penguji / Asesor</p>
            <br><br><br>
            <p class="font-bold underline">{{ optional($peserta->penguji)->name ?? '_______________________' }}</p>
        </div>
    </div>
</body>
</html>
