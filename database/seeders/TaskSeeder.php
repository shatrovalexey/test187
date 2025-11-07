<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Task, Project, User};

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        Task::factory()->count(200)->create();
    }
}