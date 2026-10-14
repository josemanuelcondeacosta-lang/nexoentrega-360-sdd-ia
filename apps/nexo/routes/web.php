<?php

use App\Http\Middleware\RequireIdentity;
use App\Modules\AI\AiController;
use App\Modules\Identity\IdentityController;
use App\Modules\Inventory\TransitionController;
use App\Modules\Queries\QueryController;
use App\Modules\Requests\RequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::view('/', 'nexo');
Route::get('/health', fn () => response()->json(['status' => 'ok']));
Route::get('/ready', function () {
    try {
        DB::select('SELECT 1');
        if (! Schema::hasTable('ai_attempts')) {
            return response()->json(['status' => 'not_ready', 'database' => 'up'], 503);
        }

        return response()->json(['status' => 'ready', 'database' => 'up']);
    } catch (Throwable $e) {
        return response()->json(['status' => 'not_ready', 'database' => 'down'], 503);
    }
});
Route::prefix('api')->group(function () {
    Route::get('/auth/csrf', [IdentityController::class, 'csrf']);
    Route::post('/auth/login', [IdentityController::class, 'login']);
    Route::middleware(RequireIdentity::class)->group(function () {
        Route::get('/auth/me', [IdentityController::class, 'me']);
        Route::post('/auth/logout', [IdentityController::class, 'logout']);
        Route::get('/requests', [QueryController::class, 'index']);
        Route::get('/requests/{requestId}', [QueryController::class, 'show']);
        Route::post('/requests', fn (Request $r) => (new RequestService)->save($r));
        Route::patch('/requests/{requestId}', fn (Request $r, string $requestId) => (new RequestService)->save($r, $requestId));
        Route::post('/requests/{requestId}/{action}', [TransitionController::class, 'run'])->whereIn('action', ['send', 'reserve', 'dispatch', 'deliver', 'cancel']);
        Route::get('/inventory', [QueryController::class, 'inventory']);
        Route::get('/catalog', [QueryController::class, 'catalog']);
        Route::get('/dashboard', [QueryController::class, 'dashboard']);
        Route::get('/audit', [QueryController::class, 'audit']);
        Route::get('/requests/{requestId}/audit', [QueryController::class, 'audit']);
        Route::post('/ai/summaries', [AiController::class, 'create']);
        Route::post('/ai/summaries/{summaryId}/approve', [AiController::class, 'approve']);
    });
});
