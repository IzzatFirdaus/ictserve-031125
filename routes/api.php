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

// Agentic memory import endpoint (supports token/authorization): not protected by auth:sanctum so agents can call with MEMORY_API_TOKEN
Route::post('/v1/memory/import', [\App\Http\Controllers\Api\MemoryController::class, 'import'])
    ->name('api.v1.memory.import')
    ->middleware('throttle:60,1');

Route::get('/v1/memory/search', [\App\Http\Controllers\Api\MemoryController::class, 'search'])
    ->name('api.v1.memory.search')
    ->middleware('throttle:120,1');

// Loan Applications API
Route::middleware(['auth:web'])->group(function () {
    Route::get('/loan-applications', [LoanApplicationController::class, 'index'])
        ->name('api.loan-applications.index');

    Route::get('/assets/search', [AssetSearchController::class, 'search'])
        ->name('api.assets.search');
});

// Backwards-compatible API routes (non-versioned)
Route::middleware(['auth:sanctum'])->name('api.')->group(function () {
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ApiTicketController::class, 'index'])
            ->name('index')
            ->middleware(['ability:read:tickets,admin:all', 'throttle:60,1']);

        Route::post('/', [\App\Http\Controllers\Api\ApiTicketController::class, 'store'])
            ->name('store')
            ->middleware(['ability:write:tickets,admin:all', 'throttle:60,1']);
    });

    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ApiLoanController::class, 'index'])
            ->name('index')
            ->middleware(['ability:read:loans,admin:all', 'throttle:60,1']);

        Route::post('/', [\App\Http\Controllers\Api\ApiLoanController::class, 'store'])
            ->name('store')
            ->middleware(['ability:write:loans,admin:all', 'throttle:60,1']);
    });
});

// Cross-Module Integration API Routes
Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.v1.')->group(function () {

    // Helpdesk Tickets API (Requirement 37.3)
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ApiTicketController::class, 'index'])
            ->name('index')
            ->middleware(['ability:read:tickets,admin:all', 'throttle:60,1']);

        Route::post('/', [\App\Http\Controllers\Api\ApiTicketController::class, 'store'])
            ->name('store')
            ->middleware(['ability:write:tickets,admin:all', 'throttle:60,1']);
    });

    // Loan Applications API (Requirement 37.3)
    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ApiLoanController::class, 'index'])
            ->name('index')
            ->middleware(['ability:read:loans,admin:all', 'throttle:60,1']);

        Route::post('/', [\App\Http\Controllers\Api\ApiLoanController::class, 'store'])
            ->name('store')
            ->middleware(['ability:write:loans,admin:all', 'throttle:60,1']);
    });

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

    // Memory sync - allow agentic sessions to push memory content
    // NOTE: Memory import is handled by the token-protected endpoint declared above.
    // Keep this route out of the Sanctum-protected group to avoid requiring the
    // 'sanctum' guard for agent token-based imports accessible via MEMORY_API_TOKEN.
});

// Performance Analytics (public - no auth required)
Route::post('/analytics/web-vitals', [WebVitalsController::class, 'store'])
    ->name('api.analytics.web-vitals')
    ->middleware('throttle:300,1'); // 300 requests per minute (high frequency metrics)

// Health Check Endpoints (public - for load balancers and monitoring)
Route::prefix('health')->name('api.health.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\HealthCheckController::class, 'basic'])
        ->name('basic');

    Route::get('/detailed', [\App\Http\Controllers\Api\HealthCheckController::class, 'detailed'])
        ->name('detailed');

    Route::get('/performance', [\App\Http\Controllers\Api\HealthCheckController::class, 'performance'])
        ->name('performance');
});

/*
|--------------------------------------------------------------------------
| Ollama AI Integration API Routes (v3.6.0)
|--------------------------------------------------------------------------
|
| Endpoint untuk integrasi AI Ollama dengan sistem ICTServe.
| Menyokong True Hybrid Architecture (tetamu + authenticated).
| Semua respons dalam Bahasa Melayu sahaja (D15 v3.6.0).
|
| Requirements: 1.1, 1.4, 2.1, 2.5, 3.1, 3.2, 3.4, 3.6, 7.1, 8.4
|
*/

