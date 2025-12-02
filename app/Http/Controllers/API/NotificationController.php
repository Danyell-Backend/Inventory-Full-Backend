<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
                
            return response()->json([
                'status' => true,
                'data' => $notifications
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch notifications: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark a notification as read
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $user = $request->user();
            $notification = Notification::where('id', $id)
                ->where('user_id', $user->id)
                ->first();
                
            if (!$notification) {
                return response()->json([
                    'status' => false,
                    'message' => 'Notification not found'
                ], 404);
            }
            
            $notification->status = 'read';
            $notification->save();
            
            return response()->json([
                'status' => true,
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to mark notification as read: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark all notifications as read for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $user = $request->user();
            Notification::where('user_id', $user->id)
                ->where('status', 'unread')
                ->update(['status' => 'read']);
                
            return response()->json([
                'status' => true,
                'message' => 'All notifications marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to mark all notifications as read: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get unread notification count for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getUnreadCount(Request $request)
    {
        try {
            $user = $request->user();
            $count = Notification::where('user_id', $user->id)
                ->where('status', 'unread')
                ->count();
                
            return response()->json([
                'status' => true,
                'data' => [
                    'count' => $count
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to get unread count: ' . $e->getMessage()
            ], 500);
        }
    }
}

