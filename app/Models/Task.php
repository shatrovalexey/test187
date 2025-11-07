<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Validation\Rule;

class Task extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected const STATUS = ['planned', 'in_progress', 'done',];

    protected $attributes = [
        'status' => 'planned',
    ];

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'completion_date',
        'assignee_id',
    ];

    protected $casts = [
        'completion_date' => 'date',
    ];

    /**
     * Правила валидации для создания задачи
     */
    public static function createRules(): array
    {
        return [
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'completion_date' => 'nullable|date',
            'assignee_id' => 'required|exists:users,id',
        ];
    }

    /**
     * Правила валидации для обновления задачи
     */
    public static function updateRules($taskId = null): array
    {
        return [
            'project_id' => 'sometimes|exists:projects,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['sometimes', 'required', Rule::in(static::STATUS)],
            'completion_date' => 'nullable|date',
            'assignee_id' => 'sometimes|exists:users,id',
        ];
    }

    /**
     * Сообщения об ошибках валидации
     */
    public static function validationMessages(): array
    {
        return [
            'project_id.required' => 'Проект обязателен для заполнения',
            'project_id.exists' => 'Выбранный проект не существует',
            'title.required' => 'Заголовок задачи обязателен для заполнения',
            'title.max' => 'Заголовок не может быть длиннее 255 символов',
            'status.required' => 'Статус задачи обязателен',
            'status.in' => 'Недопустимый статус задачи',
            'assignee_id.required' => 'Исполнитель задачи обязателен',
            'assignee_id.exists' => 'Выбранный исполнитель не существует',
            'completion_date.date' => 'Дата завершения должна быть корректной датой',
        ];
    }

    /**
     * Отношение к проекту
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Отношение к исполнителю (пользователю)
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachment')->singleFile();
    }
}