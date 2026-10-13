<#
.SYNOPSIS
    Skrip setup otomatis untuk Aplikasi Driver Monitoring System (DMS) - Kode Tim IF3-PD15.

.DESCRIPTION
    Mengotomatiskan seluruh proses preparation environment di komputer baru:
      1. Memeriksa kebutuhan perangkat lunak (PHP, Composer, Node.js, Python 3.13, MySQL)
      2. Memasang dependensi aplikasi Laravel
      3. Menyiapkan file .env
      4. Membuat database MySQL dan menjalankan migrasi
      5. Memasang dependensi frontend (npm)
      6. Menyiapkan virtual environment + paket Python untuk AI Service
      7. Mengunduh model MediaPipe yang diperlukan

    Skrip aman dijalankan berulang kali (idempoten). Langkah yang sudah
    selesai akan dilewati secara otomatis.

.PARAMETER SkipAi
    Lewati persiapan AI Service (berguna jika hanya ingin menyiapkan bagian Laravel).

.PARAMETER SkipDb
    Lewati pembuatan database dan migrasi.

.EXAMPLE
    .\setup.ps1

.EXAMPLE
    .\setup.ps1 -SkipAi

.NOTES
    Kebutuhan: PowerShell 5.1+, PHP 8.3+, Composer 2, Node.js 20+,
                Python 3.13 (bukan 3.14), MySQL 8 (bawaan Laragon).
#>

param(
    [switch]$SkipAi,
    [switch]$SkipDb
)

# ---------------------------------------------------------------------------
# Konfigurasi
# ---------------------------------------------------------------------------
$ErrorActionPreference = 'Continue'

$Database  = 'pbl_dms'
$DbUser    = 'root'
$DbPass    = ''
$DbPort    = 3306
$PythonVer = '3.13'

$RootPath   = Split-Path -Parent $MyInvocation.MyCommand.Path
$AiPath     = Join-Path $RootPath 'ai-service'
$TotalSteps = if ($SkipAi) { 5 } else { 6 }

# ---------------------------------------------------------------------------
# Fungsi bantu
# ---------------------------------------------------------------------------
$script:StepNumber  = 0
$script:FailedCount = 0

function Write-Step([string]$Text) {
    $script:StepNumber++
    Write-Host ''
    Write-Host ("=" * 68) -ForegroundColor DarkCyan
    Write-Host (" LANGKAH {0}/{1}: {2}" -f $script:StepNumber, $TotalSteps, $Text) -ForegroundColor Cyan
    Write-Host ("=" * 68) -ForegroundColor DarkCyan
}

function Write-Ok      ([string]$Text) { Write-Host "  [OK]    $Text" -ForegroundColor Green }
function Write-Warn    ([string]$Text) { Write-Host "  [LEWATI] $Text" -ForegroundColor Yellow }
function Write-Info    ([string]$Text) { Write-Host "  [i]     $Text" -ForegroundColor Gray }
function Write-Fail    ([string]$Text) {
    Write-Host "  [GAGAL] $Text" -ForegroundColor Red
    $script:FailedCount++
}

function Test-Command([string]$Name) {
    return [bool](Get-Command $Name -ErrorAction SilentlyContinue)
}

function Get-VersionString([string]$Command, [string[]]$Arguments = @('--version')) {
    try {
        return (& $Command @Arguments 2>&1 | Select-Object -First 1 | Out-String).Trim()
    } catch {
        return 'tidak diketahui'
    }
}

function Find-MySqlExe {
    if (Test-Command 'mysql') { return (Get-Command 'mysql').Source }

    $roots = @('C:\laragon\bin\mysql', 'C:\xampp\mysql', 'C:\Program Files\MySQL')
    foreach ($root in $roots) {
        if (Test-Path $root) {
            $found = Get-ChildItem $root -Recurse -Filter 'mysql.exe' -ErrorAction SilentlyContinue |
                     Where-Object { $_.FullName -match '\\bin\\mysql\.exe$' } |
                     Select-Object -First 1
            if ($found) { return $found.FullName }
        }
    }
    return $null
}

