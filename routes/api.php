<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API endpoints for mobile/cPanel integration (Default Branch ID = 2)
Route::get('/products', [ApiController::class, 'products']);
Route::get('/categories', [ApiController::class, 'categories']);
Route::get('/brands', [ApiController::class, 'brands']);
Route::get('/customers', [ApiController::class, 'customers']);
Route::get('/suppliers', [ApiController::class, 'suppliers']);

// Steadfast Courier Real-time Status Webhook
Route::match(['get', 'post', 'head'], '/steadfast/webhook', [\App\Http\Controllers\Api\SteadfastWebhookController::class, 'handleWebhook'])->name('steadfast.webhook');
Route::match(['get', 'post', 'head'], '/steadfast/hook', [\App\Http\Controllers\Api\SteadfastWebhookController::class, 'handleWebhook']);
Route::match(['get', 'post', 'head'], '/steadfast-webhook', [\App\Http\Controllers\Api\SteadfastWebhookController::class, 'handleWebhook']);

Route::match(['get', 'post', 'head'], '/woocommerce/webhook', [\App\Http\Controllers\Backend\WooCommerceController::class, 'handleWebhook'])->name('woocommerce.webhook');
Route::match(['get', 'post', 'head'], '/woocommerce/webl', [\App\Http\Controllers\Backend\WooCommerceController::class, 'handleWebhook']);
Route::match(['get', 'post', 'head'], '/woocommerce/webhooks', [\App\Http\Controllers\Backend\WooCommerceController::class, 'handleWebhook']);
Route::match(['get', 'post', 'head'], '/woocommerce/hook', [\App\Http\Controllers\Backend\WooCommerceController::class, 'handleWebhook']);


