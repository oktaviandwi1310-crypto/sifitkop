<!DOCTYPE html>
<html>
<head>
    <title>SK Kelulusan UKK</title>
    <style>
        body { font-family: 'Times New Roman', serif; margin: 30px; }
        .header { text-align: center; border-bottom: 2px solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .title { text-align: center; font-weight: bold; font-size: 14pt; margin-top: 20px; text-decoration: underline; }
        .subtitle { text-align: center; font-weight: bold; font-size: 12pt; margin-bottom: 20px; }
        .content { font-size: 12pt; line-height: 1.5; text-align: justify; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 10px; }
        .table td { padding: 5px; vertical-align: top; }
        .signature { margin-top: 50px; float: right; text-align: center; width: 300px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0; font-size: 16pt;">PEMERINTAH PROVINSI JAWA TENGAH</h2>
        <h1 style="margin:0; font-size: 18pt;">DINAS KOPERASI, USAHA KECIL DAN MENENGAH</h1>
        <p style="margin:0; font-size: 10pt;">Jl. Sisingamangaraja No.3A, Kaliwiru, Kec. Candisari, Kota Semarang, Jawa Tengah 50232</p>
    </div>

    <div class="title">SURAT KETERANGAN LULUS UJI KELAYAKAN DAN KEPATUTAN</div>
    <div class="subtitle">Nomor: {{ date('Y') }}/UKK/{{ str_pad($peserta->id, 4, '0', STR_PAD_LEFT) }}</div>

    <div class="content">
        <p>Berdasarkan hasil penilaian Uji Kelayakan dan Kepatutan (UKK) yang diselenggarakan oleh Dinas Koperasi dan UKM Provinsi Jawa Tengah berdasarkan Petunjuk Pelaksanaan UKK Nomor 1 Tahun 2024, maka yang bertanda tangan di bawah ini menerangkan bahwa:</p>
        
        <table class="table">
            <tr>
                <td width="30%">Nama Lengkap</td>
                <td width="2%">:</td>
                <td><strong>{{ strtoupper($peserta->nama_lengkap) }}</strong></td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td>{{ $peserta->nik_peserta }}</td>
            </tr>
            <tr>
                <td>Koperasi Asal</td>
                <td>:</td>
                <td>{{ $peserta->koperasi->nama_koperasi }}</td>
            </tr>
            <tr>
                <td>Jabatan Usulan</td>
                <td>:</td>
                <td>{{ strtoupper($peserta->jabatan_usulan) }}</td>
            </tr>
            <tr>
                <td>Total Skor UKK</td>
                <td>:</td>
                <td>{{ $peserta->skor_total }} / 100</td>
            </tr>
        </table>

        <p>Telah mengikuti seluruh tahapan UKK yang meliputi Seleksi Administratif dan Wawancara, serta dinyatakan <strong>LULUS</strong> dan memenuhi syarat untuk diangkat sebagai {{ $peserta->jabatan_usulan }} pada {{ $peserta->koperasi->nama_koperasi }}.</p>

        <p>Demikian Surat Keterangan ini dibuat dengan sesungguhnya untuk dapat dipergunakan sebagaimana mestinya oleh Koperasi yang bersangkutan.</p>
    </div>

    <div class="signature">
        <p>Semarang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <p><strong>Kepala Dinas Koperasi, Usaha Kecil<br>dan Menengah Provinsi Jawa Tengah</strong></p>
        <br><br><br><br>
        <p style="text-decoration: underline; font-weight: bold; margin-bottom: 0;">Eddy Sulistiyo Bramiyanto, S.E., M.M.</p>
        <p style="margin-top: 0;">NIP. 19700318 199603 1 002</p>
    </div>
</body>
</html>
