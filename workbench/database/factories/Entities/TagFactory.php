<?php

namespace Workbench\Database\Factories\Entities;

use Nolanos\LaravelDoctrineFactory\DoctrineFactory;
use Workbench\App\Entities\Tag;

class TagFactory extends DoctrineFactory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
        ];
    }
}