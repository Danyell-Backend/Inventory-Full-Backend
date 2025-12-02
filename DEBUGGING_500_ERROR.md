# Debugging 500 Internal Server Error

## Current Issue
All API endpoints are returning 500 Internal Server Error in Postman.

## Possible Causes

### 1. Database Connection Issues
- Check `.env` file has correct database credentials
- Verify database server is running
- Test connection: `php artisan migrate:status`

### 2. Missing Migrations
- Run: `php artisan migrate`
- Check: `php artisan migrate:status`

### 3. Missing Roles/Permissions
- Run: `php artisan db:seed --class=RoleSeeder`
- Verify roles exist in database

### 4. File System Issues
- Check `storage/logs` directory is writable
- Check `bootstrap/cache` directory exists and is writable
- Clear all caches: `php artisan optimize:clear`

### 5. Package Manifest Issues
- Delete `bootstrap/cache/packages.php` if it exists
- Run: `composer dump-autoload`
- Run: `php artisan config:clear`

## Quick Fix Steps

### Step 1: Clear All Caches
```bash
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Step 2: Check Database
```bash
php artisan migrate:status
```

If migrations are pending:
```bash
php artisan migrate
```

### Step 3: Seed Roles
```bash
php artisan db:seed --class=RoleSeeder
```

### Step 4: Check Logs
```bash
# Windows PowerShell
Get-Content storage\logs\laravel.log -Tail 50

# Or open the file directly
notepad storage\logs\laravel.log
```

### Step 5: Test Simple Endpoint
Try the simplest endpoint first:
```
GET http://127.0.0.1:8000/api/user
Headers:
  Authorization: Bearer {token}
```

## What Was Changed

### Login Method Optimization
- Changed from `User::where()->first()` to `Auth::user()` after successful login
- Changed from relationship loading to direct DB query for roles
- Added try-catch around role loading
- Added logging for errors

### Why Direct DB Query?
The Spatie Permission package's `getRoleNames()` and relationship loading was causing timeouts. Using direct DB query is faster and more reliable.

## Testing

### Test Login:
```
POST http://127.0.0.1:8000/api/login
Content-Type: application/json

{
  "email": "admin@inventory.com",
  "password": "admin123"
}
```

### Expected Response:
```json
{
  "status": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin",
      "email": "admin@inventory.com",
      "roles_array": ["admin"]
    },
    "token": "1|xxxxxxxxxxxxx"
  }
}
```

## If Still Getting 500 Error

1. **Enable Debug Mode** in `.env`:
   ```
   APP_DEBUG=true
   ```
   This will show detailed error messages.

2. **Check the Actual Error**:
   - Look at Postman response body
   - Check `storage/logs/laravel.log`
   - The error message will tell you exactly what's wrong

3. **Common Errors**:
   - **SQLSTATE[HY000] [2002]**: Database connection failed
   - **Class not found**: Run `composer dump-autoload`
   - **Table doesn't exist**: Run `php artisan migrate`
   - **Permission denied**: Check file permissions

## Next Steps

1. Try the login endpoint again
2. Check the error message in Postman response
3. Share the exact error message if it persists
4. Check Laravel logs for detailed stack trace

