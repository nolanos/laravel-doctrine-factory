<?php


namespace Tests\Feature\Relationships;

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
 * BelongsTo Relationships
 * ---------------------------------------------------------------------------------
 *
 * @see https://laravel.com/docs/11.x/eloquent-factories#belongs-to-relationships
 */
describe('BelongsTo Relationships', function () {
    test("create with parent", function () {
        $post = Post::factory()
            ->for(User::factory())
            ->make();

        expect($post->getUser())->toBeInstanceOf(User::class);
    });
})->todo();