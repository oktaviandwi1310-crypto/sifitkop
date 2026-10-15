<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class OdsApiService
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        // Menggunakan env() secara langsung untuk kemudahan setup, 
        // pastikan menambahkan variabel ini di file .env Anda nanti.
        $this->baseUrl = env('ODS_API_BASE_URL', 'https://nik.kop.go.id/odsapi');
        $this->username = env('ODS_API_USERNAME', 'jateng');
        $this->password = env('ODS_API_PASSWORD', 'jateng@717');
    }

    /**
     * Mendapatkan instance HTTP Client dasar dengan User-Agent agar tidak diblokir oleh WAF Kemenkop.
     */
    protected function getClient()
    {
        return Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept' => 'application/json'
        ]);
    }

    /**
     * Melakukan HTTP Basic Auth login untuk mendapatkan Bearer Token ODS.
     * Menggunakan Redis/File Cache (3600 detik) agar tidak perlu request login berulang kali (Optimasi).
     */
    public function getToken(): ?string
    {
        if (Cache::has('ods_api_token')) {
            return Cache::get('ods_api_token');
        }

        $response = $this->getClient()->withBasicAuth($this->username, $this->password)
            ->get($this->baseUrl . '/odslogin');

        if ($response->successful()) {
            $token = $response->json('token');
            if ($token) {
                Cache::put('ods_api_token', $token, 3600);
                return $token;
            }
        }

        return null;
    }

    /**
     * Mengambil detail Koperasi berdasarkan NIK.
     * Digunakan saat fitur Auto-Fill pendaftaran UKK (Modul 1).
     * 
     * @param string $nik
     * @return array
     */
    public function getDetail(string $nik): array
    {
        $token = $this->getToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Koneksi ke server ODS Kemenkop gagal (Autentikasi Ditolak).'
            ];
        }

        /* 
         * Catatan Teknis: 
         * Walaupun HTTP GET biasanya tidak memiliki body, Postman collection ODS mensyaratkan 
         * body JSON berisi NIK. Metode send('GET') pada Laravel HTTP Client mengakomodasi hal ini.
         */
        $response = $this->getClient()->withToken($token)
            ->send('GET', $this->baseUrl . '/odsdetail', [
                'json' => [
                    'nik' => $nik
                ]
            ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'data' => $response->json()
            ];
        }

        return [
            'success' => false,
            'message' => 'Data Koperasi dengan NIK tersebut tidak ditemukan di ODS.',
            'error_code' => $response->status()
        ];
    }

    /**
     * Mengambil daftar rekap koperasi ODS berdasarkan tahun.
     */
    public function getRekap(string $tahun): array
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Gagal autentikasi ke ODS', 'data' => []];
        }

        $response = $this->getClient()->withToken($token)
            ->send('GET', $this->baseUrl . '/odsrekap', [
                'json' => ['tahun' => $tahun]
            ]);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }

        return ['success' => false, 'data' => []];
    }

    /**
     * Mengambil profil lengkap seluruh koperasi (Digunakan untuk lihat detail).
     * Di-cache selama 1 jam agar tidak memberatkan server Kemenkop (karena payload sangat besar).
     */
    public function getProfile(): array
    {
        ini_set('memory_limit', '-1');

        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'data' => []];
        }

        // Beralih ke File System Caching ketimbang Database Caching untuk menghindari MySQL Packet Error
        $cacheFile = 'ods_profile_cache.json';
        $disk = \Illuminate\Support\Facades\Storage::disk('local');

        // Cek apakah file cache ada dan umurnya belum 1 jam (3600 detik)
        if ($disk->exists($cacheFile) && (time() - $disk->lastModified($cacheFile)) < 3600) {
            $jsonData = $disk->get($cacheFile);
            return ['success' => true, 'data' => json_decode($jsonData, true) ?? []];
        }

        // Jika cache tidak ada / kadaluarsa, tembak API ODS
        $response = $this->getClient()->timeout(120)->withToken($token)->get($this->baseUrl . '/odsprofile');
        
        if ($response->successful()) {
            $data = $response->json('data') ?? [];
            
            // Simpan JSON utuh ke Storage/app agar tidak masuk ke MySQL
            $disk->put($cacheFile, json_encode($data));
            
            return ['success' => true, 'data' => $data];
        }

        return ['success' => false, 'data' => []];
    }
}
