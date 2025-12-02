# Debug Login Timeout Issue

## Problem
Frontend is timing out after 30 seconds when trying to login. Backend is not responding.

## Diagnostic Steps

### Step 1: Verify Backend Server is Running
```bash
# In Inventory-Backend directory
php artisan serve
```

**Expected output:**
```
INFO  Server running on [http://127.0.0.1:8000]
```

### Step 2: Test Health Endpoint
Open browser or use curl:
```
GET http://127.0.0.1:8000/api/health
```

**Expected response:**
```json
{
  "status": true,
  "message": "API is running",
  "timestamp": "2025-12-02 12:00:00"
}
```

If this doesn't work → Backend server is not running or not accessible.

### Step 3: Test Login with Postman
1. Open Postman
2. Create new request:
   - Method: `POST`
   - URL: `http://127.0.0.1:8000/api/login`
   - Headers:
     - `Content-Type: application/json`
     - `Accept: application/json`
   - Body (raw JSON):
   ```json
   {
     "email": "admin@inventory.com",
     "password": "admin123"
   }
   ```
3. Click Send
4. Check response time:
   - If < 1 second → Backend works, issue is frontend/network
   - If > 30 seconds/timeout → Backend issue

### Step 4: Check Laravel Logs
```bash
Get-Content storage\logs\laravel.log -Tail 50
```

Look for:
- "Login attempt" - Request reached backend
- "User authenticated" - Auth worked
- "Token created" - Token creation worked
- Any error messages

### Step 5: Check Database Connection
```bash
php artisan migrate:status
```

If this hangs or errors → Database connection issue.

## Common Issues & Solutions

### Issue 1: Backend Server Not Running
**Symptom**: Connection refused, timeout
**Solution**: 
```bash
cd Inventory-Backend
php artisan serve
```

### Issue 2: Database Connection Hanging
**Symptom**: Login hangs at `Auth::attempt()`
**Solution**: 
- Check `.env` database settings
- Verify database server is running
- Test connection: `php artisan migrate:status`

### Issue 3: CORS Blocking Request
**Symptom**: Request never reaches backend
**Solution**: Check `config/cors.php` settings

### Issue 4: Wrong API URL
**Symptom**: Request goes to wrong server
**Solution**: 
- Check `NEXT_PUBLIC_API_URL` in frontend `.env`
- Verify it matches backend server URL

### Issue 5: Network/Firewall Blocking
**Symptom**: Timeout, no response
**Solution**: 
- Check Windows Firewall
- Verify port 8000 is not blocked
- Try `http://127.0.0.1:8000` instead of `localhost:8000`

## Quick Test Script

Create `test-backend.php` in Inventory-Backend root:

```php
<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/api/health', 'GET');
$response = $kernel->handle($request);

echo $response->getContent();
```

Run: `php test-backend.php`

## What to Check Next

1. **Is backend server running?** → Check with health endpoint
2. **Does Postman work?** → If yes, issue is frontend/network
3. **What do logs show?** → Check which step is hanging
4. **Is database accessible?** → Test with migrate:status

## Added Logging

The login method now logs:
- Login attempt start
- Authentication result
- Token creation
- Response return

Check logs to see where it's hanging.

