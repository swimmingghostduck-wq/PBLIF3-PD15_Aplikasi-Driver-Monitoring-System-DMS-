@echo off
title DriveSafe DMS Launcher
cd /d "%~dp0"
echo ========================================================
echo   Memulai Seluruh Layanan DriveSafe DMS...
echo ========================================================
echo [1/2] Menjalankan Service AI Vision Python (Port 5000)...
start "DriveSafe AI Service (Port 5000)" cmd /k ".\run_ai.bat"
timeout /t 3 /nobreak >nul

echo [2/2] Menjalankan Server Web Laravel (Port 8000)...
start "DriveSafe Laravel Web (Port 8000)" cmd /k ".\run_laravel.bat"
timeout /t 2 /nobreak >nul

echo ========================================================
echo Kedua service telah berjalan!
echo Silakan buka browser Anda di: http://127.0.0.1:8000
echo ========================================================

