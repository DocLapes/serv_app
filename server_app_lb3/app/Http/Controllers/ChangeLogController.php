<?php

namespace App\Http\Controllers;

use App\Models\ChangeLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChangeLogController
{
    /**
     * Restore the entity based on change log ID.
     *
     * @param  Request  $request
     * @param  int  $changeLogId
     * @return JsonResponse
     */
    public function restoreEntity(Request $request, int $changeLogId): JsonResponse
    {
        // Вызов метода восстановления
        $success = ChangeLog::restoreEntity($changeLogId);

        if ($success) {
            return response()->json(['message' => 'Сущность успешно восстановлена!'], 200);
        } else {
            return response()->json(['message' => 'Не удалось восстановить сущность.'], 404);
        }
    }
}

