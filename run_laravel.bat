@echo off
title DriveSafe - Laravel Web Server
cd /d "%~dp0laravel"
echo ========================================================
echo   DriveSafe DMS - Laravel Web Application (Port 8000)
echo ========================================================
echo Akses Web: http://127.0.0.1:8000
echo Akun Login:
echo   - Administrator: admin / admin123
echo   - Driver Utama : apri  / driver123
echo ========================================================
php artisan serve --host=127.0.0.1 --port=8000
pause
