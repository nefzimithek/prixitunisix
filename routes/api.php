<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — PrixTunisix
|--------------------------------------------------------------------------
| Phase 1 (Sprint 1): Auth
| Phase 2 (Sprint 2): Products, Categories, Brands, Offers
| Phase 3 (Sprint 3): Price History
| Phase 4 (Sprint 4): Search, Comparison, Redirect + Click tracking
|--------------------------------------------------------------------------
*/

// ── Public auth routes ────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
});

// ── Authenticated routes ──────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me',      [AuthController::class, 'me']);

    // ── Client routes ─────────────────────────────────────────────────────
    Route::middleware('role:client')->prefix('client')->group(function () {
        // Wishlist, Cart, Price Alerts, Favorites — Sprint 4
    });

    // ── Merchant routes ───────────────────────────────────────────────────
    Route::middleware('role:merchant')->prefix('merchant')->group(function () {
        // Merchant dashboard — Sprint 5
    });

    // ── Employee routes ───────────────────────────────────────────────────
    Route::middleware('role:employee')->prefix('employee')->group(function () {
        // Product match review — Sprint 6
    });

    // ── Admin routes ──────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Admin panel — Sprint 6
    });
});
