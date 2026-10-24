<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Uji Kelayakan dan Kepatutan</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm 2cm 1.5cm 2cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }
        
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, Helvetica, sans-serif;
        }
        .kop-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .logo-kop {
            width: 75px;
            height: auto;
        }
        .header-text {
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
        }
        .header-text h3 {
            font-size: 13pt;
            font-weight: normal;
            margin: 0;
            line-height: 1.2;
            text-transform: uppercase;
        }
        .header-text h2 {
            font-size: 15pt;
            font-weight: bold;
            margin: 1px 0 5px 0;
            line-height: 1.15;
            text-transform: uppercase;
        }
        .header-text p {
            font-size: 8.5pt;
            margin: 0;
            line-height: 1.25;
        }
        .kop-garis {
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 2px;
            margin: 6px 0 15px 0;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin-top: 15px;
            margin-bottom: 20px;
            text-transform: uppercase;
            line-height: 1.3;
        }
        .content-p {
            text-align: justify;
            margin-bottom: 12px;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0 20px 0;
        }
        .table-data th, .table-data td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 11pt;
        }
        .table-data th {
            text-align: center;
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .ttd-section {
            width: 100%;
            margin-top: 25px;
        }
        .ttd-header {
            margin-bottom: 15px;
        }
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
        }
        .ttd-table td {
            border: none;
            padding: 6px 0;
            vertical-align: bottom;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <table class="kop-table">
        <tr>
            <td style="width: 15%; text-align: left;">
                <img src="{{ public_path('logo.png') }}" class="logo-kop" alt="Logo">
            </td>
            <td style="width: 70%;" class="header-text">
                <h3>Pemerintah Provinsi Jawa Tengah</h3>
                <h2>Dinas Koperasi, Usaha Kecil<br>dan Menengah</h2>
                <p>Jln. Sisingamangaraja 3A Semarang Kode Pos 50253 Telepon 024-8310556, 8318773</p>
                <p>Faksimili 0248414165 Laman http://dinkop-umkm.jatengprov.go.id</p>
                <p>Surat Elektronik : dinkop@jatengprov.go.id</p>
            </td>
            <td style="width: 15%;">&nbsp;</td>
        </tr>
    </table>
    <div class="kop-garis"></div>

    <!-- JUDUL -->
    <div class="title">
        BERITA ACARA UJI KELAYAKAN DAN KEPATUTAN<br>
        PENGURUS DAN PENGAWAS KOPERASI
    </div>

    @php
        $tglPleno = isset($tanggal_pleno) ? \Carbon\Carbon::parse($tanggal_pleno) : \Carbon\Carbon::now();
        $hariHari = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $namaHari = $hariHari[$tglPleno->format('l')] ?? $tglPleno->translatedFormat('l');
    @endphp

    <p class="content-p">
        Pada hari ini, <strong>{{ $namaHari }}</strong>, tanggal <strong>{{ $tglPleno->locale('id')->translatedFormat('j F Y') }}</strong>, bertempat di Kantor Dinas Koperasi dan UKM Provinsi Jawa Tengah, Jl. Sisingamangaraja No. 3A Kaliwiru, Kec. Candisari, Kota Semarang, Tim Penguji Kelayakan dan Kepatutan telah melaksanakan Uji Kelayakan dan Kepatutan serta Rapat Pleno untuk memberikan penilaian terhadap Pengurus dan Pengawas <strong>{{ optional($koperasi)->nama_koperasi ?? 'KSP/Koperasi' }}</strong> yang berkedudukan di {{ optional($koperasi)->alamat ?? 'Jawa Tengah' }}.
    </p>

    <p class="content-p">
        Berdasarkan penilaian terhadap hasil Uji Kelayakan dan Kepatutan, dengan ini ditetapkan hasil kelulusan terhadap nama-nama Pengurus dan Pengawas <strong>{{ optional($koperasi)->nama_koperasi }}</strong>, sebagai berikut:
    </p>

    <!-- TABEL DENGAN FORMAT PERSIS BA.DOCX -->
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 50%;">Nama</th>
                <th style="width: 25%;">Diuji Sebagai</th>
                <th style="width: 25%;">Hasil Uji</th>
            </tr>
        </thead>
        <tbody>
            @forelse($daftar_peserta as $p)
                <tr>
                    <td>{{ $p->nama_lengkap }}</td>
                    <td style="text-align: center;">{{ $p->jabatan_usulan }}</td>
                    <td style="text-align: center; font-weight: bold;">
                        {{ $p->status_kelulusan === 'LULUS' ? 'Lulus' : 'Tidak Lulus' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; font-style: italic;">Belum ada data peserta uji.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="content-p">
        Demikian berita acara ini dibuat untuk dipergunakan sebagaimana mestinya.
    </p>

    <!-- TANDA TANGAN TIM PENGUJI -->
    <div class="ttd-section">
        <p style="margin-bottom: 5px;">Semarang, {{ $tglPleno->locale('id')->translatedFormat('j F Y') }}</p>
        <p class="ttd-header">Tim Penguji Kelayakan dan Kepatutan bagi Pengurus dan Pengawas Koperasi</p>

        <table class="ttd-table">
            @forelse($tim_penguji as $penguji)
                <tr>
                    <td style="width: 5%; text-align: right; padding-right: 8px;">{{ $loop->iteration }}.</td>
                    <td style="width: 45%;">{{ $penguji->nama_lengkap ?? $penguji->name ?? 'Penguji' }}</td>
                    <td style="width: 50%;">( ……………………………….. )</td>
                </tr>
            @empty
                <tr>
                    <td style="width: 5%;">1.</td>
                    <td style="width: 45%;">Penguji 1</td>
                    <td style="width: 50%;">( ……………………………….. )</td>
                </tr>
                <tr>
                    <td style="width: 5%;">2.</td>
                    <td style="width: 45%;">Penguji 2</td>
                    <td style="width: 50%;">( ……………………………….. )</td>
                </tr>
                <tr>
                    <td style="width: 5%;">3.</td>
                    <td style="width: 45%;">Penguji 3</td>
                    <td style="width: 50%;">( ……………………………….. )</td>
                </tr>
                <tr>
                    <td style="width: 5%;">4.</td>
                    <td style="width: 45%;">Penguji 4</td>
                    <td style="width: 50%;">( ……………………………….. )</td>
                </tr>
            @endforelse
        </table>
    </div>

</body>
</html>