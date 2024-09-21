<?php

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Workbench\Database\Factories\Entities\UserFactory;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // UserFactory::new(10)->create();

        UserFactory::new()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
