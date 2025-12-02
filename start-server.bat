@echo off
echo ========================================
echo Starting Laravel Server (PHP Built-in)
echo ========================================
echo.
echo This bypasses php artisan serve to avoid cache issues
echo.
cd /d "%~dp0"

echo Starting server on http://127.0.0.1:8000
echo Press Ctrl+C to stop
echo.

php -S 127.0.0.1:8000 -t public

pause

