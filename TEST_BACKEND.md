# Test Backend Directly

## Quick Test Steps

### 1. Verify Backend Server is Running
```bash
# In Inventory-Backend directory
php artisan serve
```

You should see:
```
INFO  Server running on [http://127.0.0.1:8000]
```

### 2. Test Login with cURL (Bypass Frontend)
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"email\":\"admin@inventory.com\",\"password\":\"admin123\"}"
```

### 3. Test with Postman
- Method: POST
- URL: http://127.0.0.1:8000/api/login
- Headers:
  - Content-Type: application/json
  - Accept: application/json
- Body (raw JSON):
```json
{
  "email": "admin@inventory.com",
  "password": "admin123"
}
```

### 4. Check Response Time
- If it takes > 30 seconds → Backend issue
- If it's fast (< 1 second) → Frontend/network issue

## Common Issues

### Issue 1: Database Connection Slow
**Symptoms**: Login hangs, no response
**Fix**: Check database connection in `.env`

### Issue 2: Backend Not Running
**Symptoms**: Connection refused, network error
**Fix**: Start server with `php artisan serve`

### Issue 3: Database Query Hanging
**Symptoms**: Timeout after 30-60 seconds
**Fix**: Check database server, verify tables exist

### Issue 4: PHP Execution Time Limit
**Symptoms**: "Maximum execution time exceeded"
**Fix**: Increase `max_execution_time` in php.ini or check for infinite loops

## Debug Steps

1. **Check if backend responds at all**:
   ```
   GET http://127.0.0.1:8000/api/user
   ```
   (Should return 401 Unauthorized, not timeout)

2. **Check database connection**:
   ```bash
   php artisan migrate:status
   ```

3. **Check Laravel logs**:
   ```bash
   Get-Content storage\logs\laravel.log -Tail 50
   ```

4. **Test with minimal endpoint**:
   Create a simple test route that doesn't touch database

## If Backend Times Out in Postman

The issue is definitely in the backend. Possible causes:
1. Database connection is hanging
2. `Auth::attempt()` is slow
3. `createToken()` is slow
4. Some middleware is blocking

Try creating a minimal login endpoint that bypasses some checks.

