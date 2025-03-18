<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:3072' // Max 3MB
            ]);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                
                // Store in storage/app/public/uploads/products
                $path = $file->storeAs('uploads/products', $fileName, 'public');
                
                // Return full URL including domain
                $url = url('storage/' . $path);
                
                return response()->json([
                    'url' => asset('storage/' . $path)
                ]);                
            }

            return response()->json([
                'error' => 'Không tìm thấy file'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Lỗi khi tải lên hình ảnh',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}