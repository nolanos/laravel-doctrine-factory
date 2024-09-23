<?php


namespace Tests\Feature\Relationships;

use Nolanos\LaravelDoctrineFactory\DoctrineBelongsToRelationship;
use Nolanos\LaravelDoctrineFactory\DoctrineFactory;
use Workbench\App\Entities\Post;
use Workbench\App\Entities\User;

describe('BelongsTo Relationships', function () {
    test("the parent can be an instance", function () {
        $user = User::factory()->create();

        $post = Post::factory()
            ->for($user)
            ->make();

        expect($post->getUser())->toBe($user);
    });

    test("the parent can be a factory", function () {
        $post = Post::factory()
            ->for(User::factory())
            ->make();

        expect($post->getUser())->toBeInstanceOf(User::class);
    });

    test("the parent factory's state can be changed", function () {
        $post = Post::factory()
            ->for(User::factory()->state(['name' => 'John Doe']))
            ->make();

        expect($post->getUser()->getName())->toBe('John Doe');
    });

    test("the relationship name can be specified", function () {
        $author = User::factory()->create();
        $secondaryAuthor = User::factory()->create();

        $post = Post::factory()
            ->for($author)
            ->for($secondaryAuthor, 'secondaryAuthor')
            ->make();

        expect($post->getUser())->toEqual($author)
            ->and($post->getSecondaryAuthor())->toEqual($secondaryAuthor);
    });

    test("magic methods can infer the relationship", function () {
        $post = Post::factory()
            ->forUser()
            ->make();

        expect($post->getUser())->toBeInstanceOf(User::class);
    })->note('https://laravel.com/docs/11.x/eloquent-factories#belongs-to-relationships-using-magic-methods')
        ->done(issue: 5);

})->note(note: 'https://laravel.com/docs/11.x/eloquent-factories#belongs-to-relationships')
    ->covers(DoctrineFactory::class, DoctrineBelongsToRelationship::class)
    ->done(issue: 10);
