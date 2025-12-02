# Fix Corrupted Cache Files

## Problem
Error: `file_get_contents(): Read of 384855 bytes failed with errno=22 Invalid argument`

This means there's a **corrupted cache file** that Laravel can't read. This is causing `php artisan serve` to hang.

## Solution: Delete All Cache Files

### Step 1: Stop Any Running Servers
Press `Ctrl+C` in any terminal running `php artisan serve`

### Step 2: Delete Cache Directories

**Option A: Using PowerShell (Recommended)**
```powershell
cd "C:\Users\dan\OneDrive\Desktop\INVENTORY SYSTEM BOTH FRONT AND BACKEND\Inventory-Backend"

# Delete bootstrap cache
Remove-Item -Path bootstrap\cache\* -Recurse -Force -ErrorAction SilentlyContinue

# Delete storage framework cache
Remove-Item -Path storage\framework\cache\data\* -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item -Path storage\framework\sessions\* -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item -Path storage\framework\views\* -Recurse -Force -ErrorAction SilentlyContinue

# Delete compiled files
Remove-Item -Path storage\framework\compiled.php -Force -ErrorAction SilentlyContinue
```

**Option B: Manual Delete**
1. Open File Explorer
2. Navigate to `Inventory-Backend\bootstrap\cache`
3. Delete all files inside (keep the folder)
4. Navigate to `Inventory-Backend\storage\framework\cache\data`
5. Delete all files inside (keep the folder)
6. Navigate to `Inventory-Backend\storage\framework\sessions`
7. Delete all files inside (keep the folder)
8. Navigate to `Inventory-Backend\storage\framework\views`
9. Delete all files inside (keep the folder)

### Step 3: Recreate Cache Directories (If Needed)
```powershell
# Make sure directories exist
New-Item -ItemType Directory -Path bootstrap\cache -Force | Out-Null
New-Item -ItemType Directory -Path storage\framework\cache\data -Force | Out-Null
New-Item -ItemType Directory -Path storage\framework\sessions -Force | Out-Null
New-Item -ItemType Directory -Path storage\framework\views -Force | Out-Null
```

### Step 4: Test
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

If these commands work without errors → Cache is fixed!

### Step 5: Start Server
```bash
php artisan serve
```

Should now start quickly!

## Quick Fix Script

Save this as `clear-cache.bat` in Inventory-Backend:

```batch
@echo off
echo Clearing all Laravel caches...

cd /d "%~dp0"

echo Deleting bootstrap cache...
if exist bootstrap\cache\*.* del /q bootstrap\cache\*.*

echo Deleting storage cache...
if exist storage\framework\cache\data\*.* del /q storage\framework\cache\data\*.*
if exist storage\framework\sessions\*.* del /q storage\framework\sessions\*.*
if exist storage\framework\views\*.* del /q storage\framework\views\*.*

echo Cache cleared!
echo.
echo Now try: php artisan serve
pause
```

Run: `clear-cache.bat`

## Why This Happens

- OneDrive sync corruption
- Antivirus scanning files
- Unexpected shutdown during cache write
- File system errors
- Disk space issues

## Prevention

1. **Add to antivirus exclusions:**
   - `Inventory-Backend\bootstrap\cache`
   - `Inventory-Backend\storage`

2. **Pause OneDrive sync** when developing

3. **Regular cleanup:**
   ```bash
   php artisan optimize:clear
   ```

## After Fixing

1. **Test:**
   ```bash
   php artisan serve
   ```

2. **Should see:**
   ```
   INFO  Server running on [http://127.0.0.1:8000]
   ```

3. **If still hangs:**
   - Check Laravel logs: `storage\logs\laravel.log`
   - Try: `php artisan route:list`
   - Check database connection: `php artisan migrate:status`

