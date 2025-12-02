@echo off
echo ========================================
echo Clearing ALL Laravel Cache Files
echo ========================================
echo.

cd /d "%~dp0"

echo [1/5] Deleting bootstrap cache...
if exist bootstrap\cache\*.* (
    del /q /s bootstrap\cache\*.* 2>nul
    echo   ✓ Bootstrap cache cleared
) else (
    echo   ✓ No bootstrap cache files
)

echo [2/5] Deleting storage framework cache...
if exist storage\framework\cache\data\*.* (
    del /q /s storage\framework\cache\data\*.* 2>nul
    echo   ✓ Framework cache cleared
) else (
    echo   ✓ No framework cache files
)

echo [3/5] Deleting sessions...
if exist storage\framework\sessions\*.* (
    del /q /s storage\framework\sessions\*.* 2>nul
    echo   ✓ Sessions cleared
) else (
    echo   ✓ No session files
)

echo [4/5] Deleting compiled views...
if exist storage\framework\views\*.* (
    del /q /s storage\framework\views\*.* 2>nul
    echo   ✓ Compiled views cleared
) else (
    echo   ✓ No compiled views
)

echo [5/5] Running artisan clear commands...
php artisan config:clear 2>nul
php artisan cache:clear 2>nul
php artisan route:clear 2>nul
php artisan view:clear 2>nul

echo.
echo ========================================
echo Cache clearing complete!
echo ========================================
echo.
echo Now try: php artisan serve
echo.
pause

