<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'status' => $this->faker->randomElement(['planned', 'in_progress', 'done']),
            'completion_date' => $this->faker->optional(0.3)->dateTimeBetween('now', '+1 year'),
            'assignee_id' => User::factory(),
            'attachment' => $this->faker->optional(0.2)->word().'.'.$this->faker->fileExtension(),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Состояние для запланированных задач
     */
    public function planned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'planned',
            'completion_date' => null,
        ]);
    }

    /**
     * Состояние для задач в работе
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'completion_date' => null,
        ]);
    }

    /**
     * Состояние для завершенных задач
     */
    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'done',
            'completion_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Состояние для задач с вложениями
     */
    public function withAttachment(): static
    {
        return $this->state(fn (array $attributes) => [
            'attachment' => 'document_'.$this->faker->unique()->word().'.'.$this->faker->fileExtension(),
        ]);
    }

    /**
     * Состояние для задач с датой завершения
     */
    public function withCompletionDate(): static
    {
        return $this->state(fn (array $attributes) => [
            'completion_date' => $this->faker->dateTimeBetween('now', '+1 year'),
        ]);
    }

    /**
     * Состояние для просроченных задач
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'completion_date' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
            'status' => $this->faker->randomElement(['planned', 'in_progress']),
        ]);
    }

    /**
     * Состояние для задачи с определенным проектом
     */
    public function forProject(Project $project): static
    {
        return $this->state(fn (array $attributes) => [
            'project_id' => $project->id,
        ]);
    }

    /**
     * Состояние для задачи с определенным исполнителем
     */
    public function forAssignee(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'assignee_id' => $user->id,
        ]);
    }

    /**
     * Состояние для срочных задач (близкая дата завершения)
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'completion_date' => $this->faker->dateTimeBetween('now', '+3 days'),
            'status' => $this->faker->randomElement(['planned', 'in_progress']),
        ]);
    }
}