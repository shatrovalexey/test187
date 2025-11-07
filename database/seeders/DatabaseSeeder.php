<?php

namespace Database\Seeders;

use App\Models\{User, Task, Project};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        Project::factory(50)->create();
        Task::factory(1000)->create();
    }
}