// Ollama AI API v1 Routes
Route::prefix('v1/ollama')->name('api.v1.ollama.')->middleware([
    \App\Http\Middleware\OllamaApiMiddleware::class,
])->group(function () {

    // FAQ Bot API - Hybrid Access (Guest + Authenticated)
    // Requirements: 1.1, 1.4, 7.1, 8.4
    Route::prefix('faq')->name('faq.')->group(function () {
        // Public FAQ query endpoint (guest access supported)
        Route::post('/query', [\App\Http\Controllers\Api\FaqController::class, 'query'])
            ->name('query')
            ->middleware('throttle:60,1'); // 60 requests per minute

        // Conversation history (optional auth)
        Route::get('/history', [\App\Http\Controllers\Api\FaqController::class, 'history'])
            ->name('history')
            ->middleware('throttle:120,1');

        // Claim guest conversation (requires auth)
        Route::post('/claim', [\App\Http\Controllers\Api\FaqController::class, 'claimConversation'])
            ->name('claim')
            ->middleware(['auth:sanctum', 'throttle:30,1']);
    });

    // Document Analysis API - Admin Only
    // Requirements: 2.1, 2.5, 7.1
    Route::prefix('documents')->name('documents.')->middleware(['auth:sanctum'])->group(function () {
        // List documents
        Route::get('/', [\App\Http\Controllers\Api\DocumentController::class, 'index'])
            ->name('index')
            ->middleware(['ability:admin:all', 'throttle:60,1']);

        // Upload document
        Route::post('/upload', [\App\Http\Controllers\Api\DocumentController::class, 'upload'])
            ->name('upload')
            ->middleware(['ability:admin:all', 'throttle:30,1']);

        // Get document status
        Route::get('/{id}/status', [\App\Http\Controllers\Api\DocumentController::class, 'status'])
            ->name('status')
            ->middleware(['ability:admin:all', 'throttle:120,1']);

        // Reprocess failed document
        Route::post('/{id}/reprocess', [\App\Http\Controllers\Api\DocumentController::class, 'reprocess'])
            ->name('reprocess')
            ->middleware(['ability:admin:all', 'throttle:30,1']);

        // Delete document
        Route::delete('/{id}', [\App\Http\Controllers\Api\DocumentController::class, 'destroy'])
            ->name('destroy')
            ->middleware(['ability:admin:all', 'throttle:30,1']);

        // Document statistics
        Route::get('/stats', [\App\Http\Controllers\Api\DocumentController::class, 'stats'])
            ->name('stats')
            ->middleware(['ability:admin:all', 'throttle:60,1']);
    });

    // Auto-Reply API - Admin/Superuser Only
    // Requirements: 3.1, 3.2, 3.4, 3.6
    Route::prefix('auto-reply')->name('auto-reply.')->group(function () {
        // Generate draft (requires auth)
        Route::post('/generate', [\App\Http\Controllers\Api\AutoReplyController::class, 'generate'])
            ->name('generate')
            ->middleware(['auth:sanctum', 'ability:admin:all', 'throttle:30,1']);

        // List drafts (admin only)
        Route::get('/', [\App\Http\Controllers\Api\AutoReplyController::class, 'index'])
            ->name('index')
            ->middleware(['auth:sanctum', 'ability:admin:all', 'throttle:60,1']);

        // List pending drafts
        Route::get('/pending', [\App\Http\Controllers\Api\AutoReplyController::class, 'pending'])
            ->name('pending')
            ->middleware(['auth:sanctum', 'ability:admin:all', 'throttle:60,1']);

        // Get draft status
        Route::get('/{id}/status', [\App\Http\Controllers\Api\AutoReplyController::class, 'status'])
            ->name('status')
            ->middleware(['auth:sanctum', 'ability:admin:all', 'throttle:120,1']);

        // Email token action (approve/reject without auth)
        Route::post('/email-action', [\App\Http\Controllers\Api\AutoReplyController::class, 'emailAction'])
            ->name('email-action')
            ->middleware('throttle:30,1');

        // Approve draft (supports token-based approval)
        Route::post('/{id}/approve', [\App\Http\Controllers\Api\AutoReplyController::class, 'approve'])
            ->name('approve')
            ->middleware('throttle:30,1'); // Token-based approval doesn't require auth

        // Reject draft (supports token-based rejection)
        Route::post('/{id}/reject', [\App\Http\Controllers\Api\AutoReplyController::class, 'reject'])
            ->name('reject')
            ->middleware('throttle:30,1'); // Token-based rejection doesn't require auth
    });
});
