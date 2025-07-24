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

class OrderController extends Controller
{
    public function confirmOrder (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ip_address' => 'required|ip',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'delivery_area' => 'required|numeric',
            'customer_address' => 'required|string|min:10',
            'price' => 'required|numeric|min:0',
            'product_quantity' => 'required|integer',
            'payment_type' => 'required|string',
            'order_type' => 'required|string',
            'products' => 'required|array',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.qty' => 'required|integer|min:1',
            'products.*.size' => 'sometimes',
            'products.*.color' => 'sometimes',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }
    
        try {
            DB::beginTransaction();

            //Check Product Type...
            $data = $request->all();
            $firstProductId = $data['products'][0]['id'];
            $product = Product::find($firstProductId);
            
            // Create Order
            $order = new Order();
            if($product->b_product_id == null){
                $order->is_dropshipping = false;
            }
            if($product->b_product_id != null){
                $order->is_dropshipping = true;
            }
            $order->name = $request->customer_name;
            $order->phone = $request->customer_phone;
            $order->area = $request->delivery_area;
            $order->address = $request->customer_address;
            $order->orderId = $order->invoiceNumber();
            $order->price = $request->price;
            $order->qty = $request->product_quantity;
            $order->payment_type = $request->payment_type;
            $order->order_type = $request->order_type;
    
            $customerCheck = Order::where('phone', $request->customer_phone)->first();
            $order->customer_type = $customerCheck ? 'Old Customer' : 'New Customer';
    
            // Assign to employee
            $users = Admin::where('name', '!=', 'admin')->where('is_active', 1)
                ->whereDate('limit_updated_at', '!=', \Illuminate\Support\Carbon::today())->get();
    
            $session_user = Session::get('id');
            if ($session_user && session('name') != 'admin') {
                $order->employee_id = $session_user;
            } elseif ($users->isNotEmpty()) {
                $randomUser = $users->random();
                $order->employee_id = $randomUser->id;
    
                $assigned_employee_order = Order::where('employee_id', $randomUser->id)
                    ->whereDate('created_at', \Illuminate\Support\Carbon::today())
                    ->count();
    
                if ($assigned_employee_order >= $randomUser->order_limit) {
                    $randomUser->is_limit = true;
                    $randomUser->limit_updated_at = now();
                    $randomUser->save();
                }
            } else {
                $admin = Admin::first();
                $order->employee_id = $admin->id;
            }
    
            $order->save();
    
            // Create Order Details
            foreach ($request->products as $productData) {
                $productOrder = new OrderDetails();
                $productOrder->order_id = $order->id;
                $productOrder->product_id = $productData['id'];
                $productOrder->qty = $productData['qty'];
                $productOrder->price = $productData['price'];
                $productOrder->size = $productData['size'] ?? null;
                $productOrder->color = $productData['color'] ?? null;
                $productOrder->save();
            }

            //Delete Cart Products...
            $cartProducts = Cart::where('ip_address', $request->ip_address)->get();
            foreach($cartProducts as $product){
                $product->delete();
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Order confirmed successfully',
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm order. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function orderDetails ($orderId)
    {
        try {
            $order = Order::with('orderDetails')->where('orderId', $orderId)->first();
    
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                    'data' => null
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Order retrieved successfully',
                'data' => [
                    'order' => $order
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve order. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
