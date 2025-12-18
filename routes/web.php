<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return response()->json([
        'message' => 'Inventory Management System API',
        'version' => '1.0.0',
        'status' => 'running'
    ]);
});

// Fallback for undefined routes - return 404
Route::fallback(function () {
    return response()->json([
        'status' => false,
        'message' => 'Route not found'
    ], 404);
});
