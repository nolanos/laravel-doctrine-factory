<?php

use Doctrine\Common\Collections\ArrayCollection;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Workbench\App\Entities\Post;
use Workbench\App\Entities\User;

describe('BelongsTo relationships', function () {
    test('it throws exception when persisting entity with unpersisted relationships', function () {
        expect(function () {
            $user = new User('John Doe');  // Unpersisted user

            $post = new Post();
            $post->setTitle('Test Post');
            $post->setUser($user);         // Setting unpersisted relationship
            $post->setSecondaryAuthor($user); // Another unpersisted relationship

            EntityManager::persist($post);
            EntityManager::flush();
        })->toThrow(
            Doctrine\ORM\ORMInvalidArgumentException::class,
            'not configured to cascade persist'
        );
    });

    test('it does not throw exception when creating factories', function () {
        // Works
        Post::factory()->forUser()->create();

        // Works
        Post::factory()->create([
            'user' => User::factory()->make(),
        ]);

        // Doesn't work
        Post::factory()->create([]);
    });
});


describe('HasMany relationships', function () {
    test('it throws exception when persisting entity with unpersisted relationships', function () {
        expect(function () {
            $user = new User('John Doe');  // Unpersisted user

            $post = new Post();
            $post->setTitle('Test Post');
            $user->getPosts()->add($post);

            EntityManager::persist($user);
            EntityManager::flush();
        })->toThrow(
            Doctrine\ORM\ORMInvalidArgumentException::class,
            'not configured to cascade persist'
        );
    });

    test('it does not throw exception when creating factories', function () {
        // Works
        User::factory()->create([
            'children' => new ArrayCollection([User::factory()->make()]),
        ]);
    });
});
