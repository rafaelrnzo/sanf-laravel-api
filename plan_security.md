# 🔐 Security Fix Implementation Plan (Backend-Only)
**Scope:** Backend/Server-side only — Zero Breaking-Changes untuk Mobile Developer  
**Referensi:** Laporan Pentest A.1 (V1–V4)  
**Tanggal:** 30 April 2026  

---

## Daftar Isi

1. [IDOR & Missing Auth Middleware (A.1.V1)](#1-idor--missing-auth-middleware)
2. [Insecure Auth & Brute-Force Mitigation (A.1.V2)](#2-insecure-auth--brute-force-mitigation)
3. [SQL Injection — Product Search (High)](#3-sql-injection--product-search)
4. [File Upload Extension Bypass (Medium)](#4-file-upload-extension-bypass)
5. [Global Security Headers (High)](#5-global-security-headers)
6. [Excluded Items (Butuh Tim Mobile)](#excluded-items)
7. [Open Questions](#open-questions)
8. [Verification Plan](#verification-plan)

---

## 1. IDOR & Missing Auth Middleware

**Finding:** A.1.V1  
**Status:** ⚠️ Sebagian selesai (injeksi kepemilikan di level Service)  
**Tujuan:** Setiap endpoint profil hanya dapat diakses oleh pemilik aslinya.

### File yang Dimodifikasi

**`packages/sanf/api/src/routes.php`**

```php
// [PROMPT IMPLEMENTASI]
// TASK: Sisipkan middleware 'auth' dan 'pic' secara tegas pada routing group v1
// yang mencakup endpoint data pengguna (profil, akun, dll).
//
// SEBELUM (contoh pola yang rentan):
Route::prefix('v1')->group(function () {
    Route::get('/profile/{id}', [ProfileController::class, 'show']);
    Route::put('/profile/{id}', [ProfileController::class, 'update']);
});

// SESUDAH (yang harus diterapkan):
Route::prefix('v1')->middleware(['auth:sanctum', 'pic'])->group(function () {
    Route::get('/profile/{id}', [ProfileController::class, 'show']);
    Route::put('/profile/{id}', [ProfileController::class, 'update']);
    // Semua route yang mengakses data pengguna harus berada di dalam group ini
});

// CATATAN PENTING:
// - Middleware 'pic' bertanggung jawab memverifikasi bahwa user yang login
//   adalah pemilik resource yang diminta (owner check).
// - Pastikan middleware 'pic' sudah terdaftar di $routeMiddleware di Kernel.php.
// - Lakukan audit menyeluruh: semua endpoint /profile, /account, /user/*
//   harus masuk ke dalam group middleware ini.
```

### Checklist Implementasi

- [ ] Identifikasi semua route yang menyentuh data profil/pengguna
- [ ] Bungkus semua route tersebut dalam `middleware(['auth:sanctum', 'pic'])`
- [ ] Verifikasi middleware `pic` sudah ada dan berfungsi sebagai ownership check
- [ ] Jalankan `php artisan route:list` untuk konfirmasi middleware terpasang

---

## 2. Insecure Auth & Brute-Force Mitigation

**Finding:** A.1.V2  
**Strategi:** Account Lockout via API Throttling (tanpa perubahan UI Mobile)  
**Batas:** `throttle:5,1` — maksimal 5 request per menit per IP/user

### File yang Dimodifikasi

**`packages/sanf/api/src/routes.php`**

```php
// [PROMPT IMPLEMENTASI]
// TASK: Terapkan throttle middleware pada endpoint login dan verifikasi PIN.
//
// IMPLEMENTASI:
Route::prefix('v1')->group(function () {

    // Endpoint Login — batasi 5x percobaan per menit
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');

    // Endpoint Verifikasi PIN — batasi 5x percobaan per menit
    Route::post('/auth/verify-pin', [AuthController::class, 'verifyPin'])
        ->middleware('throttle:5,1');

    // Jika ada endpoint reset PIN, tambahkan juga:
    Route::post('/auth/reset-pin', [AuthController::class, 'resetPin'])
        ->middleware('throttle:5,1');
});

// PENJELASAN throttle:5,1:
// - Angka pertama (5)  = maksimal 5 request yang diizinkan
// - Angka kedua  (1)  = dalam jendela waktu 1 menit
// - Setelah limit tercapai, server otomatis mengembalikan HTTP 429 Too Many Requests
// - Laravel secara otomatis menyertakan header Retry-After pada respons 429
```

**`bootstrap/app.php`**

```php
// [PROMPT IMPLEMENTASI]
// TASK: Pastikan ThrottleRequests middleware terdaftar secara global
// atau tersedia sebagai route middleware alias.
//
// Verifikasi di app/Http/Kernel.php bahwa alias berikut sudah ada:
protected $middlewareAliases = [
    // ... middleware lainnya ...
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
];

// Jika menggunakan Laravel 10+ dengan bootstrap/app.php baru:
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    ]);
})
```

### ⚠️ Open Question #1
> **Apakah batas 5x per menit sudah rasional untuk sistem Anda?**  
> Pertimbangan: Jika user nyata bisa salah input PIN berkali-kali dalam waktu singkat (misalnya di kondisi jaringan buruk dengan retry otomatis), batas ini mungkin perlu disesuaikan. Diskusikan dengan tim produk sebelum deploy ke production.

### Checklist Implementasi

- [ ] Tambahkan `throttle:5,1` pada route `/auth/login`
- [ ] Tambahkan `throttle:5,1` pada route `/auth/verify-pin`
- [ ] Audit semua endpoint auth lainnya (reset-pin, change-pin, dll)
- [ ] Pastikan alias `throttle` terdaftar di Kernel/bootstrap
- [ ] Test: kirim 6 request berturut-turut → harus dapat HTTP 429 pada request ke-6

---

## 3. SQL Injection — Product Search

**Severity:** 🔴 High  
**Tujuan:** Menghentikan injeksi via query parameter pencarian dengan mengganti raw string concatenation ke parameter binding.

### File yang Dimodifikasi

**`packages/sanf/core/src/Modules/Product/ListProductService.php`**

```php
// [PROMPT IMPLEMENTASI]
// TASK: Hapus pembuatan string SQL secara mentah pada filter title.
//
// SEBELUM (VULNERABLE — SQL Injection):
$filter += ['title' => "title like '%{$dto->title}%'"];
// ❌ Input user langsung diinterpolasi ke dalam string SQL tanpa sanitasi.
// Contoh exploit: title = "' OR '1'='1" akan memanipulasi query.

// SESUDAH (AMAN — serahkan filtering ke Repository layer):
// Hapus baris di atas. Cukup teruskan nilai title mentah ke Repository:
$filter['title'] = $dto->title; // nilai bersih, tanpa SQL fragment
// Repository yang bertanggung jawab untuk binding parameter.

// CATATAN:
// - Pastikan DTO melakukan sanitasi/validasi dasar (strip HTML tags, trim whitespace)
//   sebelum nilai diteruskan ke Service.
// - Jangan pernah membangun fragment SQL di layer Service atau Controller.
```

**`packages/sanf/core/src/Modules/Product/EloquentProductRepository.php`**

```php
// [PROMPT IMPLEMENTASI]
// TASK: Ganti whereRaw dengan parameter binding native Eloquent.
//
// SEBELUM (VULNERABLE):
$query->whereRaw("title like '%{$title}%'");
// ❌ whereRaw dengan interpolasi string = SQL Injection risk.

// SESUDAH (AMAN):
$query->where('title', 'ilike', '%' . $title . '%');
// ✅ Eloquent secara otomatis melakukan PDO parameter binding.
// Nilai $title tidak akan pernah dieksekusi sebagai SQL.

// CATATAN UNTUK PostgreSQL vs MySQL:
// - PostgreSQL: gunakan 'ilike' untuk case-insensitive search
// - MySQL     : gunakan 'like' (MySQL LIKE sudah case-insensitive by default
//               tergantung collation)
// Sesuaikan dengan database engine yang digunakan sistem.

// ALTERNATIF (jika perlu full-text search yang lebih canggih):
$query->where('title', 'like', '%' . addcslashes($title, '%_\\') . '%');
// addcslashes mencegah wildcard injection pada karakter % dan _ dalam input user.
```

### Checklist Implementasi

- [ ] Hapus interpolasi string SQL di `ListProductService.php`
- [ ] Ganti `whereRaw` dengan `where('title', 'ilike', ...)` di `EloquentProductRepository.php`
- [ ] Audit seluruh codebase: cari pola `whereRaw`, `DB::raw`, dan string interpolasi dalam query
- [ ] Test: input `' OR '1'='1` pada parameter pencarian → harus return 0 hasil, bukan semua data

---

## 4. File Upload Extension Bypass

**Severity:** 🟡 Medium  
**Tujuan:** Memblokir unggahan file berbahaya dengan validasi ekstensi statis di sisi server.

### File yang Dimodifikasi

**`packages/sanf/api/src/Modules/Asset/AssetFileController.php`**

```php
// [PROMPT IMPLEMENTASI]
// TASK: Tambahkan rule validasi mimes pada method upload/store.
//
// IMPLEMENTASI:
public function store(Request $request): JsonResponse
{
    $request->validate([
        'file' => [
            'required',
            'file',
            'mimes:jpg,jpeg,png,pdf',  // ← TAMBAHKAN INI
            'max:10240',               // Opsional: batasi ukuran file (10MB)
        ],
    ]);

    // ... logika upload selanjutnya ...
}

// PENJELASAN:
// - 'mimes:jpg,jpeg,png,pdf' memvalidasi MIME type berdasarkan konten file
//   (bukan hanya ekstensi nama file), sehingga bypass via rename ekstensi dicegah.
// - Laravel menggunakan finfo_file() di balik layar untuk deteksi MIME type aktual.
```

**`packages/sanf/api/src/Modules/Asset/ProfileAssetController.php`**

```php
// [PROMPT IMPLEMENTASI]
// TASK: Terapkan validasi yang sama pada ProfileAssetController.
//
// IMPLEMENTASI (sama seperti AssetFileController):
public function update(Request $request): JsonResponse
{
    $request->validate([
        'avatar' => [
            'required',
            'file',
            'mimes:jpg,jpeg,png',  // Profil foto hanya butuh gambar (tanpa pdf)
            'max:2048',            // Batasi 2MB untuk foto profil
        ],
    ]);

    // ... logika upload selanjutnya ...
}
```

### ⚠️ Open Question #2
> **Apakah ada ekstensi file lain yang dibutuhkan untuk alur pembiayaan/asuransi?**  
> Kandidat yang perlu dikonfirmasi: `.docx`, `.zip`, `.xls`, `.xlsx`  
> Tambahkan ke rule `mimes` hanya setelah dikonfirmasi kebutuhan bisnisnya.  
> Contoh jika disetujui: `'mimes:jpg,jpeg,png,pdf,docx,xlsx'`

### Checklist Implementasi

- [ ] Tambahkan validasi `mimes:jpg,jpeg,png,pdf` di `AssetFileController.php`
- [ ] Tambahkan validasi `mimes:jpg,jpeg,png` di `ProfileAssetController.php`
- [ ] Konfirmasi apakah ada controller upload lain yang belum divalidasi
- [ ] Test: upload file `.php`, `.exe`, `.svg` → harus ditolak dengan HTTP 422
- [ ] Test: upload file gambar valid → harus tetap berhasil

---

## 5. Global Security Headers

**Severity:** 🔴 High  
**Tujuan:** Melampirkan security headers pada setiap respons HTTP untuk mencegah clickjacking, MIME sniffing, dan memaksa HTTPS.

### File yang Dimodifikasi

**`packages/sanf/core/src/Middleware/SecurityHeadersMiddleware.php`**

```php
// [PROMPT IMPLEMENTASI]
// TASK: Buat atau lengkapi SecurityHeadersMiddleware dengan header berikut.
//
// IMPLEMENTASI PENUH:
<?php

namespace Sanf\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Mencegah MIME type sniffing oleh browser
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Mencegah halaman dimuat dalam iframe (proteksi clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Memaksa koneksi HTTPS untuk 1 tahun ke depan (termasuk subdomain)
        $response->headers->set(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains'
        );

        // Opsional tapi direkomendasikan:
        // Kontrol referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Nonaktifkan fitur browser yang tidak diperlukan
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        return $response;
    }
}
```

**`bootstrap/app.php`**

```php
// [PROMPT IMPLEMENTASI]
// TASK: Daftarkan SecurityHeadersMiddleware sebagai global middleware
// agar aktif di setiap request tanpa terkecuali.
//
// Untuk Laravel 10+ (bootstrap/app.php style baru):
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(
        \Sanf\Core\Middleware\SecurityHeadersMiddleware::class
    );
})

// Untuk Laravel 9 ke bawah (app/Http/Kernel.php):
protected $middleware = [
    // ... middleware global lainnya ...
    \Sanf\Core\Middleware\SecurityHeadersMiddleware::class,
];

// CATATAN:
// - Gunakan append() bukan prepend() agar header ditambahkan setelah
//   response body terbentuk.
// - Verifikasi header aktif dengan: curl -I https://yourdomain.com/api/v1/health
```

### Checklist Implementasi

- [ ] Buat/lengkapi class `SecurityHeadersMiddleware` dengan 3 header utama
- [ ] Daftarkan sebagai global middleware di `bootstrap/app.php` atau `Kernel.php`
- [ ] Verifikasi dengan `curl -I` bahwa header muncul di setiap response
- [ ] Pastikan header HSTS tidak diaktifkan di environment lokal/development (bisa menyebabkan masalah)

---

## Excluded Items

Item berikut **tidak termasuk** dalam sprint ini karena membutuhkan koordinasi dengan tim Mobile Developer:

| # | Item | Alasan Exclusion |
|---|------|-----------------|
| 1 | **MFA / OTP Login** | Butuh desain UI popup input SMS/Email di aplikasi Mobile |
| 2 | **A.1.V3 — Root Detection Bypass** | Perbaikan sejati ada di modul Kotlin, deteksi `su` binary, dan integrasi Google Play Integrity SDK |
| 3 | **A.1.V4 — SSL Pinning Bypass** | Wajib dieksekusi via `network_security_config.xml` dan OkHttp module di Android |

---

## Open Questions

### ❓ Q1 — Batas Throttle Rate
> Standar yang diusulkan: **5 request per menit** untuk endpoint login dan verifikasi PIN.  
> **Pertanyaan:** Apakah ini rasional untuk sistem Anda?  
> Pertimbangkan kondisi jaringan mobile yang tidak stabil dan kemungkinan retry otomatis dari SDK.

**Opsi Alternatif:**

| Batas | Kasus Penggunaan |
|-------|-----------------|
| `throttle:3,1` | Sistem high-security (fintech, perbankan) |
| `throttle:5,1` | **Rekomendasi saat ini** — keseimbangan keamanan dan UX |
| `throttle:10,5` | Sistem dengan toleransi UX lebih tinggi |

### ❓ Q2 — Ekstensi File Upload
> Saat ini hanya diizinkan: `jpg`, `jpeg`, `png`, `pdf`  
> **Pertanyaan:** Apakah alur pembiayaan/asuransi memerlukan ekstensi tambahan?  
> Kandidat: `.docx`, `.xlsx`, `.zip`  
> **Konfirmasi diperlukan sebelum menambahkan** ke rule validasi.

---

## Verification Plan

### 1. Automated Syntax Check
```bash
# Cek sintaks PHP di semua file yang dimodifikasi
php -l packages/sanf/api/src/routes.php
php -l bootstrap/app.php
php -l packages/sanf/core/src/Modules/Product/ListProductService.php
php -l packages/sanf/core/src/Modules/Product/EloquentProductRepository.php
php -l packages/sanf/api/src/Modules/Asset/AssetFileController.php
php -l packages/sanf/api/src/Modules/Asset/ProfileAssetController.php
php -l packages/sanf/core/src/Middleware/SecurityHeadersMiddleware.php
```

### 2. Route & Middleware Verification
```bash
# Verifikasi middleware terpasang pada route yang benar
php artisan route:list --path=v1 | grep -E "auth|throttle|pic"

# Verifikasi tidak ada route profil yang lolos tanpa middleware
php artisan route:list --path=profile
```

### 3. Security Header Check
```bash
# Verifikasi header muncul di response (ganti URL sesuai environment)
curl -I https://staging.yourdomain.com/api/v1/health

# Yang harus terlihat:
# X-Content-Type-Options: nosniff
# X-Frame-Options: SAMEORIGIN
# Strict-Transport-Security: max-age=31536000; includeSubDomains
```

### 4. Manual Functional Testing

| Test Case | Input | Expected Output |
|-----------|-------|-----------------|
| SQL Injection | `title=' OR '1'='1` | HTTP 200, 0 results |
| Brute Force PIN | 6x request dalam 1 menit | Request ke-6 → HTTP 429 |
| File Upload Bypass | Upload file `.php` | HTTP 422 Unprocessable |
| File Upload Bypass | Upload file `.exe` | HTTP 422 Unprocessable |
| File Upload Valid | Upload file `.jpg` | HTTP 200, upload sukses |
| IDOR Check | Akses profil user lain | HTTP 403 Forbidden |

### 5. Code Diff Review
- [ ] Konfirmasi `whereRaw` sudah tidak ada di `EloquentProductRepository.php`
- [ ] Konfirmasi interpolasi string `{$dto->title}` sudah tidak ada di `ListProductService.php`
- [ ] Konfirmasi `throttle:5,1` terpasang di kedua route auth
- [ ] Konfirmasi `SecurityHeadersMiddleware` terdaftar sebagai global middleware

---

*Dokumen ini dibuat sebagai panduan implementasi teknis. Setiap perubahan wajib direview melalui pull request sebelum merge ke branch production.*