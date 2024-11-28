

<?php
use App\Http\Controllers\ChangeLogController;

Route::post('/changelog/restore/{id}', [ChangeLogController::class, 'restoreEntity']);