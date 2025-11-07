<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Task $task)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Новая задача: {$this->task->title}")
            ->greeting('Здравствуйте!')
            ->line('Была создана новая задача.')
            ->line("**Задача:** {$this->task->title}")
            ->line("**Описание:** {$this->task->description}")
            ->line("**Статус:** {$this->task->status}")
            ->action('Посмотреть задачу', url("/tasks/{$this->task->id}"));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
        ];
    }
}