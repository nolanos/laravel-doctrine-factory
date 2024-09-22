<?php


namespace Tests\Feature\Relationships;

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
    test("make with parent", function () {
        $post = Post::factory()
            ->for(User::factory())
            ->make();

        expect($post->getUser())->toBeInstanceOf(User::class);
    });

    test("make with parent with a different relationship name", function () {
        $author = User::factory()->create();
        $secondaryAuthor = User::factory()->create();

        $post = Post::factory()
            ->for($author)
            ->for($secondaryAuthor, 'secondaryAuthor')
            ->make();

        expect($post->getSecondaryAuthor())->toEqual($secondaryAuthor);
    });

    test("pass state to parent", function () {
        $post = Post::factory()
            ->for(User::factory()->state(['name' => 'John Doe']))
            ->make();

        expect($post->getUser()->getName())->toBe('John Doe');
    });

    test("pass existing parent", function () {
        $user = User::factory()->create();
        $post = Post::factory()
            ->for($user)
            ->make();

        expect($post->getUser())->toBe($user);
    });

    /**
     * @see https://laravel.com/docs/11.x/eloquent-factories#belongs-to-relationships-using-magic-methods
     */
    test("create with parent using magic methods", function () {
        $post = Post::factory()
            ->forUser()
            ->make();

        expect($post->getUser())->toBeInstanceOf(User::class);
    })->done(issue: 5);
});