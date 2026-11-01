@echo off
title DriveSafe - Python AI Vision Service
cd /d "%~dp0"
echo ========================================================
echo   DriveSafe DMS - Python AI Vision Service (Port 5000)
echo ========================================================
echo Service inferensi MediaPipe & Random Forest Classifier
echo Endpoint: http://127.0.0.1:5000
echo ========================================================
".\venv\Scripts\python.exe" run.py
pause

