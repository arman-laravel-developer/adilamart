<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function getProducts ()
    {
        try {
            $products = Product::where('status', 1)->orderBy('priority', 'asc')->get();
    
            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Products not found',
                    'data' => null
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getProductById ($id)
    {
        try {
            $product = Product::with('category', 'productImages', 'colors', 'sizes', 'reviews')->find($id);
    
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                    'data' => null
                ], 404);
            }
    
            $related = Product::with('reviews', 'category')
                ->where('status', 1)
                ->where('cat_id', $product->category ? $product->category->id : null)
                ->where('id', '!=', $product->id)
                ->get();
    
            return response()->json([
                'success' => true,
                'message' => 'Product retrieved successfully',
                'data' => [
                    'product' => $product,
                    'related' => $related
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function productAddtoCart (Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ip_address' => 'required|ip',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $ip_address = $request->ip_address;
        $oldCartProduct = Cart::where('product_id', $id)->where('ip_address', $ip_address)->first();

        //Check Previos Cart Product Type...
        $cartProducts = Cart::where('ip_address', $ip_address)->get();
        $lastCartProduct = Cart::where('ip_address', $ip_address)->with('product')->orderBy('id', 'desc')->first();
        $product = Product::find($id);
        $currentCartProduct = Product::find($product->id);

        if($cartProducts->count()>0){
            if($lastCartProduct->product->b_product_id == null && $currentCartProduct->b_product_id != null){
                foreach($cartProducts as $cart){
                    $cart->delete();
                }
            }
            elseif($lastCartProduct->product->b_product_id != null && $currentCartProduct->b_product_id == null){
                foreach($cartProducts as $cart){
                    $cart->delete();
                }
            }
        }
        //Check Previos Cart Product Type...
        
        try {
            if ($oldCartProduct) {
                $oldCartProduct->qty += $request->qty;
                $oldCartProduct->save();
                $cartItem = $oldCartProduct;
            } else {
                $cartItem = Cart::create([
                    'ip_address' => $request->ip_address,
                    'product_id' => $id,
                    'qty' => $request->qty,
                    'price' => $request->price,
                    'color' => $request->color,
                    'size'  => $request->size,
                ]);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'data' => $cartItem
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function deleteCart ($id)
    {
        try {
            $cart = Cart::find($id);
    
            if ($cart == null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found',
                    'data' => null
                ], 404);
            }
    
            // Deleting the cart
            $cart->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Cart deleted successfully',
                'data' => $cart
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cart. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getProductsByCatId ($cat_id)
    {
        try {
            $products = Product::where('status', 1)->where('cat_id', $cat_id)->orderBy('priority', 'asc')->get();
    
            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Products not found',
                    'data' => null
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getProductsBySubCatId ($subcat_id)
    {
        try {
            $products = Product::where('status', 1)->where('sub_cat_id', $subcat_id)->orderBy('priority', 'asc')->get();
    
            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Products not found',
                    'data' => null
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function countCartProducts($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid IP address',
                'data' => null
            ], 400);
        }

        try {
            $countProducts = Cart::where('ip_address', $ip)->count();
    
            return response()->json([
                'success' => true,
                'message' => 'Cart products count retrieved successfully',
                'data' => $countProducts
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve products count', [
                'ip' => $ip,
                'exception' => $e
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products count. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getCartProducts ($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid IP address',
                'data' => null
            ], 400);
        }

        try {
            $cartProducts = Cart::where('ip_address', $ip)->with('product')->get();
            $subTotal = 0;
            foreach($cartProducts as $product){
                $subTotal = $subTotal + $product->price * $product->qty;
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Cart products retrieved successfully',
                'data' => [
                    'carts' => $cartProducts,
                    'subTotal' => $subTotal
                ]
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve cart products', [
                'ip' => $ip,
                'exception' => $e
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cart products.' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getProductsSearchByName ($p_name)
    {
        try {
            $products = Product::where('name', 'like', '%' . $p_name . '%')->where('status', 1)->orderBy('priority', 'asc')->get();
    
            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Products not found',
                    'data' => null
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
