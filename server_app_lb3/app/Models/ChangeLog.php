<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChangeLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['entity_name', 'entity_id', 'old_values', 'new_values', 'created_by'];

    public function author() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Logs entity changes
     *
     * The model must be dirty in order to find changed values
     */
    static public function log_entity_changes(Model $entity) : void
    {
        if (! $entity->isDirty())
            return;

        $new_values = [];
        $old_values = [];
        foreach ($entity->getDirty() as $field => $value)
        {
            if (in_array($field, $entity->getHidden()))
            {
                $new_values[$field] = '<SECRET>';
                $old_values[$field] = '<SECRET>';
            }
            else
            {
                $new_values[$field] = $value;
                $old_values[$field] = $entity->getOriginal($field);
            }
        }

        static::insert([
            'entity_name' => $entity->getMorphClass(),
            'entity_id' => $entity->id,
            'old_values' => json_encode($old_values),
            'new_values' => json_encode($new_values),
            'created_by' => auth()->id(),
        ]);
    }
    public static function restoreEntity(int $changeLogId): bool
    {
        // Получаем запись из журнала изменений
        $changeLog = static::find($changeLogId);

        if (!$changeLog) {
            return false; // Запись не найдена
        }

        // Получаем класс сущности и идентификатор
        $entityClass = $changeLog->entity_name;
        $entityId = $changeLog->entity_id;

        // Находим сущность
        $entity = $entityClass::find($entityId);

        if (!$entity) {
            return false; // Сущность не найдена
        }

        // Восстанавливаем старые значения
        
        $oldValues = json_decode($changeLog->old_values, true);

        $newValues1 = [];
        foreach ($oldValues as $field => $value) {
            $newValues1[$field] = $entity->$field; // Текущие значения сущности
        }

        foreach ($oldValues as $field => $value) {
            $entity->$field = $value; // Восстанавливаем старое значение
        }
        // Сохраняем изменения
        $entity->save();

        $newValues = [];
        foreach ($oldValues as $field => $value) {
            $newValues[$field] = $entity->$field; // Текущие значения сущности
        }

        static::insert([
            'entity_name' => $entityClass,
            'entity_id' => $entityId,
            'old_values' => json_encode($newValues1), // Текущие значения (старые значения)
            'new_values' => json_encode($newValues), // Старые значения (новые после восстановления)
            'created_by' => auth()->id(),
        ]);

        return true;
    
    }
}

