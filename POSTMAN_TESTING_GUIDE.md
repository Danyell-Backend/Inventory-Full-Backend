# Postman Testing Guide - Fixed Endpoints

## Important Notes

### GET Requests
**DO NOT send JSON body with GET requests!** GET requests should only use:
- URL parameters (query strings)
- Headers (Authorization, Accept, etc.)

For example:
- ✅ Correct: `GET /api/admin/items?status=available&category_id=1`
- ❌ Wrong: `GET /api/admin/items` with JSON body

### Authentication
All protected endpoints require:
```
Authorization: Bearer {your_token_here}
Accept: application/json
```

## Testing GET /api/admin/items

### Correct Way:
1. **Method**: `GET`
2. **URL**: `http://127.0.0.1:8000/api/admin/items`
3. **Headers**:
   - `Authorization: Bearer {your_token}`
   - `Accept: application/json`
4. **Body**: **NONE** (GET requests don't have bodies)
5. **Query Params** (Optional):
   - `status`: available, borrowed, or maintenance
   - `category_id`: numeric ID

### Example with Query Params:
```
GET http://127.0.0.1:8000/api/admin/items?status=available&category_id=1
```

## Common Issues Fixed

### 1. Status Enum Mismatch
- **Fixed**: Changed `unavailable` to `maintenance` to match database enum
- **Valid statuses**: `available`, `borrowed`, `maintenance`

### 2. Category Relationship
- **Fixed**: Added `withDefault()` to handle deleted categories gracefully
- **Fixed**: Added null checks for category relationships

### 3. Error Handling
- **Fixed**: All endpoints now have try-catch blocks
- **Fixed**: Proper error messages returned

## Quick Test Steps

1. **Login first**:
   ```
   POST http://127.0.0.1:8000/api/login
   Body: {
     "email": "admin@inventory.com",
     "password": "admin123"
   }
   ```

2. **Copy the token** from response

3. **Test GET items**:
   ```
   GET http://127.0.0.1:8000/api/admin/items
   Headers:
     Authorization: Bearer {paste_token_here}
     Accept: application/json
   ```

4. **Check response**:
   - Should return: `{ "status": true, "data": [...] }`
   - If 500 error, check the error message in response

## All Fixed Endpoints

All 27 endpoints have been fixed with:
- ✅ Proper error handling
- ✅ Consistent response format
- ✅ Relationship loading
- ✅ Status enum fixes
- ✅ Null safety checks

## If Still Getting 500 Error

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check the error message in Postman response
3. Verify:
   - Database is migrated: `php artisan migrate`
   - Roles are seeded: `php artisan db:seed --class=RoleSeeder`
   - User has admin role
   - Token is valid

## Debug Mode

To see detailed errors, set in `.env`:
```
APP_DEBUG=true
```

Then error responses will include stack traces.

