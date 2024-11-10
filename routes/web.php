<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\AuthAdmin;

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product_details');

// add to cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add_to_cart'])->name('cart.add');
Route::put('/cart/increase_quantity/{rowId}', [CartController::class, 'increase_cart_quantity'])->name('cart.qty.increase');
Route::put('/cart/decrease_quantity/{rowId}', [CartController::class, 'decrease_cart_quantity'])->name('cart.qty.decrease');
Route::delete('/cart/delete/item/{rowId}', [CartController::class, 'remove_item'])->name('cart.delete.item');
Route::delete('/cart/clear', [CartController::class, 'empty_cart'])->name('cart.empty');
Route::post('/cart/apply-coupon',[CartController::class,'apply_coupon_code'])->name('cart.coupon.apply');
Route::get('/cart/checkout', [CartController::class, 'checkOut'])->name('cart.checkOut');
Route::post('/place_order', [CartController::class, 'place_order'])->name('cart.place_order');
Route::get('/confirm_order', [CartController::class, 'order_confirm'])->name('cart.confirmation');

// add wishlish
Route::post('/wishlist/add', [WishlistController::class, 'add_to_wishlist'])->name('wishlist.add');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::delete('/wishlist/item/remove/{rowId}', [WishlistController::class, 'remove_item'])->name('wishlist.item.remove');
Route::delete('/wishlist/clear}', [WishlistController::class, 'empty_wishlist'])->name('wishlist.item.clear');
Route::post('/wishlist/move_to_cart/{rowId}', [WishlistController::class, 'move_to_cart'])->name('wishlist.move.to.cart');

Route::middleware('auth')->group(function(){
    Route::get('/account_dashboard', [UserController::class, 'index'])->name('user.index');
    Route::get('/account_orders', [UserController::class, 'orders'])->name('user.orders');
    Route::get('/account_order_details/{order_id}', [UserController::class, 'order_details'])->name('user.order_details');
    Route::put('/account-order/cancel-order',[UserController::class,'account_cancel_order'])->name('user.account_cancel_order');
});

Route::middleware('auth', AuthAdmin::class)->group(function(){
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    // brand
    Route::get('/admin/brand', [AdminController::class, 'brands'])->name('admin.brands');
    Route::get('/admin/brand/add', [AdminController::class, 'add_brand'])->name('admin.brand_add');
    Route::post('/admin/brand/store', [AdminController::class, 'brand_store'])->name('admin.brand_store');
    Route::get('/admin/brand/edit/{id}', [AdminController::class, 'brand_edit'])->name('admin.brand_edit');
    Route::put('/admin/brand/update', [AdminController::class, 'brand_update'])->name('admin.brand_update');
    Route::delete('/admin/brand/delete/{id}', [AdminController::class, 'brand_delete'])->name('admin.brand_delete');

    // categories
    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/category_add', [AdminController::class, 'category_add'])->name('admin.category_add');
    Route::post('/admin/category_store', [AdminController::class, 'category_store'])->name('admin.category_store');
    Route::get('/admin/category_edit/{id}', [AdminController::class, 'category_edit'])->name('admin.category_edit');
    Route::put('/admin/category/update', [AdminController::class, 'category_update'])->name('admin.category_update');
    Route::delete('/admin/category/delete/{id}', [AdminController::class, 'category_delete'])->name('admin.category_delete');

    // prodcu
    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/admin/product/add', [AdminController::class, 'product_add'])->name('admin.product_add');
    Route::post('/admin/product/store', [AdminController::class, 'product_store'])->name('admin.product_store');
    Route::get('/admin/product/edit/{id}', [AdminController::class, 'product_edit'])->name('admin.product_edit');
    Route::put('/admin/product/update', [AdminController::class, 'product_update'])->name('admin.product_update');
    Route::delete('/admin/product/delete/{id}', [AdminController::class, 'product_delete'])->name('admin.product_delete');

    // coupons
    Route::get('/admin/coupons', [AdminController::class, 'coupons'])->name('admin.coupons');
    Route::get('/admin/coupon/add', [AdminController::class, 'coupon_add'])->name('admin.coupon_add');
    Route::post('/admin/coupon/store', [AdminController::class, 'coupon_store'])->name('admin.coupon_store');
    Route::get('/admin/coupon/edit/{id}', [AdminController::class, 'coupon_edit'])->name('admin.coupon_edit');
    Route::put('/admi/coupon/update', [AdminController::class, 'coupon_update'])->name('admin.coupon_update');
    Route::delete('/add/coupon/delete/{id}', [AdminController::class, 'coupon_delete'])->name('admin.coupon_delete');

    // order
    Route::get('/admin/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/admin/order/details/{order_id}',[AdminController::class,'order_details'])->name('admin.order_details');
    Route::put('/admin/order/update-status',[AdminController::class,'update_order_status'])->name('admin.order.status.update');
});