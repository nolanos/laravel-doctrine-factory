<?php


namespace Tests\Feature\Relationships;

use LaravelDoctrine\ORM\Facades\EntityManager;
use Nolanos\LaravelDoctrineFactory\DoctrineBelongsToRelationship;
use Nolanos\LaravelDoctrineFactory\DoctrineFactory;
use Workbench\App\Entities\Post;
use Workbench\App\Entities\User;

describe('BelongsTo Relationships', function () {
    test("the parent can be an instance", function () {
        $user = User::factory()->make();

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

    describe("magic methods can infer the relationship", function () {
        test("calling without arguments", function () {
            $post = Post::factory()
                ->forUser()
                ->make();

            expect($post->getUser())->toBeInstanceOf(User::class);
        });

        test("calling with array attributes", function () {
            $name = "Donkey Kong";

            $post = Post::factory()->forUser(["name" => $name])->make();

            expect($post->getUser())->getName()->toEqual($name);
        });
    })->note('https://laravel.com/docs/11.x/eloquent-factories#belongs-to-relationships-using-magic-methods')
        ->done(issue: 5);

    it("creates all records", function () {
        $post = Post::factory()
            ->for(User::factory())
            ->create();

        EntityManager::refresh($post);
        EntityManager::refresh($post->getUser());

        expect(EntityManager::find(Post::class, $post->getId()))->not->toBeNull();
        expect(EntityManager::find(User::class, $post->getUser()->getId()))->not->toBeNull();
    });
})->note(note: 'https://laravel.com/docs/11.x/eloquent-factories#belongs-to-relationships')
    ->covers(DoctrineFactory::class, DoctrineBelongsToRelationship::class)
    ->done(issue: 10);
