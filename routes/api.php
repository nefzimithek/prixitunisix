<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PriceAlertController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — PrixTunisix
|--------------------------------------------------------------------------
| Sprint 1: Auth (register, login, logout, me)
| Sprint 2: Product catalog (categories, brands, products, offers)
| Sprint 3: Price history
| Sprint 4: Search, comparison, redirect + click tracking
|--------------------------------------------------------------------------
*/

// ── Public auth ───────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
});

// ── Public catalog (read-only, no auth required) ──────────────────────────
Route::get('categories',          [CategoryController::class, 'index']);
Route::get('categories/{category}', [CategoryController::class, 'show']);
Route::get('brands',              [BrandController::class, 'index']);
Route::get('brands/{brand}',      [BrandController::class, 'show']);
Route::get('products',            [ProductController::class, 'index']);
Route::get('products/{product}',  [ProductController::class, 'show']);
Route::get('products/{product}/offers', [ProductController::class, 'offers']);
Route::get('offers/{offer}/price-history', [OfferController::class, 'priceHistory']);

// Redirect logs the click then returns merchant URL (no auth required — user tracked if logged in)
Route::post('offers/{offer}/redirect', [OfferController::class, 'redirect']);

// ── Authenticated routes ──────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me',      [AuthController::class, 'me']);

    // ── Admin routes ──────────────────────────────────────────────────────
    Route::middleware('role:admin,employee')->prefix('admin')->group(function () {

        // Category management
        Route::post('categories',              [CategoryController::class, 'store']);
        Route::put('categories/{category}',    [CategoryController::class, 'update']);
        Route::delete('categories/{category}', [CategoryController::class, 'destroy']);

        // Brand management
        Route::post('brands',          [BrandController::class, 'store']);
        Route::put('brands/{brand}',   [BrandController::class, 'update']);
        Route::delete('brands/{brand}',[BrandController::class, 'destroy']);

        // Product management (validate, edit)
        Route::post('products',            [ProductController::class, 'store']);
        Route::put('products/{product}',   [ProductController::class, 'update']);
        Route::delete('products/{product}',[ProductController::class, 'destroy']);
    });

    // ── Client routes ─────────────────────────────────────────────────────
    Route::middleware('role:client')->prefix('client')->group(function () {
        // Wishlists
        Route::get('wishlists',                                      [WishlistController::class, 'index']);
        Route::post('wishlists',                                     [WishlistController::class, 'store']);
        Route::delete('wishlists/{wishlist}',                        [WishlistController::class, 'destroy']);
        Route::post('wishlists/{wishlist}/items',                    [WishlistController::class, 'addItem']);
        Route::delete('wishlists/{wishlist}/items/{item}',           [WishlistController::class, 'removeItem']);

        // Cart
        Route::get('cart',                  [CartController::class, 'show']);
        Route::post('cart/items',           [CartController::class, 'addItem']);
        Route::put('cart/items/{item}',     [CartController::class, 'updateItem']);
        Route::delete('cart/items/{item}',  [CartController::class, 'removeItem']);
        Route::delete('cart',               [CartController::class, 'clear']);

        // Price alerts
        Route::get('alerts',           [PriceAlertController::class, 'index']);
        Route::post('alerts',          [PriceAlertController::class, 'store']);
        Route::delete('alerts/{priceAlert}', [PriceAlertController::class, 'destroy']);

        // Favorites
        Route::get('favorites',                   [FavoriteController::class, 'index']);
        Route::post('favorites',                  [FavoriteController::class, 'store']);
        Route::delete('favorites/{productId}',    [FavoriteController::class, 'destroy']);
    });

    // ── Merchant routes ───────────────────────────────────────────────────
    Route::middleware('role:merchant')->prefix('merchant')->group(function () {
        Route::get('profile',            [MerchantController::class, 'profile']);
        Route::put('profile',            [MerchantController::class, 'updateProfile']);
        Route::get('offers',             [MerchantController::class, 'offers']);
        Route::post('offers',            [MerchantController::class, 'storeOffer']);
        Route::put('offers/{offer}',     [MerchantController::class, 'updateOffer']);
        Route::delete('offers/{offer}',  [MerchantController::class, 'deleteOffer']);
    });

    // ── Admin + Employee routes ───────────────────────────────────────────
    Route::middleware('role:admin,employee')->prefix('admin')->group(function () {
        // Category / Brand / Product management already declared above

        // Users & roles
        Route::get('users',                  [AdminController::class, 'users']);
        Route::put('users/{user}/role',      [AdminController::class, 'updateRole'])
            ->middleware('role:admin'); // only admin can change roles

        // Merchant verification
        Route::get('merchants',                       [AdminController::class, 'merchants']);
        Route::post('merchants/{merchant}/verify',    [AdminController::class, 'verifyMerchant']);

        // Product match review queue
        Route::get('product-matches',              [AdminController::class, 'productMatches']);
        Route::put('product-matches/{productMatch}', [AdminController::class, 'reviewMatch']);

        // Click analytics
        Route::get('analytics/clicks', [AdminController::class, 'clickAnalytics']);
    });
});
