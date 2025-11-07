<?php
namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\{Task, Project};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Notifications\TaskCreatedNotification;

/**
* Задача
*/
class TaskController extends Controller
{
    /**
    * Список
    *
    * @route GET /api/projects/{project_id}/tasks
    *
    * @param ?string status - статус
    * @param ?int assignee_id - исполнитель
    * @param ?string completion_date - дата завершения
    */
    public function index(Request $request, $projectId): JsonResponse
    {
        $tasks = Task::where('project_id', $projectId);

        if ($request->has('status') && $request->status)
            $tasks->where('status', $request->status);

        if ($request->has('assignee_id') && $request->assignee_id)
            $tasks->where('assignee_id', $request->assignee_id);

        if ($request->has('completion_date') && $request->completion_date)
            $tasks->whereDate('completion_date', $request->completion_date);

        return response()->json($tasks->get());
    }

    /**
    * Создать
    *
    * @route POST /api/projects/{project_id}/tasks
    *
    * @param int project_id - ID проекта
    * @param string title - название
    * @param string description - описание
    * @param string status - статус
    * @param int assignee_id - ID исполнителя
    * @param string completion_date - дата завершения
    */
    public function store(Request $request, $projectId): JsonResponse
    {
        try {
            $data = $request->validate(Task::createRules());
            $task = Task::create($data);
        } catch (ValidationException $exception) {
            return response()->json($exception->errors(), 400);
        } catch (\Throwable $exception) {
            return response()->json($exception->getMessage(), 400);
        }

        $task->assignee->notify(new TaskCreatedNotification($task));

        return response()->json($task, 201);
    }

    /**
    * Показать
    *
    * @route GET /api/tasks/{id}
    */
    public function show($id): JsonResponse
    {
        return response()->json(Task::findOrFail($id));
    }

    /**
    * Обновить
    *
    * @route PUT /api/tasks/{id}
    *
    * @param int project_id - ID проекта
    * @param string title - название
    * @param string description - описание
    * @param string status - статус
    * @param int assignee_id - ID исполнителя
    * @param string completion_date - дата завершения
    */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $data = $request->validate(Task::updateRules());
        } catch (ValidationException $exception) {
            return response()->json($exception->errors(), 400);
        }

        $result = Task::findOrFail($id)->update($data);

        return response()->json($result);
    }

    /**
    * Обновить вложение
    *
    * @route PATCH /api/tasks/{id}/attach
    *
    * @param resource attachment - вложение
    */
    public function attach(Request $request, $id): JsonResponse
    {
        if (!$request->hasFile('attachment'))
            return response()->json(false, 400);

        $result = Task::findOrFail($id)->addMediaFromRequest('attachment')
            ->toMediaCollection();

        return response()->json($result, 200);
    }

    /**
    * Удалить вложение
    *
    * @route PATCH /api/tasks/{id}/detach
    */
    public function detach(Request $request, $id): JsonResponse
    {
        Task::findOrFail($id)->clearMediaCollection();

        return response()->json(true, 200);
    }

    /**
    * Получить вложение
    *
    * @route GET /api/tasks/{id}/attachment
    */
    public function attachment(Request $request, $id): JsonResponse
    {
        if (!$media = Task::findOrFail($id)->media()->first())
            return response()->json(false, 404);

        return response()->download($media->getPath(), $media->file_name);
    }

    /**
    * Удалить
    *
    * @route DELETE /api/tasks/{id}
    */
    public function destroy($id): JsonResponse
    {
        Task::findOrFail($id)->delete();

        return response()->json(true, 204);
    }
}
