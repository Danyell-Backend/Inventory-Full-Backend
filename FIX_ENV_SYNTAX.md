# Fix .env Syntax Error

## Problem Found
Your `.env` file has a **syntax error on line 3**:
```
Warning: syntax error, unexpected '=' in .env on line 3
```

This is causing `php artisan serve` to hang because Laravel can't parse the environment file properly.

## How to Fix

### Step 1: Open `.env` File
Open: `Inventory-Backend\.env`

### Step 2: Check Line 3
Look at line 3. Common issues:

**❌ Wrong:**
```env
APP_NAME=Inventory System
APP_KEY=base64:something=with=equals
DB_PASSWORD=password with spaces
```

**✓ Correct:**
```env
APP_NAME="Inventory System"
APP_KEY=base64:something=with=equals
DB_PASSWORD="password with spaces"
```

### Step 3: Common .env Syntax Rules

1. **Values with spaces MUST be quoted:**
   ```env
   APP_NAME="My App Name"  ✓
   APP_NAME=My App Name     ✗ (error)
   ```

2. **Values with special characters should be quoted:**
   ```env
   DB_PASSWORD="p@ssw0rd#123"  ✓
   DB_PASSWORD=p@ssw0rd#123     ✗ (might cause issues)
   ```

3. **No spaces around equals sign:**
   ```env
   DB_HOST=127.0.0.1  ✓
   DB_HOST = 127.0.0.1  ✗ (error)
   ```

4. **Comments start with #:**
   ```env
   # This is a comment
   DB_HOST=127.0.0.1
   ```

### Step 4: Check Your Line 3

Common issues on line 3:
- `APP_NAME` with spaces (needs quotes)
- `APP_URL` with special characters
- Extra `=` sign somewhere
- Missing quotes around value

### Step 5: Test After Fix

After fixing, test:
```bash
php test-db-connection.php
```

If no syntax error → Good!
Then try:
```bash
php artisan serve
```

## Quick Fix Template

If you're not sure what's wrong, here's a clean `.env` template:

```env
APP_NAME="Inventory System"
APP_ENV=local
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_db
DB_USERNAME=root
DB_PASSWORD=your_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## After Fixing

1. **Test .env parsing:**
   ```bash
   php test-db-connection.php
   ```

2. **Start server:**
   ```bash
   php artisan serve
   ```

3. **Should see:**
   ```
   INFO  Server running on [http://127.0.0.1:8000]
   ```

If it still hangs after fixing `.env`, the issue is elsewhere.

