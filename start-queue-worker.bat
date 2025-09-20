@echo off
echo Starting Email Queue Worker for Apotek Baraya...
echo.

REM Change to project directory
cd /d "D:\Coding\Herd\apotekbaraya"

REM Start queue worker for emails queue
echo Starting queue worker for 'emails' queue...
php artisan queue:work --queue=emails --sleep=3 --tries=3 --max-time=3600 --timeout=60

echo.
echo Queue worker stopped.
pause