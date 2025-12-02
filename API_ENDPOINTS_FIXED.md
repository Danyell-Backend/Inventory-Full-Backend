# API Endpoints - Fixed and Tested

All API endpoints have been fixed with proper error handling. Here's a complete guide for testing in Postman.

## Base URL
```
http://localhost:8000/api
```

## Authentication

### 1. Register User
- **Method**: `POST`
- **URL**: `/api/register`
- **Auth**: None (Public)
- **Body** (JSON):
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```
- **Optional**: `profile_image` (multipart/form-data)

### 2. Login
- **Method**: `POST`
- **URL**: `/api/login`
- **Auth**: None (Public)
- **Body** (JSON):
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```
- **Response**: Returns `token` - use this for authenticated requests

### 3. Get Current User
- **Method**: `GET`
- **URL**: `/api/user`
- **Auth**: Bearer Token (Required)
- **Headers**: 
  - `Authorization: Bearer {token}`
  - `Accept: application/json`

### 4. Update Profile
- **Method**: `PUT`
- **URL**: `/api/user/profile`
- **Auth**: Bearer Token (Required)
- **Body** (FormData or JSON):
```json
{
  "name": "John Updated",
  "email": "johnupdated@example.com"
}
```
- **Optional**: `profile_image` (file upload)

### 5. Change Password
- **Method**: `PUT`
- **URL**: `/api/user/password`
- **Auth**: Bearer Token (Required)
- **Body** (JSON):
```json
{
  "current_password": "password123",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

### 6. Logout
- **Method**: `POST`
- **URL**: `/api/logout`
- **Auth**: Bearer Token (Required)

## Categories

### 7. List All Categories (User)
- **Method**: `GET`
- **URL**: `/api/user/categories`
- **Auth**: Bearer Token (Required)

### 8. Get Category Details (User)
- **Method**: `GET`
- **URL**: `/api/user/categories/{id}`
- **Auth**: Bearer Token (Required)

### 9. Create Category (Admin)
- **Method**: `POST`
- **URL**: `/api/admin/categories`
- **Auth**: Bearer Token + Admin Role (Required)
- **Body** (JSON):
```json
{
  "name": "Electronics",
  "description": "Electronic items"
}
```

### 10. Update Category (Admin)
- **Method**: `PUT`
- **URL**: `/api/admin/categories/{id}`
- **Auth**: Bearer Token + Admin Role (Required)
- **Body** (JSON):
```json
{
  "name": "Electronics Updated",
  "description": "Updated description"
}
```

### 11. Delete Category (Admin)
- **Method**: `DELETE`
- **URL**: `/api/admin/categories/{id}`
- **Auth**: Bearer Token + Admin Role (Required)

## Items

### 12. List All Items (User)
- **Method**: `GET`
- **URL**: `/api/user/items`
- **Auth**: Bearer Token (Required)
- **Query Params** (Optional):
  - `category_id`: Filter by category
  - `status`: Filter by status (available, unavailable, maintenance)

### 13. Get Item Details (User)
- **Method**: `GET`
- **URL**: `/api/user/items/{id}`
- **Auth**: Bearer Token (Required)

### 14. Create Item (Admin)
- **Method**: `POST`
- **URL**: `/api/admin/items`
- **Auth**: Bearer Token + Admin Role (Required)
- **Body** (FormData):
  - `name`: "Laptop"
  - `description`: "Dell Laptop"
  - `category_id`: 1
  - `quantity`: 10
  - `status`: "available"
  - `image`: (file, optional)

### 15. Update Item (Admin)
- **Method**: `PUT`
- **URL**: `/api/admin/items/{id}`
- **Auth**: Bearer Token + Admin Role (Required)
- **Body** (FormData or JSON):
```json
{
  "name": "Laptop Updated",
  "description": "Updated description",
  "category_id": 1,
  "quantity": 15,
  "status": "available"
}
```

### 16. Delete Item (Admin)
- **Method**: `DELETE`
- **URL**: `/api/admin/items/{id}`
- **Auth**: Bearer Token + Admin Role (Required)

## Transactions

### 17. List Transactions (User)
- **Method**: `GET`
- **URL**: `/api/user/transactions`
- **Auth**: Bearer Token (Required)
- **Query Params** (Optional):
  - `status`: Filter by status (borrowed, returned)

### 18. List All Transactions (Admin)
- **Method**: `GET`
- **URL**: `/api/admin/transactions`
- **Auth**: Bearer Token + Admin Role (Required)
- **Query Params** (Optional):
  - `user_id`: Filter by user
  - `status`: Filter by status

### 19. Borrow Item
- **Method**: `POST`
- **URL**: `/api/user/transactions/borrow`
- **Auth**: Bearer Token (Required)
- **Body** (JSON):
```json
{
  "item_id": 1,
  "borrow_date": "2025-01-20",
  "due_date": "2025-01-27"
}
```

### 20. Return Item
- **Method**: `PUT`
- **URL**: `/api/user/transactions/{id}/return`
- **Auth**: Bearer Token (Required)

### 21. Cancel Transaction (Admin)
- **Method**: `PUT`
- **URL**: `/api/admin/transactions/{id}/cancel`
- **Auth**: Bearer Token + Admin Role (Required)

## Notifications

### 22. List Notifications
- **Method**: `GET`
- **URL**: `/api/user/notifications`
- **Auth**: Bearer Token (Required)

### 23. Get Unread Count
- **Method**: `GET`
- **URL**: `/api/user/notifications/unread-count`
- **Auth**: Bearer Token (Required)

### 24. Mark Notification as Read
- **Method**: `PUT`
- **URL**: `/api/user/notifications/{id}/read`
- **Auth**: Bearer Token (Required)

### 25. Mark All Notifications as Read
- **Method**: `PUT`
- **URL**: `/api/user/notifications/mark-all-read`
- **Auth**: Bearer Token (Required)

## User Management (Admin Only)

### 26. List All Users
- **Method**: `GET`
- **URL**: `/api/admin/users`
- **Auth**: Bearer Token + Admin Role (Required)

### 27. Toggle User Restriction
- **Method**: `PUT`
- **URL**: `/api/admin/users/{id}/toggle-restriction`
- **Auth**: Bearer Token + Admin Role (Required)

## Postman Setup

### Environment Variables
1. Create a new environment in Postman
2. Add variables:
   - `base_url`: `http://localhost:8000/api`
   - `token`: (will be set after login)

