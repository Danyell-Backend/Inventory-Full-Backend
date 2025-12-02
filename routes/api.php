<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;





// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => true,
        'message' => 'API is running',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'changePassword']);
    
    // User routes
    Route::prefix('user')->group(function () {
        // User can view categories and items
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{id}', [CategoryController::class, 'show']);
        Route::get('/items', [ItemController::class, 'index']);
        Route::get('/items/{id}', [ItemController::class, 'show']);
        
        // Transaction routes
        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::post('/transactions/borrow', [TransactionController::class, 'borrow']);
        Route::put('/transactions/{id}/return', [TransactionController::class, 'return']);
        
        // Notification routes
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
        Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    });
    
    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Category management
        Route::apiResource('categories', CategoryController::class);
        
        // Item management
        Route::apiResource('items', ItemController::class);
        
        // Admin transaction management
        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::put('/transactions/{id}/cancel', [TransactionController::class, 'cancel']);
        
        // User management
        Route::get('/users', [UserController::class, 'index']);
        Route::put('/users/{id}/toggle-restriction', [UserController::class, 'toggleRestriction']);
    });
});