function Get-PythonExe([string]$Version) {
    # 1) Python Launcher (cara paling aman & prinsipial)
    if (Test-Command 'py') {
        $exe = (& py "-$Version" -c 'import sys; print(sys.executable)' 2>$null | Select-Object -First 1 | Out-String).Trim()
        if ($exe -and (Test-Path $exe)) { return $exe }
    }
    # 2) Cari di lokasi instalasi umum
    $candidates = Get-ChildItem "$env:LOCALAPPDATA\Programs\Python" -Directory -ErrorAction SilentlyContinue |
                  Where-Object { $_.Name -like "Python$Version*" } |
                  ForEach-Object { Join-Path $_.FullName 'python.exe' } |
                  Where-Object { Test-Path $_ }
    if ($candidates) { return $candidates[0] }
    return $null
}

# ---------------------------------------------------------------------------
# Judul
# ---------------------------------------------------------------------------
Write-Host ''
Write-Host '  ==============================================================' -ForegroundColor DarkCyan
Write-Host '   APLIKASI DRIVER MONITORING SYSTEM (DMS) - Kode Tim IF3-PD15' -ForegroundColor White
Write-Host '   Skrip Setup Otomatis' -ForegroundColor White
Write-Host '  ==============================================================' -ForegroundColor DarkCyan
Write-Host ''
Write-Host "  Folder proyek : $RootPath"

# ===========================================================================
Write-Step 'Memeriksa kebutuhan perangkat lunak'
# ===========================================================================

$requirements = @(
    @{ Name = 'php';      Minimum = '8.3';  Label = 'PHP 8.3+' },
    @{ Name = 'composer'; Minimum = '2.0';  Label = 'Composer 2+' },
    @{ Name = 'node';     Minimum = '20';   Label = 'Node.js 20+' },
    @{ Name = 'npm.cmd';  Minimum = '20';   Label = 'npm 20+' }
)

foreach ($req in $requirements) {
    if (Test-Command $req.Name) {
        $version = Get-VersionString $req.Name
        Write-Ok ("{0} - {1}" -f $req.Label, $version)
    } else {
        Write-Fail ("{0} BELUM TERINSTALL" -f $req.Label)
        Write-Host '          -> Untuk PHP/Composer/Node, install lewat Laragon atau situs resminya.' -ForegroundColor Gray
    }
}

# Node & npm: PowerShell memblokir npm.ps1, pastikan npm.cmd tersedia
if (-not (Test-Command 'npm.cmd')) {
    Write-Warn 'npm.cmd tidak ditemukan. Jenkins/OneDrive bisa memblokir.'
}

Write-Host ''
Write-Ok "PowerShell versi $($PSVersionTable.PSVersion)"

# ===========================================================================
Write-Step 'Memasang dependensi aplikasi Laravel'
# ===========================================================================

if (-not (Test-Path (Join-Path $RootPath 'artisan'))) {
    Write-Fail 'Berkas artisan tidak ditemukan. Pastikan berada di folder proyek.'
    Write-Host '          -> Clone repo dulu:  git clone <url-repo>' -ForegroundColor Gray
    exit 1
}

if (Test-Command 'composer') {
    Push-Location $RootPath
    try {
        Write-Info 'Menjalankan composer install (bisa memakan waktu beberapa menit)...'
        & composer install --no-interaction --prefer-dist
        if ($LASTEXITCODE -eq 0) {
            Write-Ok 'Dependensi PHP (vendor/) terpasang'
        } else {
            Write-Fail "composer install gagal (kode $LASTEXITCODE)"
        }
    } finally {
        Pop-Location
    }
} else {
    Write-Fail 'Composer tidak ditemukan, dependensi PHP belum terpasang.'
}

# ===========================================================================
Write-Step 'Menyiapkan konfigurasi .env'
# ===========================================================================

$envPath = Join-Path $RootPath '.env'

if (Test-Path $envPath) {
    Write-Warn '.env sudah ada, tidak ditimpa (konfigurasi Anda aman).'
} else {
    Copy-Item (Join-Path $RootPath '.env.example') $envPath
    Write-Ok '.env dibuat dari .env.example'
}

# APP_KEY hanya di-generate bila belum terisi
$envContent = Get-Content $envPath -Raw
if ($envContent -match '(?m)^APP_KEY\s*=\s*$') {
    Push-Location $RootPath
    try {
        & php artisan key:generate --ansi | Out-Null
        if ($LASTEXITCODE -eq 0) { Write-Ok 'APP_KEY berhasil dibuat' }
        else { Write-Fail 'Gagal membuat APP_KEY' }
    } finally {
        Pop-Location
    }
} else {
    Write-Warn 'APP_KEY sudah terisi, tidak di-regenerate.'
}

