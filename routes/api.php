<?php

use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

// , 'auth:sanctum'

Route::middleware(['api',])->group(function () {
    Route::prefix('projects/{project_id}')->group(function () {
        Route::get('/tasks', [TaskController::class, 'index']);
        Route::post('/tasks', [TaskController::class, 'store']);
    });

    Route::prefix('tasks')->group(function () {
        Route::get('/{id}', [TaskController::class, 'show']);
        Route::put('/{id}', [TaskController::class, 'update']);
        Route::get('/{id}/attachment', [TaskController::class, 'attachment']);
        Route::patch('/{id}/attach', [TaskController::class, 'attach']);
        Route::patch('/{id}/detach', [TaskController::class, 'detach']);
        Route::delete('/{id}', [TaskController::class, 'destroy']);
    });
});
