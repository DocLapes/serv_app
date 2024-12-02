

<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChangeLogController;
use App\Http\Controllers\RolesController;
use App\Http\Middleware\EnsureUserHasPermission;

Route::controller(ChangeLogController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::middleware(EnsureUserHasPermission::class . ':get-story-role')
            ->post('/changelog/restore/{id}', 'restoreEntity');
    });