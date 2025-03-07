<?php

use Doctrine\ORM\EntityManagerInterface;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Workbench\App\Entities\Post;
use Workbench\App\Entities\User;

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
    // Currently works
    Post::factory()->forUser()->create();


    // TODO: Currently doesn't work
    Post::factory()->create([
        'user' => User::factory()->make(),
    ]);
});
