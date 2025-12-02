<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\UpdateItemRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Item::with('category');
            
            // Filter by category
            if ($request->has('category_id') && $request->input('category_id')) {
                $query->where('category_id', $request->input('category_id'));
            }
            
            // Filter by status
            if ($request->has('status') && $request->input('status')) {
                $query->where('status', $request->input('status'));
            }
            
            $items = $query->get();
            
            return response()->json([
                'status' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            Log::error('ItemController@index Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch items: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemRequest $request)
    {
        try {
            $imagePath = null;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = 'items/' . $filename;

                // Make sure directory exists
                Storage::disk('public')->makeDirectory('items', 0755, true, true);

                // Resize and save the image properly (Intervention Image v3 uses read())
                Image::read($image)->resize(500, 500)->save(storage_path('app/public/' . $imagePath));
            }

            $item = Item::create([
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'quantity' => $request->quantity,
                'image' => $imagePath,
                'status' => $request->status ?? 'available'
            ]);

            $item->load('category');

            return response()->json([
                'status' => true,
                'message' => 'Item created successfully',
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $item = Item::with('category', 'transactions')->find($id);
            
            if (!$item) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, string $id)
    {
        try {
            $item = Item::find($id);
            
            if (!$item) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($item->image) {
                    Storage::disk('public')->delete($item->image);
                }

                $image = $request->file('image');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = 'items/' . $filename;
                
                // Store the image
                Storage::disk('public')->makeDirectory('items', 0755, true, true);
                Image::read($image)->resize(500, 500)->save(storage_path('app/public/' . $imagePath));
                
                $item->image = $imagePath;
            }

            $item->name = $request->name;
            $item->description = $request->description;
            $item->category_id = $request->category_id;
            $item->quantity = $request->quantity;
            $item->status = $request->status ?? $item->status;
            $item->save();

            $item->load('category');

            return response()->json([
                'status' => true,
                'message' => 'Item updated successfully',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $item = Item::find($id);
            
            if (!$item) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            // Check if item has active transactions
            if ($item->transactions()->where('status', 'borrowed')->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete item with active transactions'
                ], 400);
            }

            // Delete image if exists
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }

            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'Item deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete item: ' . $e->getMessage()
            ], 500);
        }
    }
}
