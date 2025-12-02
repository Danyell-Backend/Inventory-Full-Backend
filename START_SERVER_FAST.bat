@echo off
echo Starting Laravel server...
echo.
echo If this hangs, the database connection might be the issue.
echo.
echo Try these solutions:
echo 1. Make sure MySQL is running
echo 2. Check your .env file database settings
echo 3. Switch to SQLite: Set DB_CONNECTION=sqlite in .env
echo.
echo Starting server on http://127.0.0.1:8000
echo Press Ctrl+C to stop
echo.

cd /d "%~dp0"
php artisan serve --host=127.0.0.1 --port=8000

pause

