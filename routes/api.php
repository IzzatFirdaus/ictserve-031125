<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AssetReturnController;
use App\Http\Controllers\Api\AssetSearchController;
use App\Http\Controllers\Api\LoanApplicationController;
use App\Http\Controllers\Api\TicketAssetLinkingController;
use App\Http\Controllers\Api\WebVitalsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Loan Applications API
Route::middleware(['auth:web'])->group(function () {
    Route::get('/loan-applications', [LoanApplicationController::class, 'index'])
        ->name('api.loan-applications.index');

    Route::get('/assets/search', [AssetSearchController::class, 'search'])
        ->name('api.assets.search');
});

// Cross-Module Integration API Routes
Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.v1.')->group(function () {

    // Asset Return Notifications
    Route::prefix('asset-returns')->name('asset-returns.')->group(function () {
        Route::post('/notify-damage', [AssetReturnController::class, 'notifyDamage'])
            ->name('notify-damage')
            ->middleware('throttle:60,1'); // 60 requests per minute

        Route::post('/create-maintenance-ticket', [AssetReturnController::class, 'createMaintenanceTicket'])
            ->name('create-maintenance-ticket')
            ->middleware('throttle:60,1');
    });

    // Ticket-Asset Linking
    Route::prefix('ticket-asset')->name('ticket-asset.')->group(function () {
        Route::post('/link', [TicketAssetLinkingController::class, 'linkTicketToAsset'])
            ->name('link')
            ->middleware('throttle:120,1'); // 120 requests per minute

        Route::delete('/unlink/{ticket}', [TicketAssetLinkingController::class, 'unlinkTicketFromAsset'])
            ->name('unlink')
            ->middleware('throttle:120,1');

        Route::get('/ticket/{ticket}/asset', [TicketAssetLinkingController::class, 'getTicketAssets'])
            ->name('ticket-asset')
            ->middleware('throttle:180,1'); // 180 requests per minute (read-heavy)

        Route::get('/asset/{asset}/tickets', [TicketAssetLinkingController::class, 'getAssetTickets'])
            ->name('asset-tickets')
            ->middleware('throttle:180,1');
    });

    // Performance Analytics
    Route::post('/analytics/web-vitals', [WebVitalsController::class, 'store'])
        ->name('analytics.web-vitals')
        ->middleware('throttle:300,1'); // 300 requests per minute (high frequency metrics)
});
