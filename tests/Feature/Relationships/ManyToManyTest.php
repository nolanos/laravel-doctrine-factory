<?php

namespace Tests\Feature\Relationships;

use LaravelDoctrine\ORM\Facades\EntityManager;
use Nolanos\LaravelDoctrineFactory\DoctrineFactory;
use Workbench\App\Entities\Post;
use Workbench\App\Entities\Tag;

/**
 * Covers
 */
covers(DoctrineFactory::class);

/**
 * ---------------------------------------------------------------------------------
 * ManyToMany Relationships
 * ---------------------------------------------------------------------------------
 *
 * @see https://laravel.com/docs/11.x/eloquent-factories#many-to-many-relationships
 */
describe('ManyToMany Relationships', function () {
    test("create with attached entities", function () {
        /** @var Post $post */
        $post = Post::factory()
            ->attachedTo(Tag::factory()->count(3))
            ->create();

        expect($post->getTags())->toHaveCount(3);
        EntityManager::refresh($post);

        $post->getTags()->map(function ($tag) use ($post) {
            expect($tag->getPosts())->toContain($post);
            EntityManager::refresh($tag);
        });
    });

    test("create with attached entities and specified relationship", function () {
        /** @var Post $post */
        $post = Post::factory()
            ->attachedTo(Tag::factory()->count(2), 'tags')
            ->create();

        expect($post->getTags())->toHaveCount(2);

        $post->getTags()->map(function ($tag) use ($post) {
            expect($tag->getPosts())->toContain($post);
        });
    });

    describe("magic methods", function () {
        test("with count only", function () {
            /** @var Post $post */
            $post = Post::factory()
                ->attachedToTags(4)
                ->create();

            expect($post->getTags())->toHaveCount(4);

            $post->getTags()->map(function ($tag) use ($post) {
                expect($tag->getPosts())->toContain($post);
            });
        });

        test("with attributes only", function () {
            $name = 'Laravel';
            /** @var Post $post */
            $post = Post::factory()
                ->attachedToTags(1, ['name' => $name])
                ->create();

            expect($post->getTags())->toHaveCount(1);

            $post->getTags()->map(function ($tag) use ($name, $post) {
                expect($tag->getName())->toBe($name);
                expect($tag->getPosts())->toContain($post);
            });
        });
    });

    test("bidirectional relationship is maintained", function () {
        /** @var Tag $tag */
        $tag = Tag::factory()->create();
        
        /** @var Post $post */
        $post = Post::factory()
            ->attachedTo([$tag])
            ->create();

        expect($post->getTags())->toContain($tag);
        expect($tag->getPosts())->toContain($post);
    });

})->note('https://laravel.com/docs/11.x/eloquent-factories#many-to-many-relationships');