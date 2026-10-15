# PANDUAN WORKFLOW GIT & KOLABORASI TIM (SIFITKOP)

Dokumen ini adalah panduan standar operasional (SOP) pengelolaan kode dan kolaborasi Git pada proyek **SIFITKOP** menggunakan repository GitHub:  
👉 `https://github.com/oktaviandwi1310-crypto/sifitkop`

---

## 📌 DAFTAR ISI
1. [Aturan Emas (Golden Rules)](#1-aturan-emas-golden-rules)
2. [Skenario A: Rekan Ada Update di GitHub, Anda Ingin Menerapkannya ke Komputer Anda](#2-skenario-a-rekan-ada-update-di-github-anda-ingin-menerapkannya)
3. [Skenario B: Anda Membuat Perubahan Lokal dan Ingin Mengunggah ke GitHub](#3-skenario-b-anda-membuat-perubahan-lokal-dan-ingin-mengunggah-ke-github)
4. [Skenario C: Menangani & Menyelesaikan Konflik (Merge Conflict)](#4-skenario-c-menangani--menyelesaikan-konflik-merge-conflict)
5. [Skenario D: Mengamankan Pekerjaan Sementara dengan Git Stash](#5-skenario-d-mengamankan-pekerjaan-sementara-dengan-git-stash)
6. [Praktik Terbaik: Menggunakan Fitur Branch (Pull Request)](#6-praktik-terbaik-menggunakan-fitur-branch)
7. [Tabel Rangkuman Perintah Cepat (Cheatsheet)](#7-tabel-rangkuman-perintah-cepat-cheatsheet)

---

## 1. ATURAN EMAS (GOLDEN RULES)

1. **Selalu PULL sebelum mulai bekerja:**  
   Setiap awal sesi kerja (misal pagi hari), selalu jalankan `git pull` agar kode di laptop Anda selalu yang paling baru.
2. **Jangan commit file sensitif:**  
   File `.env`, folder `vendor/`, `node_modules/`, file `.zip` berukuran besar, dan folder upload user (`storage/app/public/*`) tidak boleh di-commit. Pastikan selalu terdaftar di `.gitignore`.
3. **Pesan Commit Jelas & Spesifik:**  
   Gunakan pesan yang menjelaskan apa yang diubah, contoh:  
   - ✅ `git commit -m "feat: Tambah filter status di rekap koperasi"`  
   - ❌ `git commit -m "update"` atau `git commit -m "fix"`
4. **Komunikasi antar tim:**  
   Jika hendak mengubah file inti (seperti file migrasi lama, konfigurasi database, atau provider utama), komunikasikan dengan rekan terlebih dahulu.

---

## 2. SKENARIO A: Rekan Ada Update di GitHub, Anda Ingin Menerapkannya

Lakukan langkah ini jika rekan Anda baru saja mengabari: *"Saya sudah push fitur baru / perbaikan ke GitHub"*.

### Langkah-langkah:
Masuk ke terminal di folder `sifitkop-web`:
```bash
cd /Users/macbook/Applications/sifitkop/sifitkop-web
```

#### Langkah 1: Pastikan file lokal Anda bersih
```bash
git status
```
*Jika ada perubahan lokal Anda yang belum disimpan, simpan dulu (commit) atau amankan via `git stash` (lihat Bagian 5).*

#### Langkah 2: Tarik perubahan dari GitHub
```bash
git pull origin main
```

#### Langkah 3: Eksekusi Perintah Wajib Laravel (Pasca-Pull)
Setiap selesai menarik kode dari rekan, selalu lakukan langkah pemeriksaan ini:

1. **Cek migrasi database baru:**
   ```bash
   php artisan migrate
   ```
   *(Penting jika rekan menambahkan tabel/kolom baru di database).*

2. **Cek dependensi PHP baru (jika `composer.json` berubah):**
   ```bash
   composer install
   ```

3. **Cek dependensi JavaScript/CSS baru (jika `package.json` berubah):**
   ```bash
   npm install && npm run build
   ```

4. **Bersihkan cache Laravel & Filament:**
   ```bash
   php artisan optimize:clear
   ```

---

## 3. SKENARIO B: Anda Membuat Perubahan Lokal dan Ingin Mengunggah ke GitHub

Lakukan langkah ini setelah Anda selesai coding suatu fitur atau perbaikan.

### Langkah-langkah:

#### Langkah 1: Cek file apa saja yang Anda ubah
```bash
git status
```

#### Langkah 2: Masukkan file ke staging area
Jika ingin menambahkan semua file yang diubah:
```bash
git add .
```
*Atau jika hanya file tertentu:*
```bash
git add app/Filament/Pages/RekapOdsKoperasi.php
```

#### Langkah 3: Buat Commit dengan pesan deskriptif
```bash
git commit -m "Menambahkan kolom rekap per kecamatan dan ekspor excel"
```

#### Langkah 4: Tarik update terbaru sebelum Anda push (Wajib!)
Langkah ini memastikan bahwa tidak ada kode rekan yang terlewat:
```bash
git pull origin main
```
*(Jika tidak ada konflik, Git akan otomatis menggabungkannya).*

#### Langkah 5: Unggah (Push) ke GitHub
```bash
git push origin main
```

---

## 4. SKENARIO C: Menangani & Menyelesaikan Konflik (Merge Conflict)

Konflik terjadi jika **Anda dan rekan sama-sama mengedit file yang sama pada baris yang sama** lalu melakukan push.

Saat Anda menjalankan `git pull origin main`, terminal akan menampilkan pesan:
```text
CONFLICT (content): Merge conflict in app/Filament/...
Automatic merge failed; fix conflicts and then commit the result.
```

### Cara Menyelesaikan Konflik:

#### 1. Cek file yang berkonflik:
```bash
git status
```
File yang bertanda `both modified` adalah file yang harus diperbaiki.

#### 2. Buka file tersebut di text editor / IDE:
Cari tanda konflik berikut di dalam file:
```text
<<<<<<< HEAD
// Ini kode versi Anda di komputer lokal
$status = 'Draft';
=======
// Ini kode versi rekan Anda yang diambil dari GitHub
$status = 'Pending';
>>>>>>> origin/main
```

#### 3. Tentukan pilihan yang benar:
- Hapus tanda `<<<<<<< HEAD`, `=======`, dan `>>>>>>> origin/main`.
- Pilih kode yang benar (atau kombinasikan keduanya jika memang saling melengkapi).
- Contoh hasil akhir yang bersih:
  ```text
  $status = 'Pending';
  ```

#### 4. Simpan file, lalu commit penyelesaiannya:
```bash
git add .
git commit -m "fix: Selesaikan merge conflict pada modul penilaian"
git push origin main
```

---

## 5. SKENARIO D: Mengamankan Pekerjaan Sementara dengan Git Stash

Gunakan `git stash` jika rekan Anda mendesak minta Anda pull perubahan mereka sekarang, tetapi codingan Anda saat ini **belum selesai** dan belum siap di-commit.

```bash
# 1. Simpan sementara perubahan yang belum selesai ke 'laci rahasia'
git stash

# 2. Tarik kode rekan dari GitHub dengan aman
git pull origin main

# 3. Keluarkan kembali pekerjaan Anda yang tadi disimpan
git stash pop
```
*Pekerjaan Anda akan kembali ada di workspace tanpa hilang sama sekali.*

---

## 6. PRAKTIK TERBAIK: Menggunakan Fitur Branch (Fitur Baru / Eksperimen)

Untuk menghindari tabrakan kerja langsung di branch `main`, gunakan alur Branching:

```bash
# 1. Buat dan pindah ke branch baru untuk fitur Anda
git checkout -b fitur-rekap-baru

# 2. Bekerja dan coding seperti biasa...
git add .
git commit -m "feat: Selesaikan tampilan grafik rekap"

# 3. Push branch Anda ke GitHub
git push -u origin fitur-rekap-baru

# 4. Buka GitHub repository di browser:
#    Buat Pull Request (PR) dari 'fitur-rekap-baru' ke 'main'.
#    Rekan Anda bisa mereview, lalu klik 'Merge Pull Request'.

# 5. Setelah selesai di-merge di GitHub, kembali ke main lokal:
git checkout main
git pull origin main
```

---

## 7. TABEL RANGKUMAN PERINTAH CEPAT (CHEATSHEET)

| Kebutuhan / Situasi | Perintah Terminal |
|---|---|
| **Cek status file** | `git status` |
| **Tarik perubahan rekan** | `git pull origin main` |
| **Simpan semua perubahan** | `git add .` lalu `git commit -m "pesan perubahan"` |
| **Unggah ke GitHub** | `git push origin main` |
| **Jalankan migrasi database baru** | `php artisan migrate` |
| **Bersihkan semua cache** | `php artisan optimize:clear` |
| **Simpan sementara tanpa commit** | `git stash` |
| **Ambil kembali simpanan sementara** | `git stash pop` |
| **Lihat riwayat commit terakhir** | `git log -n 5 --oneline` |
| **Buat branch baru** | `git checkout -b nama-branch-baru` |
| **Pindah kembali ke branch main** | `git checkout main` |

---

*Dokumen ini dibuat secara otomatis pada 15 Oktober 2026 untuk standarisasi alur pengembangan SIFITKOP.*