# Arahkan ke database yang benar bila perlu
if ((Get-Content $envPath -Raw) -notmatch [regex]::Escape("DB_DATABASE=$Database")) {
    Write-Warn "Menyesuaikan DB_DATABASE menjadi '$Database'..."
    (Get-Content $envPath) -replace '^DB_DATABASE=.*$', "DB_DATABASE=$Database" |
        Set-Content $envPath
    Write-Ok "DB_DATABASE = $Database"
}

# ===========================================================================
if (-not $SkipDb) {
    Write-Step 'Menyiapkan database MySQL dan migrasi'

    $mysql = Find-MySqlExe

    if (-not $mysql) {
        Write-Fail 'mysql.exe tidak ditemukan.'
        Write-Host '          -> Jalankan MySQL lewat Laragon, lalu ulangi langkah ini.' -ForegroundColor Gray
    } else {
        Write-Info "MySQL client: $mysql"

        # Penting: hanya sertakan -p bila password tidak kosong.
        # Flag "-p" tanpa nilai membuat MySQL menunggu input secara interaktif
        # dan skrip akan menggantung.
        $mysqlArgs = @("-u$DbUser", "-P$DbPort", '-h127.0.0.1', '--default-character-set=utf8mb4')
        if ($DbPass) { $mysqlArgs += "-p$DbPass" }

        # Cek apakah server MySQL hidup
        $pingOut = & $mysql @mysqlArgs -e 'SELECT 1;' 2>&1
        if ($LASTEXITCODE -ne 0) {
            Write-Fail "Tidak bisa terhubung ke MySQL di port $DbPort."
            $pingOut | ForEach-Object { Write-Host "          $_" -ForegroundColor DarkYellow }
            Write-Host '          -> Jalankan "Start All" di Laragon, lalu ulangi langkah ini.' -ForegroundColor Gray
        } else {
            Write-Ok 'Server MySQL aktif'

            # Catatan: nama database sengaja TANPA backtick agar tidak bentrok
            # dengan karakter escape PowerShell ('`').
            $createSql = "CREATE DATABASE IF NOT EXISTS $Database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
            $createOut = & $mysql @mysqlArgs -e $createSql 2>&1

            if ($LASTEXITCODE -eq 0) {
                Write-Ok "Database '$Database' siap"

                Push-Location $RootPath
                try {
                    & php artisan migrate --force
                    if ($LASTEXITCODE -eq 0) { Write-Ok 'Migrasi database selesai' }
                    else { Write-Fail 'Migrasi gagal' }
                } finally {
                    Pop-Location
                }
            } else {
                Write-Fail "Gagal membuat database '$Database'"
                $createOut | ForEach-Object { Write-Host "          $_" -ForegroundColor DarkYellow }
            }
        }
    }
} else {
    Write-Warn 'Persiapan database dilewati (opsi -SkipDb).'
}

# ===========================================================================
Write-Step 'Memasang dependensi frontend (npm)'
# ===========================================================================

if (Test-Command 'npm.cmd') {
    Push-Location $RootPath
    try {
        Write-Info 'Menjalankan npm install...'
        & npm.cmd install --no-fund --no-audit
        if ($LASTEXITCODE -eq 0) { Write-Ok 'Dependensi frontend (node_modules/) terpasang' }
        else { Write-Fail 'npm install gagal' }
    } finally {
        Pop-Location
    }
} else {
    Write-Fail 'npm tidak ditemukan, dependensi frontend belum terpasang.'
}

