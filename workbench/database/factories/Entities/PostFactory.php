<?php

namespace Workbench\Database\Factories\Entities;

use Nolanos\LaravelDoctrineFactory\DoctrineFactory;

use Workbench\App\Entities\User;

class PostFactory extends DoctrineFactory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'title' => fake()->name(),
            'published' => fake()->boolean(),
        ];
    }
}
