<?php


namespace Tests\Feature\Relationships;

use Illuminate\Support\Collection;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Nolanos\LaravelDoctrineFactory\DoctrineFactory;
use Workbench\App\Entities\Post;
use Workbench\App\Entities\User;

/**
 * Covers
 */
covers(DoctrineFactory::class);

/**
 * ---------------------------------------------------------------------------------
 * Persisting Entities
 * ---------------------------------------------------------------------------------
 *
 * @see https://laravel.com/docs/11.x/eloquent-factories#has-many-relationships
 */
describe('HasMany Relationships', function () {
    test("create with children", function () {
        $user = User::factory()
            ->has(Post::factory()->count(3))
            ->create();

        expect($user->getPosts())
            ->toHaveCount(3)
            ->toBeInstanceOf(Collection::class);

        EntityManager::refresh($user);
    });
})->todo(issue: 3);