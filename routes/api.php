<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProductController;
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
        // Wishlist, Cart, Price Alerts, Favorites — Sprint 4
    });

    // ── Merchant routes ───────────────────────────────────────────────────
    Route::middleware('role:merchant')->prefix('merchant')->group(function () {
        // Merchant dashboard — Sprint 5
    });
});