# ===========================================================================
if (-not $SkipAi) {
    Write-Step 'Menyiapkan AI Service (Python + MediaPipe)'

    $pythonExe = Get-PythonExe $PythonVer

    if (-not $pythonExe) {
        Write-Fail "Python $PythonVer tidak ditemukan."
        Write-Host '          -> PENTING: MediaPipe belum mendukung Python 3.14.' -ForegroundColor Yellow
        Write-Host '          -> Pasang Python 3.13 dari https://www.python.org/downloads/' -ForegroundColor Gray
        Write-Host '          -> Centang "Add python.exe to PATH" saat instalasi.' -ForegroundColor Gray
    } else {
        $pyVersion = (& $pythonExe -c 'import sys; print(sys.version.split()[0])' 2>&1 | Out-String).Trim()
        Write-Ok "Python $pyVersion -> $pythonExe"

        $venvPath   = Join-Path $AiPath '.venv'
        $venvPython = Join-Path $venvPath 'Scripts\python.exe'

        if (Test-Path $venvPython) {
            Write-Warn 'Virtual environment sudah ada, tidak dibuat ulang.'
        } else {
            Write-Info "Membuat virtual environment dengan Python $PythonVer..."
            & $pythonExe -m venv $venvPath
            if ($LASTEXITCODE -eq 0) { Write-Ok 'Virtual environment (.venv) dibuat' }
            else { Write-Fail 'Gagal membuat virtual environment' }
        }

        if (Test-Path $venvPython) {
            Write-Info 'Memasang paket Python (bisa memakan waktu 5-10 menit)...'
            & $venvPython -m pip install --upgrade pip --quiet
            & $venvPython -m pip install --no-cache-dir -r (Join-Path $AiPath 'requirements.txt')
            if ($LASTEXITCODE -eq 0) { Write-Ok 'Paket Python terpasang (FastAPI, MediaPipe, OpenCV)' }
            else { Write-Fail 'Pemasangan paket Python gagal' }

            # Unduh model MediaPipe (dilewati bila sudah ada)
            Write-Info 'Mengunduh model MediaPipe...'
            Push-Location $AiPath
            try {
                & $venvPython (Join-Path $AiPath 'scripts\download_models.py')
                if ($LASTEXITCODE -eq 0) { Write-Ok 'Model MediaPipe siap' }
                else { Write-Fail 'Gagal mengunduh model. Periksa koneksi internet.' }
            } finally {
                Pop-Location
            }

            # File .env untuk AI Service
            $aiEnv = Join-Path $AiPath '.env'
            if (Test-Path $aiEnv) {
                Write-Warn 'ai-service/.env sudah ada, tidak ditimpa.'
            } else {
                Copy-Item (Join-Path $AiPath '.env.example') $aiEnv
                Write-Ok 'ai-service/.env dibuat dari .env.example'
            }
        }
    }
} else {
    Write-Warn 'Persiapan AI Service dilewati (opsi -SkipAi).'
}

# ---------------------------------------------------------------------------
# Ringkasan
# ---------------------------------------------------------------------------
Write-Host ''
Write-Host '  ==============================================================' -ForegroundColor DarkCyan

if ($script:FailedCount -eq 0) {
    Write-Host '   SETUP SELESAI - tidak ada kesalahan' -ForegroundColor Green
} else {
    Write-Host ("   SETUP SELESAI dengan {0} masalah (lihat tanda [GAGAL] di atas)" -f $script:FailedCount) -ForegroundColor Yellow
}

Write-Host '  ==============================================================' -ForegroundColor DarkCyan
Write-Host ''
Write-Host '  Langkah berikutnya - buka DUA terminal terpisah:' -ForegroundColor White
Write-Host ''
Write-Host '   Terminal 1 - AI Service' -ForegroundColor Cyan
Write-Host '     cd ai-service' -ForegroundColor Gray
Write-Host '     .venv\Scripts\python.exe run.py' -ForegroundColor Gray
Write-Host ''
Write-Host '   Terminal 2 - Aplikasi Laravel' -ForegroundColor Cyan
Write-Host '     php artisan serve' -ForegroundColor Gray
Write-Host ''
Write-Host '  Lalu buka di browser:' -ForegroundColor White
Write-Host '     http://localhost:8000        -> Aplikasi Laravel' -ForegroundColor Yellow
Write-Host '     http://127.0.0.1:8001/docs   -> Dokumentasi AI Service' -ForegroundColor Yellow
Write-Host ''
Write-Host '  Catatan: MySQL harus dijalankan lewat Laragon (Start All).' -ForegroundColor DarkGray
Write-Host '  ==============================================================' -ForegroundColor DarkCyan
Write-Host ''

# Kode keluar: 0 bila bersih, 1 bila ada masalah.
exit $script:FailedCount
