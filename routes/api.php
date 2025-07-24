<?php

use App\Http\Controllers\Api\GeneralDataController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/feature/products', [App\Http\Controllers\Frontend\FeatureProductController::class, 'products']);
Route::get('/feature/products/list', [App\Http\Controllers\Frontend\FeatureProductController::class, 'list']);
Route::get('/hot/products', [App\Http\Controllers\Frontend\HotProductController::class, 'products']);
Route::get('/hot/products/list', [App\Http\Controllers\Frontend\HotProductController::class, 'list']);
Route::get('/discount/products', [App\Http\Controllers\Frontend\DiscountProductController::class, 'products']);
Route::get('/discount/products/list', [App\Http\Controllers\Frontend\DiscountProductController::class, 'list']);
Route::get('/new-arrival/products', [App\Http\Controllers\Frontend\NewArrivalProductController::class, 'products']);
Route::get('/new-arrival/products/list', [App\Http\Controllers\Frontend\NewArrivalProductController::class, 'list']);
Route::get('/top/products', [App\Http\Controllers\Frontend\TopProductController::class, 'products']);
Route::get('/top/products/list', [App\Http\Controllers\Frontend\TopProductController::class, 'list']);


//For products list page filtering route
    Route::get('/get/categories', [App\Http\Controllers\Frontend\FrontendController::class, 'categories']);
    Route::get('/get/subcategories', [App\Http\Controllers\Frontend\FrontendController::class, 'subcategories']);
    Route::get('/get/brands', [App\Http\Controllers\Frontend\FrontendController::class, 'brands']);
    Route::post('/getFilterProduct', [App\Http\Controllers\Frontend\FrontendController::class, 'productFiltering']);
    Route::get('/get/all/category', [App\Http\Controllers\Frontend\FrontendController::class, 'getAllCategory']);
    Route::get('/shop-products', [App\Http\Controllers\Frontend\FrontendController::class, 'shopProducts']);
    Route::get('/page-products/{type}', [App\Http\Controllers\Frontend\FrontendController::class, 'getPageProducts']);
//For products list page filtering route



//Supplier products controller here
Route::get('/supplier/products/{vendor_id}', [App\Http\Controllers\Frontend\SupplierController::class, 'products']);
Route::delete('/product/delete/{id}', [App\Http\Controllers\Frontend\SupplierController::class, 'productsDelete']);

//Category wise products
Route::get('/category-products/{id}', [App\Http\Controllers\Frontend\CategoryProductsController::class, 'products']);
Route::get('/subcategory-products/{id}', [App\Http\Controllers\Frontend\CategoryProductsController::class, 'subcategory_products']);

Route::post('/order/delivery/charge/update/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'orderUpdate']);
Route::post('/order/price/update/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'orderPriceUpdate']);
Route::post('/order/product/qty/update/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'qtyUpdate']);
Route::post('/product/qty/update/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'productQtyUpdate']);
Route::post('/order/product/color/update/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'productColorUpdate']);
Route::post('/order/product/size/update/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'productSizeUpdate']);

//Pathao Webhook for Order Status...
Route::post('/webhooks/pathao/order-status', [\App\Http\Controllers\Admin\ReportController::class, 'webHookForOrderStatus']);
//Pathao Webhook for Order Status...



//API for frontend...

//General Data...
Route::get('/gtm', [GeneralDataController::class, 'gtm']);
Route::get('/general-data', [GeneralDataController::class, 'getSettings']);
Route::get('/categories', [GeneralDataController::class, 'getCategories']);
Route::get('/home-sliders', [GeneralDataController::class, 'getSliders']);
Route::get('/about-us', [GeneralDataController::class, 'aboutUs']);
Route::get('/privacy-policy', [GeneralDataController::class, 'privacyPolicy']);
Route::get('/terms-conditions', [GeneralDataController::class, 'termsConditions']);
Route::get('/refund-policy', [GeneralDataController::class, 'refundPolicy']);
Route::get('/payment-policy', [GeneralDataController::class, 'paymentPolicy']);
Route::post('/contact-store', [GeneralDataController::class, 'ContactStore']);
Route::get('/offer-time', [GeneralDataController::class, 'getOfferTime']);


//Products API...
Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('/product/details/{id}', [ProductController::class, 'getProductById']);
Route::get('/category-products/{cat_id}', [ProductController::class, 'getProductsByCatId']);
Route::get('/subcategory-products/{subcat_id}', [ProductController::class, 'getProductsBySubCatId']);
Route::get('/product-search/{p_name}', [ProductController::class, 'getProductsSearchByName']);

//Add to Cart & Checkout...
Route::post('/product/add-to-cart/{id}', [ProductController::class, 'productAddtoCart']);
Route::post('/confirm-order', [OrderController::class, 'confirmOrder']);

Route::get('/cart-products-count/{ip}', [ProductController::class, 'countCartProducts']);
Route::get('/get-cart-products/{ip}', [ProductController::class, 'getCartProducts']);
Route::get('/delete-cart/{id}', [ProductController::class, 'deleteCart']);

//Offer...
Route::get('/offers', [GeneralDataController::class, 'getOffers']);
Route::get('/offer-product/{slug}', [GeneralDataController::class, 'getOfferProducts']);

//Order Details...
Route::get('/order-details/{orderId}', [OrderController::class, 'orderDetails']);