### Collection Setup
1. Create a new collection
2. Add Pre-request Script to collection:
```javascript
// Auto-add token to requests
if (pm.environment.get("token")) {
    pm.request.headers.add({
        key: "Authorization",
        value: "Bearer " + pm.environment.get("token")
    });
}
```

3. Add Tests to Login request:
```javascript
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    if (jsonData.data && jsonData.data.token) {
        pm.environment.set("token", jsonData.data.token);
    }
}
```

## Common Headers

For all authenticated requests:
```
Authorization: Bearer {your_token_here}
Accept: application/json
Content-Type: application/json
```

For file uploads (FormData):
```
Authorization: Bearer {your_token_here}
Accept: application/json
Content-Type: multipart/form-data
```

## Error Responses

All endpoints now return proper error responses:

### 400 Bad Request
```json
{
  "status": false,
  "message": "Error message here"
}
```

### 401 Unauthorized
```json
{
  "status": false,
  "message": "Unauthenticated"
}
```

### 403 Forbidden
```json
{
  "status": false,
  "message": "Access denied"
}
```

### 404 Not Found
```json
{
  "status": false,
  "message": "Resource not found"
}
```

### 500 Internal Server Error
```json
{
  "status": false,
  "message": "Error message with details"
}
```

## Testing Checklist

- [x] All endpoints have try-catch error handling
- [x] All endpoints return consistent response format
- [x] All relationships are properly loaded
- [x] All validation errors are handled
- [x] All authentication checks are in place
- [x] All role-based access controls work
- [x] All file uploads work correctly
- [x] All database operations are safe

## Notes

1. **Authentication**: Use the token from login response in `Authorization: Bearer {token}` header
2. **Admin Routes**: Require both authentication AND admin role
3. **File Uploads**: Use FormData for endpoints that accept images
4. **Date Format**: Use `YYYY-MM-DD` format for dates
5. **Error Messages**: All errors now include descriptive messages

## Fixed Issues

1. ✅ Added try-catch blocks to all methods
2. ✅ Added proper error responses with status codes
3. ✅ Fixed missing relationships loading
4. ✅ Fixed roles_array not being returned
5. ✅ Fixed notification pagination issue (changed to get())
6. ✅ Added proper null checks
7. ✅ Fixed transaction status handling
8. ✅ Added proper response structure

All endpoints should now work correctly in Postman!

