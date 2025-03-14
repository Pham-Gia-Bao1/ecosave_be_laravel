<?php
namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::where('user_id', Auth::id())->with('product.images')->get();
        return ApiResponse::success($wishlists, "Lấy danh sách wishlist thành công");
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
        ]);

        return ApiResponse::success($wishlist, "Sản phẩm đã được thêm vào wishlist");
    }

    public function destroy($id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('id', $id)->first();

        if (!$wishlist) {
            return ApiResponse::error("Không tìm thấy sản phẩm trong wishlist", [], 404);
        }

        $wishlist->delete();
        return ApiResponse::success([], "Sản phẩm đã được xóa khỏi wishlist");
    }

    public function getAllProductIds()
        {
            $productIds = Wishlist::where('user_id', Auth::id())->pluck('product_id');
            return ApiResponse::success($productIds, "Lấy danh sách product_id trong wishlist thành công");
        }

}
