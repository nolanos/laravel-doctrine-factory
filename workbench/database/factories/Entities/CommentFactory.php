<?php

namespace Workbench\Database\Factories\Entities;

use Nolanos\LaravelDoctrineFactory\DoctrineFactory;

use Workbench\App\Entities\Comment;

class CommentFactory extends DoctrineFactory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'body' => fake()->text(),
            'user' => null,
        ];
    }
}
