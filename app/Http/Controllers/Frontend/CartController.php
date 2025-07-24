<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\RelatedProduct;
use App\Models\SubDistrict;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function addToCart(Request $request, Product $product, $cart_type)
    {

        $ip_address = $request->ip();

        //Check Previos Cart Product Type...
        $cartProducts = Cart::where('ip_address', $ip_address)->get();
        $lastCartProduct = Cart::where('ip_address', $ip_address)->with('product')->orderBy('id', 'desc')->first();
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

        $oldCartProduct = Cart::where('product_id', $product->id)->where('ip_address', $ip_address)->first();

        $action = $cart_type;
        if($action === 'add_cart'){
            //if cart already have this product
            if ($oldCartProduct){
                $oldCartProduct->qty = $oldCartProduct->qty+1;
                $oldCartProduct->save();
            }
            else{
                if (auth()->check()){
                    $cartProduct = new Cart();
                    $cartProduct->product_id = $product->id;
                    $cartProduct->user_id = auth()->user()->id;
                    $cartProduct->qty = 1;
                    if($product->discount_price != null){
                        $cartProduct->price = $product->discount_price;
                    }
                    if($product->discount_price == null){
                        $cartProduct->price = $product->regular_price;
                    }
                    $cartProduct->save();
                }else{
                    $cartProduct = new Cart();
                    $cartProduct->product_id = $product->id;
                    $cartProduct->ip_address = $request->ip();
                    $cartProduct->qty = 1;
                    if($product->discount_price != null){
                        $cartProduct->price = $product->discount_price;
                    }
                    if($product->discount_price == null){
                        $cartProduct->price = $product->regular_price;
                    }
                    $cartProduct->save();
                }
            }

            // return redirect()->back()->with('success', 'Added to cart!');
            return redirect()->back();
        }

        else{
            //if cart already have this product
            if ($oldCartProduct){
                $oldCartProduct->qty = $oldCartProduct->qty+1;
                $oldCartProduct->save();
            }
            else{
                if (auth()->check()){
                    $cartProduct = new Cart();
                    $cartProduct->product_id = $product->id;
                    $cartProduct->user_id = auth()->user()->id;
                    $cartProduct->qty = 1;
                    if($product->discount_price != null){
                        $cartProduct->price = $product->discount_price;
                    }
                    if($product->discount_price == null){
                        $cartProduct->price = $product->regular_price;
                    }
                    $cartProduct->save();
                }else{
                    $cartProduct = new Cart();
                    $cartProduct->product_id = $product->id;
                    $cartProduct->ip_address = $request->ip();
                    $cartProduct->qty = 1;
                    if($product->discount_price != null){
                        $cartProduct->price = $product->discount_price;
                    }
                    if($product->discount_price == null){
                        $cartProduct->price = $product->regular_price;
                    }
                    $cartProduct->save();
                }
            }

            // return redirect('/checkout')->with('success', 'Added to cart!');
            return redirect('/checkout');
        }
    }
    public function addToCartDetailsPage(Request $request, $id)
    {
        //Check Previos Cart Product Type...
        $ip_address = $request->ip();
        $cartProducts = Cart::where('ip_address', $ip_address)->get();
        $lastCartProduct = Cart::where('ip_address', $ip_address)->with('product')->orderBy('id', 'desc')->first();
        $currentCartProduct = Product::find($id);

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

        $action = $request->input('action');
        if($action === 'addToCart'){
            if (auth()->check()){
                $cartProduct = new Cart();
                $cartProduct->product_id = $request->product_id;
                $cartProduct->user_id = auth()->user()->id;
                $cartProduct->qty = $request->qty;
                $cartProduct->color = $request->color;
                $cartProduct->size = $request->size;
                $cartProduct->price = $request->price;
                $cartProduct->save();
            }else{
                $cartProduct = new Cart();
                $cartProduct->product_id = $request->product_id;
                $cartProduct->ip_address = $request->ip();
                $cartProduct->qty = $request->qty;
                $cartProduct->color = $request->color;
                $cartProduct->size = $request->size;
                $cartProduct->price = $request->price;
                $cartProduct->save();
            }
            // return redirect()->back()->with('success', 'Added to cart!');
            return redirect()->back();
        }

        else{
            if (auth()->check()){
                $cartProduct = new Cart();
                $cartProduct->product_id = $request->product_id;
                $cartProduct->user_id = auth()->user()->id;
                $cartProduct->qty = $request->qty;
                $cartProduct->color = $request->color;
                $cartProduct->size = $request->size;
                $cartProduct->price = $request->price;
                $cartProduct->save();
            }else{
                $cartProduct = new Cart();
                $cartProduct->product_id = $request->product_id;
                $cartProduct->ip_address = $request->ip();
                $cartProduct->qty = $request->qty;
                $cartProduct->color = $request->color;
                $cartProduct->size = $request->size;
                $cartProduct->price = $request->price;
                $cartProduct->save();
            }
            // return redirect('/checkout')->with('success', 'Added to cart!');
            return redirect('/checkout');
        }
    }

    public function cartProducts()
    {
        $products = Cart::with('product')->orWhere('user_id', auth()->check() ? Auth::guard('web')->user()->id : ' ')->orWhere('ip_address', request()->ip())->get();
        return response()->json($products, 200);
    }

    public function comboProducts()
    {
        $comboProducts = RelatedProduct::with('products')->get();
        return response()->json($comboProducts, 200);
    }

    public function totalCartProducts()
    {
        $totalProduct = DB::table('carts')->orWhere('user_id', auth()->check() ? Auth::guard('web')->user()->id : ' ')->orWhere('ip_address', request()->ip())->sum('qty');
        return response()->json($totalProduct);
    }

    public function totalCartProductsPrice()
    {
        $totalProductPrice = DB::table('carts')->orWhere('user_id', auth()->check() ? Auth::guard('web')->user()->id : ' ')->orWhere('ip_address', request()->ip())->sum('price');
        return response()->json($totalProductPrice);
    }

    public function removeCartProduct($id)
    {
        $removeCartProduct = Cart::find($id);
        $removeCartProduct->delete();
        return response()->json($removeCartProduct, 200);
    }


    //Cart product
    public function userCartProducts()
    {
        if (auth()->guard('web')->check()){
            $auth_user = Auth::guard('web')->user()->id;
            return view('frontend.cart.products', compact('auth_user'));
        }else{
            $auth_user = Cart::where('ip_address', request()->ip())->with('product')->get();
            return view('frontend.v-2.cart.products', compact('auth_user'));
        }

    }

     public function getUserCartProducts($id)
     {
         $getUserProducts = Cart::orWhere('user_id', $id)->orWhere('ip_address', \request()->ip())->with('product')->get();
         return response()->json($getUserProducts, 200);
     }

     public function updateCartProduct(Request $request, $id)
     {
         DB::table('carts')->where('id', $id)->increment('qty');
         $cart = DB::table('carts')->where('id', $id)->first();
         $product = Product::where('id', $cart->product_id)->first();
         $productPrice = $product->discount_price ? $product->discount_price : $product->regular_price;
         DB::table('carts')->where('id', $id)->update(['price' => $cart->qty * $productPrice]);
         return response()->json($id);
     }

     public function decrementCartProduct(Request $request, $id)
     {
         DB::table('carts')->where('id', $id)->decrement('qty');
         $cartDecrement = DB::table('carts')->where('id', $id)->first();
         $product = Product::where('id', $cartDecrement->product_id)->first();
         $productPrice = $product->discount_price ? $product->discount_price : $product->regular_price;
         DB::table('carts')->where('id', $id)->update(['price' => $cartDecrement->qty * $productPrice]);
         return response()->json($id, 200);
     }

     public function cartUpdate(Request $request, $id)
     {
         $cartUpdate = Cart::find($id);
         $product = Product::where('id', $cartUpdate->product_id)->first();
         $cartUpdate->qty = $request->qty;
         $cartUpdate->save();
         return redirect()->back()->with('success', 'Cart has been updated');
     }

    public function subDistrictList($id)
    {
        $sub_district_name = SubDistrict::with('district')->where('district_id', $id)->get();
        return response()->json($sub_district_name, 200);
    }

    public function cartProductDelete($id)
    {
        $cartProductDelete = Cart::find($id);
        $cartProductDelete->delete();
        return redirect()->back()->with('success', 'Product has been deleted from cart');
    }
}
