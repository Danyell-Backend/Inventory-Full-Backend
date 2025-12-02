# Diagnosing Slow `php artisan serve` Startup

## Current Status
The server is **hanging** before it can even start. This means something is blocking during Laravel's bootstrap process.

## What We Know
- ✅ Laravel bootstrap itself is fast (63ms in test)
- ❌ `php artisan serve` hangs before showing "Server running" message
- ❌ Health endpoint is not accessible (server never started)

## Most Likely Causes

### 1. Database Connection Hanging (90% likely)
Laravel tries to connect to the database during bootstrap. If:
- Database server is not running
- Wrong credentials in `.env`
- Network timeout
- Database server is slow

Then `php artisan serve` will hang waiting for the connection.

**Solution:**
```bash
# Test database connection
php artisan migrate:status

# If this hangs, database is the problem
# Fix: Start MySQL or fix .env settings
```

### 2. Service Provider Doing Heavy Work
A service provider might be doing something slow during `boot()`.

**Check:**
- `app/Providers/AppServiceProvider.php`
- Any custom service providers

### 3. Route Loading Issue
Large number of routes or complex route definitions.

**Check:**
```bash
php artisan route:list
```

### 4. File System Issue
Windows file system or antivirus scanning files.

**Solution:**
- Add Laravel directory to antivirus exclusions
- Check if OneDrive sync is causing issues

## Immediate Fixes to Try

### Fix 1: Test Database Connection
```bash
php artisan migrate:status
```
If this hangs → Database issue

### Fix 2: Switch to SQLite (Fastest)
Edit `.env`:
```env
DB_CONNECTION=sqlite
# Comment out or remove:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=...
# DB_USERNAME=...
# DB_PASSWORD=...
```

Then:
```bash
# Create SQLite database
New-Item -ItemType File -Path database\database.sqlite -Force

# Run migrations
php artisan migrate
```

### Fix 3: Check MySQL Service
1. Open Windows Services (`services.msc`)
2. Find "MySQL" or "MariaDB"
3. Make sure it's "Running"
4. If not, start it

### Fix 4: Verify .env Settings
Check your `.env` file has correct database settings:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Fix 5: Test with Minimal Laravel
Create a simple test:
```php
<?php
// test-simple.php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
echo "Laravel loaded!\n";
```

Run: `php test-simple.php`

If this hangs → Laravel bootstrap issue
If this works → Issue is specific to `php artisan serve`

## Quick Diagnostic Commands

```bash
# 1. Test database
php artisan migrate:status

# 2. Test Laravel bootstrap
php test-startup.php

# 3. Check routes
php artisan route:list

# 4. Clear everything
php artisan optimize:clear

# 5. Check logs
Get-Content storage\logs\laravel.log -Tail 20
```

## Expected Behavior

**Normal:**
```
> php artisan serve
INFO  Server running on [http://127.0.0.1:8000]
```

**Hanging:**
```
> php artisan serve
[No output, just hangs]
```

## Next Steps

1. **Run:** `php artisan migrate:status`
   - If it hangs → Database connection issue
   - If it works → Different problem

2. **If database is the issue:**
   - Start MySQL service
   - Fix `.env` credentials
   - Or switch to SQLite

3. **If database is NOT the issue:**
   - Check service providers
   - Check route files
   - Check for file system issues

## Contact Points

The server MUST show "Server running on [http://127.0.0.1:8000]" within 5 seconds.
If it doesn't, something is blocking the startup process.

