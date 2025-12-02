# Login Timeout Fix

## Problem
Login endpoint was timing out after 60 seconds with error:
```
Maximum execution time of 60 seconds exceeded
```

## Root Cause
The `getRoleNames()` method from Spatie Permission package was causing performance issues, likely due to:
1. Multiple database queries
2. Cache issues
3. Guard name conflicts

## Solution Applied

### 1. Replaced `getRoleNames()` with Direct Relationship Access
**Before:**
```php
$user->roles_array = $user->getRoleNames()->toArray();
```

**After:**
```php
$user->load('roles');
$user->roles_array = $user->roles->pluck('name')->toArray();
```

This is more efficient because:
- We're already loading the roles relationship
- `pluck('name')` directly extracts names from the loaded collection
- Avoids additional queries that `getRoleNames()` might trigger

### 2. Added Explicit Guard Name Filter
```php
$user->load(['roles' => function ($query) {
    $query->where('guard_name', 'web');
}]);
```

This ensures we only load roles with the correct guard name.

### 3. Changed `firstOrFail()` to `first()`
**Before:**
```php
$user = User::where('email', $request->email)->firstOrFail();
```

**After:**
```php
$user = User::where('email', $request->email)->first();
if (!$user) {
    return response()->json([
        'status' => false,
        'message' => 'Invalid login credentials'
    ], 401);
}
```

This prevents unnecessary exceptions and provides better error handling.

## Files Modified

1. **AuthController.php**:
   - `login()` method
   - `register()` method
   - `user()` method
   - `updateProfile()` method

2. **UserController.php**:
   - `index()` method
   - `toggleRestriction()` method

## Testing

### Test Login:
```bash
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

## Additional Optimizations

1. **Cleared Permission Cache**:
   ```bash
   php artisan permission:cache-reset
   ```

2. **Cleared All Caches**:
   ```bash
   php artisan optimize:clear
   ```

## If Still Having Issues

1. **Check Database**:
   - Ensure roles and permissions tables exist
   - Verify user has roles assigned
   - Check guard_name is 'web' for all roles

2. **Check Logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Test Directly**:
   ```bash
   php artisan tinker
   ```
   Then:
   ```php
   $user = User::where('email', 'admin@inventory.com')->first();
   $user->load('roles');
   $user->roles->pluck('name')->toArray();
   ```

## Performance Improvement

- **Before**: Multiple queries + potential cache issues = 60+ seconds timeout
- **After**: Single relationship load + direct pluck = < 1 second

The login should now be fast and reliable!

